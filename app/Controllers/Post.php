<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\ArtikelModel; // Pastikan Anda memiliki ArtikelModel yang sudah dibuat

class Post extends ResourceController
{
    use ResponseTrait;

    // Deklarasi properti model untuk digunakan di seluruh kelas (praktik terbaik CodeIgniter)
    protected $model; 

    public function __construct()
    {
        // Inisialisasi model ArtikelModel sekali saat controller dibuat
        $this->model = new ArtikelModel();

        // --- BAGIAN PENTING UNTUK CORS (Cross-Origin Resource Sharing) ---
        // Header ini sangat penting agar browser mengizinkan permintaan dari domain/port yang berbeda
        // (misalnya, frontend Vue.js Anda yang diakses melalui http://localhost di Apache,
        // sedangkan backend CodeIgniter Anda berjalan di http://localhost:8080).

        // Mengizinkan akses dari semua origin.
        // Untuk lingkungan produksi, SANGAT DIREKOMENDASIKAN untuk mengganti '*'
        // dengan domain frontend Anda yang spesifik (misal: 'http://localhost' atau 'http://127.0.0.1:5500' jika pakai Live Server).
        header('Access-Control-Allow-Origin: *'); 
        
        // Menentukan metode HTTP yang diizinkan untuk diakses (GET, POST, PUT, DELETE, OPTIONS).
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        
        // Menentukan header request yang diizinkan untuk dikirim dari frontend.
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

        // Menangani preflight OPTIONS request:
        // Browser akan mengirim OPTIONS request terlebih dahulu (sebelum POST, PUT, DELETE)
        // untuk memeriksa apakah permintaan "aman" dan diizinkan oleh server.
        if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            // Jika browser meminta metode kontrol akses tertentu, respons dengan metode yang diizinkan.
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
            }
            // Jika browser meminta header kontrol akses tertentu, respons dengan header yang diizinkan.
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            }
            exit(0); // Hentikan eksekusi skrip PHP setelah menangani preflight request
        }
        // --- AKHIR BAGIAN PENTING UNTUK CORS ---
    }

    /**
     * Mengambil semua data artikel dari database.
     * Endpoint: GET /post
     * @return mixed Response JSON berisi daftar artikel
     */
    public function index()
    {
        // Menggunakan properti model yang sudah diinisialisasi di __construct()
        // Mengambil semua artikel dan mengurutkannya berdasarkan ID secara descending (terbaru di atas)
        $data['artikel'] = $this->model->orderBy('id', 'DESC')->findAll();
        
        // Mengembalikan data dalam format JSON menggunakan ResponseTrait
        return $this->respond($data);
    }

    /**
     * Menambahkan data artikel baru ke database.
     * Endpoint: POST /post
     * Data yang diharapkan di body (JSON dari frontend Vue.js): judul, isi, status
     * @return mixed Response JSON status penambahan
     */
    public function create()
    {
        // Mengambil data dari request body sebagai JSON (mengembalikan sebagai associative array)
        $input = $this->request->getJSON(true);
        
        // Fallback: Jika tidak ada data JSON, coba ambil dari x-www-form-urlencoded (jarang untuk Axios POST JSON)
        if (empty($input)) {
            $input = $this->request->getVar();
        }

        // Memastikan ada data yang diterima untuk diproses
        if (empty($input)) {
            return $this->failValidationError('Tidak ada data yang dikirim untuk pembuatan artikel.');
        }

        // Mempersiapkan array data untuk insert ke database
        // Menggunakan operator null coalescing (??) untuk nilai default jika key tidak ada
        $data = [
            'judul'  => $input['judul'] ?? null,
            'isi'    => $input['isi'] ?? null,
            'status' => $input['status'] ?? 0, // Pastikan 'status' juga ditangani dengan nilai default 0
        ];

        // Aturan validasi untuk proses pembuatan artikel
        $rules = [
            'judul'  => 'required|min_length[3]|max_length[255]',
            'isi'    => 'required|min_length[10]',
            'status' => 'required|integer|in_list[0,1]', // Validasi untuk memastikan status adalah 0 atau 1
        ];

        // Pesan validasi kustom untuk memberikan informasi yang lebih spesifik kepada pengguna
        $messages = [
            'judul' => [
                'required'   => 'Judul artikel tidak boleh kosong.',
                'min_length' => 'Judul minimal 3 karakter.',
                'max_length' => 'Judul maksimal 255 karakter.'
            ],
            'isi' => [
                'required'   => 'Isi artikel tidak boleh kosong.',
                'min_length' => 'Isi artikel minimal 10 karakter.'
            ],
            'status' => [
                'required'   => 'Status tidak boleh kosong.',
                'integer'    => 'Status harus berupa angka.',
                'in_list'    => 'Status harus 0 (Draft) atau 1 (Publish).'
            ]
        ];

        // Melakukan validasi data yang diterima
        if (!$this->validate($rules, $messages)) {
            // Jika validasi gagal, kembalikan respons error validasi dalam format JSON
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // Melakukan insert data ke database menggunakan properti model
        if ($this->model->insert($data)) {
            // Jika insert berhasil, siapkan respons sukses (HTTP 201 Created)
            $response = [
                'status'   => 201, // Kode status HTTP 201: Created
                'error'    => null,
                'messages' => [
                    'success' => 'Data artikel berhasil ditambahkan.'
                ],
                // Mengembalikan data artikel yang baru dibuat, termasuk ID yang di-generate otomatis
                'data'     => $this->model->find($this->model->getInsertID()) 
            ];
            return $this->respondCreated($response); // Menggunakan respondCreated untuk kode 201
        } else {
            // Jika ada masalah lain saat insert (misal error database), kembalikan error server
            return $this->failServerError('Gagal menambahkan data artikel.');
        }
    }

    /**
     * Menampilkan satu data artikel spesifik berdasarkan ID.
     * Endpoint: GET /post/{id}
     * @param int|string|null $id ID artikel yang akan ditampilkan
     * @return mixed Response JSON berisi data artikel tunggal atau Not Found
     */
    public function show($id = null)
    {
        // Menggunakan properti model untuk mencari data berdasarkan ID
        $data = $this->model->find($id); 
        
        if ($data) {
            // Jika data ditemukan, kembalikan data dalam format JSON
            return $this->respond($data);
        } else {
            // Jika data tidak ditemukan, kembalikan respons Not Found (HTTP 404)
            return $this->failNotFound('Data artikel dengan ID ' . $id . ' tidak ditemukan.');
        }
    }

    /**
     * Mengubah data artikel berdasarkan ID.
     * Endpoint: PUT /post/{id}
     * Data yang diharapkan di body (JSON dari frontend Vue.js): judul, isi, status
     * @param int|string|null $id ID artikel yang akan diubah
     * @return mixed Response JSON status pembaruan
     */
    public function update($id = null)
    {
        // Memastikan ID artikel tidak kosong di URL
        if ($id === null) {
            return $this->failValidationError('ID artikel tidak boleh kosong di URL.');
        }

        // Mengambil data dari request body.
        // getRawInput() adalah metode yang paling fleksibel untuk PUT requests.
        $input = $this->request->getRawInput(); 
        
        // Memastikan ada data yang valid yang dikirim untuk diupdate
        if (empty($input)) {
            return $this->failValidationError('Tidak ada data yang valid untuk diupdate.');
        }

        // Mempersiapkan array data yang akan diupdate.
        // Hanya sertakan kolom yang benar-benar ada di input untuk menghindari update kolom yang tidak ada.
        $dataToUpdate = [];
        if (isset($input['judul'])) {
            $dataToUpdate['judul'] = $input['judul'];
        }
        if (isset($input['isi'])) {
            $dataToUpdate['isi'] = $input['isi'];
        }
        if (isset($input['status'])) { 
            $dataToUpdate['status'] = $input['status'];
        }

        // Jika tidak ada kolom yang valid untuk diupdate, kembalikan respons info
        if (empty($dataToUpdate)) {
            return $this->respond([
                'status' => 200, // Status OK, tapi dengan pesan informasi
                'error' => null,
                'messages' => [
                    'info' => 'Tidak ada kolom yang valid untuk diupdate.'
                ]
            ]);
        }

        // Aturan validasi untuk data yang akan diupdate.
        // Aturan hanya diterapkan pada kolom yang *ada* di $dataToUpdate.
        $rules = [];
        $messages = [];

        if (isset($dataToUpdate['judul'])) {
            $rules['judul'] = 'required|min_length[3]|max_length[255]';
            $messages['judul'] = [
                'required'   => 'Judul tidak boleh kosong.',
                'min_length' => 'Judul minimal 3 karakter.',
                'max_length' => 'Judul maksimal 255 karakter.'
            ];
        }
        if (isset($dataToUpdate['isi'])) {
            $rules['isi'] = 'required|min_length[10]';
            $messages['isi'] = [
                'required'   => 'Isi artikel tidak boleh kosong.',
                'min_length' => 'Isi artikel minimal 10 karakter.'
            ];
        }
        if (isset($dataToUpdate['status'])) { 
            $rules['status'] = 'required|integer|in_list[0,1]';
            $messages['status'] = [
                'required'   => 'Status tidak boleh kosong.',
                'integer'    => 'Status harus berupa angka.',
                'in_list'    => 'Status harus 0 (Draft) atau 1 (Publish).'
            ];
        }

        // Melakukan validasi data hanya jika ada aturan yang ditetapkan
        if (!empty($rules) && !$this->validate($rules, $messages)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        // Cek apakah artikel dengan ID tersebut ada sebelum melakukan update
        $existingArtikel = $this->model->find($id);
        if (!$existingArtikel) {
            return $this->failNotFound('Data artikel dengan ID ' . $id . ' tidak ditemukan.');
        }
        
        // Melakukan update data ke database menggunakan properti model
        $updated = $this->model->update($id, $dataToUpdate);
        
        if ($updated) {
            // Jika update berhasil, siapkan respons sukses (HTTP 200 OK)
            $response = [
                'status'   => 200, // Kode status HTTP 200: OK
                'error'    => null,
                'messages' => [
                    'success' => 'Data artikel berhasil diubah.'
                ],
                'data' => $this->model->find($id) // Mengembalikan data yang sudah diupdate
            ];
            return $this->respond($response);
        } else {
            // Ini akan terjadi jika ID ditemukan dan data dikirim,
            // tetapi tidak ada perubahan karena data baru sama dengan data lama,
            // atau ada masalah lain di database.
            return $this->respond([
                'status'   => 200, // Tetap 200 karena permintaan valid, tapi info: tidak ada perubahan
                'error'    => null,
                'messages' => [
                    'info' => 'Tidak ada perubahan data yang terdeteksi atau data sama.'
                ]
            ]);
        }
    }

    /**
     * Menghapus data artikel berdasarkan ID.
     * Endpoint: DELETE /post/{id}
     * @param int|string|null $id ID artikel yang akan dihapus
     * @return mixed Response JSON status penghapusan
     */
    public function delete($id = null)
    {
        // Menggunakan properti model untuk mencari data
        
        // Cek apakah artikel dengan ID tersebut ada
        $data = $this->model->find($id);
        
        if ($data) {
            // Jika artikel ditemukan, coba hapus dari database
            if ($this->model->delete($id)) {
                // Jika penghapusan berhasil, siapkan respons sukses (HTTP 200 OK atau 204 No Content)
                $response = [
                    'status'   => 200, // Kode status HTTP 200: OK (atau 204 No Content juga umum untuk DELETE)
                    'error'    => null,
                    'messages' => [
                        'success' => 'Data artikel berhasil dihapus.'
                    ]
                ];
                // Menggunakan respondDeleted untuk respons yang sesuai setelah penghapusan
                return $this->respondDeleted($response); 
            } else {
                // Jika gagal dihapus karena alasan lain di database, kembalikan error server
                return $this->failServerError('Gagal menghapus data artikel.');
            }
        } else {
            // Jika data artikel tidak ditemukan, kembalikan respons Not Found (HTTP 404)
            return $this->failNotFound('Data artikel dengan ID ' . $id . ' tidak ditemukan.');
        }
    }
}
