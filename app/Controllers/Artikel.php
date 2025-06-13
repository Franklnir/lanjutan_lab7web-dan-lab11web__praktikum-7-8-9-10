<?php 

namespace App\Controllers;

use App\Models\ArtikelModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\KategoriModel;


class Artikel extends BaseController
{








private function checkLogin()
{
    $session = session();
    if (!$session->get('isLoggedIn')) {
        return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu')->send();
        exit;
    }
}


    
public function index()
{
     $this->checkLogin();
    $artikelModel = new \App\Models\ArtikelModel();
    $kategoriModel = new \App\Models\KategoriModel();

    $q = $this->request->getGet('q');
    $id_kategori = $this->request->getGet('id_kategori');

    // Dapatkan query builder dari model
    $builder = $artikelModel->builder();

    if ($q) {
        $builder->groupStart()
                ->like('judul', $q)
                ->orLike('isi', $q)
                ->groupEnd();
    }

    if ($id_kategori) {
        $builder->where('id_kategori', $id_kategori);
    }

    // Ambil data artikel berdasarkan query builder
    $artikel = $builder->get()->getResultArray();

    // Ambil semua kategori
    $kategori = $kategoriModel->asArray()->findAll();

    $data = [
        'artikel' => $artikel,
        'kategori' => $kategori,
        'q' => $q,
        'id_kategori' => $id_kategori,
        'title' => 'Daftar Artikel',
    ];

    return view('artikel/index', $data);
}











public function cari()
{
    $artikelModel = new ArtikelModel();
    $kategoriModel = new KategoriModel();

    // Mendapatkan parameter pencarian
    $keyword = $this->request->getGet('keyword');
    $id_kategori = $this->request->getGet('id_kategori');
    
    // Membangun query artikel
    $artikelQuery = $artikelModel->select('artikel.*, kategori.nama_kategori')
                                 ->join('kategori', 'kategori.id_kategori = artikel.id_kategori', 'left');
    
    // Filter berdasarkan kategori jika ada
    if ($id_kategori) {
        $artikelQuery->where('artikel.id_kategori', $id_kategori);
    }

    // Filter berdasarkan kata kunci jika ada
    if ($keyword) {
        $artikelQuery->like('artikel.judul', $keyword)->orLike('artikel.isi', $keyword);
    }

    // Ambil data artikel yang sesuai
    $data['artikel'] = $artikelQuery->findAll();
    $data['kategori'] = $kategoriModel->findAll(); // Kirimkan data kategori ke view

    return view('layout/main', $data);  // Menampilkan hasil pencarian di halaman yang sesuai
}




    



public function add()
    {
        // Ambil semua kategori untuk ditampilkan di form
        $kategoriModel = new KategoriModel();  // Inisialisasi KategoriModel
        $data['kategori'] = $kategoriModel->findAll();  // Ambil semua kategori

        // Judul halaman
        $data['title'] = 'Tambah Artikel';

        return view('artikel/create', $data); // Tampilkan form tambah artikel
    }


   public function store()
{
    // Validasi input
    if (!$this->validate([
        'judul' => 'required|min_length[3]|max_length[255]',
        'isi' => 'required',
        'id_kategori' => 'required|is_natural_no_zero', // Validasi kategori
        'gambar' => 'uploaded[gambar]|is_image[gambar]|mime_in[gambar,image/jpg,image/jpeg,image/png]|max_size[gambar,2048]' // Validasi gambar
    ])) {
        // Jika validasi gagal, tampilkan kembali form dengan pesan error
        return redirect()->to('/admin/artikel/create')->withInput()->with('validation', $this->validator);
    }

    // Ambil data dari form
    $artikelModel = new ArtikelModel();
    $data = [
        'judul' => $this->request->getPost('judul'),
        'isi' => $this->request->getPost('isi'),
        'id_kategori' => $this->request->getPost('id_kategori'),
        'slug' => url_title($this->request->getPost('judul'), '-', true),
        'status' => 1, // Status aktif
    ];

    // Menangani file gambar
$file = $this->request->getFile('gambar');
if ($file->isValid() && !$file->hasMoved()) {
    $gambarName = $file->getRandomName();
    // Pastikan file terupload
    if ($file->move(ROOTPATH . 'public/uploads', $gambarName)) {
        log_message('info', 'File gambar berhasil di-upload: ' . $gambarName);
        $data['gambar'] = $gambarName;
    } else {
        log_message('error', 'Gagal memindahkan file gambar.');
    }
} else {
    log_message('error', 'File tidak valid atau telah dipindahkan sebelumnya.');
}

    // Simpan artikel baru ke database
    $artikelModel->save($data);

    // Redirect setelah berhasil menyimpan artikel
    return redirect()->to('/admin/artikel');
}

















   public function view($slug)
{
     $this->checkLogin();
    $model = new ArtikelModel();

    // Tambahkan join ke tabel kategori agar bisa ambil nama_kategori
    $artikel = $model
        ->select('artikel.*, kategori.nama_kategori')
        ->join('kategori', 'kategori.id_kategori = artikel.id_kategori', 'left')
        ->where('slug', $slug)
        ->first();

    if (!$artikel) {
        throw PageNotFoundException::forPageNotFound();
    }

    $title = $artikel['judul'];
    return view('artikel/detail', compact('artikel', 'title'));
}



    
public function admin_index()
{
    $title = 'Daftar Artikel (Admin)';
    $model = new ArtikelModel();
    $q = $this->request->getVar('q') ?? '';
    $kategori_id = $this->request->getVar('kategori_id') ?? '';
    $sort = $this->request->getVar('sort') ?? 'artikel.id';
    $order = $this->request->getVar('order') ?? 'desc';
    $page = $this->request->getVar('page') ?? 1;

    $builder = $model->table('artikel')
        ->select('artikel.*, kategori.nama_kategori')
        ->join('kategori', 'kategori.id_kategori = artikel.id_kategori');

    if ($q != '') {
        $builder->like('artikel.judul', $q);
    }

    if ($kategori_id != '') {
        $builder->where('artikel.id_kategori', $kategori_id);
    }

    $builder->orderBy($sort, $order);

    $artikel = $builder->paginate(10, 'default', $page);
    $pager = $model->pager;

    $data = [
        'title' => $title,
        'q' => $q,
        'kategori_id' => $kategori_id,
        'artikel' => $artikel,
        'pager' => $pager
    ];

    if ($this->request->isAJAX()) {
        return $this->response->setJSON($data);
    } else {
        $kategoriModel = new KategoriModel();
        $data['kategori'] = $kategoriModel->findAll();
        return view('artikel/admin_index', $data);
    }
}


    
    public function about()
    {
        return view('artikel/about', [
            "conten" => "Ini adalah halaman yang menjelaskan tentang informasi dan tujuan dari situs ini.",
            "title" => "Tentang Kami"
        ]);
    }







    


 public function detail($slug)
{
    $artikelModel = new \App\Models\ArtikelModel();

    // Join tabel kategori untuk ambil nama kategori
    $artikel = $artikelModel
        ->select('artikel.*, kategori.nama_kategori')
        ->join('kategori', 'kategori.id_kategori = artikel.id_kategori', 'left')
        ->where('slug', $slug)
        ->first();

    if (!$artikel) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Artikel tidak ditemukan');
    }

    $data = [
        'artikel' => $artikel,
        'title' => $artikel['judul']
    ];

    return view('artikel/detail', $data);
}








    

    // Fungsi untuk menambahkan artikel
  public function create()
{
    // Validasi data artikel
    $validation = \Config\Services::validation();
    $validation->setRules([
        'judul' => 'required|min_length[3]|max_length[255]',
        'isi' => 'required',
        'id_kategori' => 'required|is_natural_no_zero',  // Validasi kategori
    ]);

    // Menjalankan validasi
    $isDataValid = $validation->withRequest($this->request)->run();

    if ($isDataValid) {
        // Menangani upload gambar
        $file = $this->request->getFile('gambar');

        if ($file->isValid() && !$file->hasMoved()) {
            // Memindahkan gambar ke folder yang sesuai
            $file->move(ROOTPATH . 'public/gambar');
        }

        // Menyimpan data artikel
        $artikel = new ArtikelModel();
        $artikel->insert([
            'judul' => $this->request->getPost('judul'),
            'isi' => $this->request->getPost('isi'),
            'slug' => url_title($this->request->getPost('judul')),
            'id_kategori' => $this->request->getPost('id_kategori'),  // Menyimpan kategori yang dipilih
            'gambar' => $file->getName(),  // Menyimpan nama file gambar
        ]);

        // Redirect ke daftar artikel setelah berhasil
        return redirect()->to('/admin/artikel');
    }

    // Jika validasi gagal, ambil kategori untuk dropdown
    $kategoriModel = new KategoriModel();
    $kategori = $kategoriModel->findAll();

    // Judul halaman
    $title = "Tambah Artikel";

    // Tampilkan form dengan data kategori
    return view('artikel/create', compact('title', 'kategori'));
}






    public function edit($id)
{
    $artikel = new ArtikelModel();
    $kategoriModel = new KategoriModel();

    // Ambil artikel berdasarkan ID
    $data = $artikel->where('id', $id)->first();

    // Cek jika data artikel tidak ditemukan
    if (!$data) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();  // Jika artikel tidak ditemukan
    }

    // Ambil semua kategori untuk dropdown
    $kategori = $kategoriModel->findAll();

    // Validasi input form
    $validation = \Config\Services::validation();
    $validation->setRules([
        'judul' => 'required',
        'isi'   => 'required',
        'id_kategori' => 'required|is_not_unique[kategori.id_kategori]'  // Pastikan kategori dipilih dengan benar
    ]);

    // Jika validasi berhasil, update artikel
    if ($validation->withRequest($this->request)->run()) {
        // Update data artikel dengan kategori yang dipilih
        $artikel->update($id, [
            'judul' => $this->request->getPost('judul'),
            'isi'   => $this->request->getPost('isi'),
            'id_kategori' => $this->request->getPost('id_kategori'),  // Menambahkan kategori yang dipilih
        ]);

        return redirect()->to('/admin/artikel');  // Redirect ke daftar artikel
    }

    // Judul halaman
    $title = "Edit Artikel";

    // Kirim data artikel dan kategori ke form
    return view('artikel/form_edit', compact('title', 'data', 'kategori'));
}



    public function update($id)
{
    $artikelModel = new ArtikelModel();
    
    // Validasi form
    if (!$this->validate([
        'judul' => 'required|min_length[3]',
        'isi' => 'required|min_length[10]',
        'id_kategori' => 'required'
    ])) {
        return redirect()->back()->withInput()->with('validation', \Config\Services::validation());
    }

    // Update artikel
    $artikelModel->update($id, [
        'judul' => $this->request->getVar('judul'),
        'isi' => $this->request->getVar('isi'),
        'id_kategori' => $this->request->getVar('id_kategori'),
        'slug' => url_title($this->request->getVar('judul'), '-', true)
    ]);
    
    return redirect()->to('/artikel')->with('message', 'Artikel berhasil diubah!');
}











    public function delete($id)
{
    $artikel = new \App\Models\ArtikelModel();

    // Periksa apakah data ada sebelum dihapus
    $data = $artikel->find($id);
    if (!$data) {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Artikel dengan ID $id tidak ditemukan.");
    }

    // Hapus data
    $artikel->delete($id);

    // Redirect ke halaman admin/artikel
    return redirect()->to('/admin/artikel');
}




}    