<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\Request;
use CodeIgniter\HTTP\Response;
use App\Models\ArtikelModel;
use App\Models\KategoriModel; // Pastikan ini di-import
use CodeIgniter\Files\File;

class AjaxController extends Controller
{
    /**
     * Menampilkan halaman utama untuk manajemen artikel (view: ajax/index).
     */
    public function index()
    {
        return view('ajax/index');
    }

    /**
     * Mengambil semua data artikel beserta nama kategori, diurutkan dari yang terbaru.
     */
    public function getData()
    {
        $artikelModel = new ArtikelModel();
        // Menggunakan join untuk mengambil nama_kategori dari tabel kategori
        $data = $artikelModel
                    ->select('artikel.*, kategori.nama_kategori')
                    ->join('kategori', 'kategori.id_kategori = artikel.id_kategori', 'left')
                    ->orderBy('artikel.id', 'DESC') // Memastikan data terbaru di paling atas
                    ->findAll();

        // --- TAMBAH BAGIAN INI untuk memformat waktu ---
        foreach ($data as &$row) {
            if (isset($row['created_at'])) {
                $row['created_at_formatted'] = date('d-m-Y H:i:s', strtotime($row['created_at']));
            }
            if (isset($row['updated_at'])) {
                $row['updated_at_formatted'] = date('d-m-Y H:i:s', strtotime($row['updated_at']));
            }
        }
        // -----------------------------------------------

        return $this->response->setJSON($data);
    }

    /**
     * Mengambil semua data kategori.
     */
    public function getCategories()
    {
        $model = new KategoriModel();
        $data = $model->findAll();
        return $this->response->setJSON($data);
    }

    /**
     * Membuat artikel baru, termasuk unggah gambar.
     */
    public function create()
    {
        $artikelModel = new ArtikelModel();

        $rules = [
            'judul'       => 'required|min_length[3]|max_length[255]',
            'isi'         => 'required',
            'id_kategori' => 'required|integer',
            'status'      => 'required',
            'gambar'      => 'uploaded[gambar]|max_size[gambar,1024]|is_image[gambar]|mime_in[gambar,image/jpg,image/jpeg,image/png,image/gif]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error', 'message' => $this->validator->getErrors()]);
        }

        $gambarName = null;
        $file = $this->request->getFile('gambar');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $gambarName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/gambar', $gambarName);
        }

        $data = [
            'judul'       => $this->request->getPost('judul'),
            'isi'         => $this->request->getPost('isi'),
            'id_kategori' => $this->request->getPost('id_kategori'),
            'status'      => $this->request->getPost('status'),
            'gambar'      => $gambarName
        ];

        // Model akan secara otomatis mengisi created_at dan updated_at
        if ($artikelModel->save($data)) {
            return $this->response->setJSON(['status' => 'OK', 'message' => 'Artikel berhasil ditambahkan']);
        } else {
            // Jika gagal simpan, hapus gambar yang sudah diunggah
            if ($gambarName && file_exists(ROOTPATH . 'public/gambar/' . $gambarName)) {
                unlink(ROOTPATH . 'public/gambar/' . $gambarName);
            }
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menambahkan artikel']);
        }
    }

    /**
     * Memperbarui artikel yang sudah ada, termasuk mengganti gambar.
     * @param int $id ID artikel yang akan diperbarui.
     */
    public function update($id = null)
    {
        $artikelModel = new ArtikelModel();
        $artikel = $artikelModel->find($id);

        if (!$artikel) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Artikel tidak ditemukan.']);
        }

        $rules = [
            'judul'       => 'required|min_length[3]|max_length[255]',
            'isi'         => 'required',
            'id_kategori' => 'required|integer',
            'status'      => 'required'
        ];

        $file = $this->request->getFile('gambar');
        // Hanya tambahkan aturan validasi gambar jika ada file yang diunggah
        if ($file && $file->isValid()) {
            $rules['gambar'] = 'uploaded[gambar]|max_size[gambar,1024]|is_image[gambar]|mime_in[gambar,image/jpg,image/jpeg,image/png,image/gif]';
        }

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error', 'message' => $this->validator->getErrors()]);
        }

        $gambarName = $artikel['gambar']; // Ambil nama gambar lama dari database

        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Hapus gambar lama jika ada
            if ($artikel['gambar'] && file_exists(ROOTPATH . 'public/gambar/' . $artikel['gambar'])) {
                unlink(ROOTPATH . 'public/gambar/' . $artikel['gambar']);
            }
            $gambarName = $file->getRandomName(); // Buat nama unik untuk gambar baru
            $file->move(ROOTPATH . 'public/gambar', $gambarName); // Pindahkan gambar baru
        }

        $data = [
            'judul'       => $this->request->getPost('judul'),
            'isi'         => $this->request->getPost('isi'),
            'id_kategori' => $this->request->getPost('id_kategori'),
            'status'      => $this->request->getPost('status'),
            'gambar'      => $gambarName // Simpan nama file yang baru atau yang lama (jika tidak ada perubahan)
        ];

        // Model akan secara otomatis mengisi updated_at
        if ($artikelModel->update($id, $data)) {
            return $this->response->setJSON(['status' => 'OK', 'message' => 'Artikel berhasil diubah']);
        } else {
            // Jika gagal update, dan ada gambar baru yang sempat diunggah, hapus gambar baru tersebut
            if ($file && $file->isValid() && !$file->hasMoved() && $gambarName !== $artikel['gambar']) {
                unlink(ROOTPATH . 'public/gambar/' . $gambarName);
            }
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal mengubah artikel']);
        }
    }

    /**
     * Menghapus artikel beserta file gambarnya.
     * @param int $id ID artikel yang akan dihapus.
     */
    public function delete($id)
    {
        $artikelModel = new ArtikelModel();
        $artikel = $artikelModel->find($id);

        if ($artikel) {
            // Hapus file gambar terkait jika ada
            if ($artikel['gambar'] && file_exists(ROOTPATH . 'public/gambar/' . $artikel['gambar'])) {
                unlink(ROOTPATH . 'public/gambar/' . $artikel['gambar']);
            }

            if ($artikelModel->delete($id)) {
                return $this->response->setJSON(['status' => 'OK', 'message' => 'Artikel berhasil dihapus']);
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menghapus artikel dari database.']);
            }
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Artikel tidak ditemukan.']);
        }
    }

    /**
     * Menghapus gambar dari artikel tertentu tanpa menghapus artikelnya.
     * @param int $id ID artikel yang gambarnya akan dihapus.
     */
    public function removeImage($id)
    {
        $artikelModel = new ArtikelModel();
        $artikel = $artikelModel->find($id);

        if (!$artikel) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Artikel tidak ditemukan.']);
        }

        if ($artikel['gambar']) {
            $filePath = ROOTPATH . 'public/gambar/' . $artikel['gambar'];
            if (file_exists($filePath)) {
                unlink($filePath); // Hapus file dari server
            }

            // Set kolom gambar di database menjadi NULL
            if ($artikelModel->update($id, ['gambar' => NULL])) {
                return $this->response->setJSON(['status' => 'OK', 'message' => 'Gambar berhasil dihapus.']);
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menghapus nama gambar dari database.']);
            }
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Artikel ini tidak memiliki gambar.']);
        }
    }
}