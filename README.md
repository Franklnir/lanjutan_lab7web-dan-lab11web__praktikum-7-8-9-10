# lanjutan-praktikum-7-8-9-10






# PRAKTIKUM 7
### langkah 1. membuat table kategori sebagai berikut :
![image](https://github.com/user-attachments/assets/eff28be4-9011-4968-806a-da5511bb4a8a)


### langkah 2. Mengubah Tabel Artikel Tambahkan foreign key `id_kategori` pada tabel `artikel` untuk membuat relasi dengan tabel `kategori`.
![image](https://github.com/user-attachments/assets/410040a6-7b40-4848-ad6f-82d35b25ab25)

### langkah 3. Membuat Model Kategori
![image](https://github.com/user-attachments/assets/e43e27cf-0112-4607-81e1-4a9f35842e5c)

### langkah 4.memodidikasi `ArtikelModel.php`
![image](https://github.com/user-attachments/assets/85eb1765-34ed-452e-a00e-667eebef9478)

### langkah 5.Memodifikasi View index.php, admin_index.php, form_add.php, form_edit.php, 


### langkah 6.Testing Lakukan uji coba untuk memastikan semua fungsi berjalan dengan baik:
• Menampilkan daftar artikel dengan nama kategori.
![image](https://github.com/user-attachments/assets/aa591427-5817-4fce-8d0b-f7f948b71a29)

![image](https://github.com/user-attachments/assets/919f6e12-1d0c-459a-9951-436d928826a1)

• Menambah artikel baru dengan memilih kategori.
![image](https://github.com/user-attachments/assets/3e160523-2363-454c-8df4-0ef90b5f238b)
![image](https://github.com/user-attachments/assets/dac82d63-2042-4fbe-8777-5a4b7d25d652)


• Mengedit artikel dan mengubah kategorinya.
![image](https://github.com/user-attachments/assets/dac87a84-56d1-4195-ab9b-4026a61c851d)
![image](https://github.com/user-attachments/assets/91d7461c-3eae-40a3-8a97-469014a9f04d)

• Menghapus artikel.
![image](https://github.com/user-attachments/assets/9113966e-2515-4f8d-b5ce-f2f4226e998f)


### langkah 7.Pertanyaan dan Tugas
1. Selesaikan semua langkah praktikum di atas.
   sudah
3. Modifikasi tampilan detail artikel (artikel/detail.php) untuk menampilkan nama kategori
artikel.
![image](https://github.com/user-attachments/assets/5a76794e-99ae-43d0-b3dd-8d9d330febb2)

![image](https://github.com/user-attachments/assets/54cfe2df-1deb-491a-bebf-aa3f8fddfb23)

5. Tambahkan fitur untuk menampilkan daftar kategori di halaman depan (opsional).
![image](https://github.com/user-attachments/assets/51d9e891-2c92-4589-bc4f-e91160df16c3)

7. Buat fungsi untuk menampilkan artikel berdasarkan kategori tertentu (opsional).
   ![image](https://github.com/user-attachments/assets/3ba7f428-d5ee-4253-b954-150817a8e079)






# PRAKTIKUM 8


### langkah 1.instal ajax versi terbaru dan letakkan di di rektori publik/asset/js/
![image](https://github.com/user-attachments/assets/f32e9d8a-0545-4713-8a4b-c8a136ee7507)


### langkah 2.Membuat AJAX Controller (AjaxController.php)
![image](https://github.com/user-attachments/assets/4b4fbd64-1745-4c36-a288-8236acc5e829)
![image](https://github.com/user-attachments/assets/8f5c74f7-83c5-4a1c-a85d-418861a9629b)



                                      
### langkah 3.pastikan url js nya di panggil di view/ajax/index
![image](https://github.com/user-attachments/assets/4e6bf5a1-fb4c-493a-a8d1-871ba2993007)

                                      
### langkah 4.Membuat View di dalam view/ajax/idex.php
![image](https://github.com/user-attachments/assets/19ddac0c-a73b-4ae4-ae96-d9e7106bd9d6)

![image](https://github.com/user-attachments/assets/0c7d6cb0-55af-478b-87e1-4f7609be3924)

### langkah 5.testing ajax
![image](https://github.com/user-attachments/assets/d36c9d43-748e-48c7-bac2-0f104103c7ee)

data otomatis tampil di kolom data artikel secara realtime ketika di klik edit tanpa perlu refress

![image](https://github.com/user-attachments/assets/350aeb4c-5181-4cf2-9590-366ebfe19a69)


### langkah 5.saya menambahkan waktu bedasarkan data terbaru dan terbaru di update dan manampilkan data artikel paling atas yang paling terbaru
![image](https://github.com/user-attachments/assets/81388675-121c-4b05-bad3-b45eeb3bacaf)







# PRAKTIKUM 9

### langkah 1 Modifikasi Controller Artikel
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


### langkah 2. penjelasan
Penjelasan:
• `$page = $this->request->getVar('page') ?? 1;`: Mendapatkan nomor
halaman dari request. Jika tidak ada, default ke halaman 1.
• `$builder->paginate(10, 'default', $page);`: Menerapkan pagination
dengan nomor halaman yang diberikan.
• `$this->request->isAJAX()`: Memeriksa apakah request yang datang adalah
AJAX.
• Jika AJAX, kembalikan data artikel dan pager dalam format JSON.
• Jika bukan AJAX, tampilkan view seperti biasa.



### langkah 3. Modifikasi View (admin_index.php)
* Ubah view `admin_index.php` untuk menggunakan jQuery.
* Hapus kode yang menampilkan tabel artikel dan pagination secara langsung.
* Tambahkan elemen untuk menampilkan data artikel dan pagination dari AJAX.
* Tambahkan kode jQuery untuk melakukan request AJAX.

                              <?= $this->include('template/admin_header'); ?>
                              <h2><?= $title; ?></h2>
                              <div class="row mb-3">
                                  <div class="col-md-6">
                                      <form id="search-form" class="form-inline">
                                          <input type="text" name="q" id="search-box" value="<?= $q; ?>" placeholder="Cari judul artikel" class="form-control mr-2">
                                          <select name="kategori_id" id="category-filter" class="form-control mr-2">
                                              <option value="">Semua Kategori</option>
                                              <?php foreach ($kategori as $k): ?>
                                                  <option value="<?= $k['id_kategori']; ?>" <?= ($kategori_id == $k['id_kategori']) ? 'selected' : ''; ?>>
                                                      <?= $k['nama_kategori']; ?>
                                                  </option>
                                              <?php endforeach; ?>
                                          </select>
                                          <input type="submit" value="Cari" class="btn btn-primary">
                                      </form>
                                  </div>
                              </div>
                              <!-- Indikator loading -->
                              <div id="loading"><span class="loader"></span> Memuat data...</div>
                              <!-- Sort -->
                              <div class="mb-3">
                                  <label>Urutkan: </label>
                                  <select id="sort-select" class="form-control d-inline-block w-auto ml-2">
                                      <option value="artikel.id|desc">Terbaru</option>
                                      <option value="artikel.judul|asc">Judul A-Z</option>
                                      <option value="artikel.judul|desc">Judul Z-A</option>
                                  </select>
                              </div>
                              <!-- Tempat artikel dan pagination -->
                              <div id="article-container"></div>
                              <div id="pagination-container"></div>
                              
                              <!-- Gaya Loading -->
                              <style>
                              #loading {
                                  display: none;
                                  font-weight: bold;
                                  color: #007bff;
                                  margin-bottom: 10px;
                              }
                              .loader {
                                  border: 4px solid #f3f3f3;
                                  border-top: 4px solid #007bff;
                                  border-radius: 50%;
                                  width: 20px;
                                  height: 20px;
                                  animation: spin 1s linear infinite;
                                  display: inline-block;
                                  vertical-align: middle;
                                  margin-right: 8px;
                              }
                              @keyframes spin {
                                  0% { transform: rotate(0deg); }
                                  100% { transform: rotate(360deg); }
                              }
                              </style>
                              <!-- jQuery -->
                              <script src="<?= base_url('assets/jquery-3.7.1.min.js') ?>"></script>
                              <script>
                              $(document).ready(function() {
                                  const articleContainer = $('#article-container');
                                  const paginationContainer = $('#pagination-container');
                                  const searchForm = $('#search-form');
                                  const searchBox = $('#search-box');
                                  const categoryFilter = $('#category-filter');
                                  const sortSelect = $('#sort-select');
                                  const loading = $('#loading');
                                  let currentSort = 'artikel.id';
                                  let currentOrder = 'desc';
                                  const fetchData = (url) => {
                                      loading.show();
                                      $.ajax({
                                          url: url,
                                          type: 'GET',
                                          dataType: 'json',
                                          headers: {
                                              'X-Requested-With': 'XMLHttpRequest'
                                          },
                                          success: function(data) {
                                              renderArticles(data.artikel);
                                              renderPagination(data.pager, data.q, data.kategori_id);
                                          },
                                          complete: function() {
                                              loading.hide();
                                          }
                                      });
                                  };
                                  const renderArticles = (articles) => {
                                      let html = '<table class="table table-bordered">';
                                      html += '<thead><tr><th>ID</th><th>Judul</th><th>Kategori</th><th>Status</th><th>Aksi</th></tr></thead><tbody>';
                                      if (articles.length > 0) {
                                          articles.forEach(article => {
                                              html += `
                                              <tr>
                                                  <td>${article.id}</td>
                                                  <td><b>${article.judul}</b><p><small>${article.isi.substring(0, 50)}...</small></p></td>
                                                  <td>${article.nama_kategori}</td>
                                                  <td>${article.status}</td>
                                                  <td>
                                                      <a class="btn btn-sm btn-info" href="/admin/artikel/edit/${article.id}">Ubah</a>
                                                      <a class="btn btn-sm btn-danger" onclick="return confirm('Yakin menghapus data?');" href="/admin/artikel/delete/${article.id}">Hapus</a>
                                                  </td>
                                              </tr>`;
                                          });
                                      } else {
                                          html += '<tr><td colspan="5">Tidak ada data.</td></tr>';
                                      }
                                      html += '</tbody></table>';
                                      articleContainer.html(html);
                                  };
                                  const renderPagination = (pager, q, kategori_id) => {
                                      let html = '<nav><ul class="pagination">';
                                      pager.links.forEach(link => {
                                          let url = link.url ? `${link.url}&q=${q}&kategori_id=${kategori_id}&sort=${currentSort}&order=${currentOrder}` : '#';
                                          html += `
                                              <li class="page-item ${link.active ? 'active' : ''}">
                                                  <a class="page-link" href="${url}">${link.title}</a>
                                              </li>`;
                                      });
                                      html += '</ul></nav>';
                                      paginationContainer.html(html);
                                  };
                                  searchForm.on('submit', function(e) {
                                      e.preventDefault();
                                      const q = searchBox.val();
                                      const kategori_id = categoryFilter.val();
                                      fetchData(`/admin/artikel?q=${q}&kategori_id=${kategori_id}&sort=${currentSort}&order=${currentOrder}`);
                                  });
                                  categoryFilter.on('change', function() {
                                      searchForm.trigger('submit');
                                  });
                                  sortSelect.on('change', function() {
                                      const val = $(this).val().split('|');
                                      currentSort = val[0];
                                      currentOrder = val[1];
                                      searchForm.trigger('submit');
                                  });
                                  $(document).on('click', '.pagination a', function(e) {
                                      e.preventDefault();
                                      const url = $(this).attr('href');
                                      if (url && url !== '#') {
                                          fetchData(url);
                                      }
                                  });
                                  // Initial load
                                  fetchData(`/admin/artikel?sort=${currentSort}&order=${currentOrder}`);
                              });
                              </script>
                              <?= $this->include('template/admin_footer'); ?>

### langkah 4.Pertanyaan dan Tugas
1. Selesaikan semua langkah praktikum di atas.
2. Modifikasi tampilan data artikel dan pagination sesuai kebutuhan desain.
3. ![image](https://github.com/user-attachments/assets/cd1eb8be-5018-4e47-a4f0-513abfc89125)

4. Tambahkan indikator loading saat data sedang diambil dari server.
   ![image](https://github.com/user-attachments/assets/3bf3b9d3-2296-41ab-bc0a-2016e900bbf4)

6. Implementasikan fitur sorting (mengurutkan artikel berdasarkan judul, dll.) dengan AJAX.
   ![image](https://github.com/user-attachments/assets/fbe45fec-72f7-4c55-b981-11c713a95cad)
   ![image](https://github.com/user-attachments/assets/9164ef79-9b4f-4705-ad52-ad4450a4875c)
   ![image](https://github.com/user-attachments/assets/c459595e-6d95-4768-8d59-5c94fa0e4366)

   ![image](https://github.com/user-attachments/assets/f1678480-03fe-4d51-a84d-8bf9ad32bf6a)



# PARKTIKUM 10

### langkah 1 Membuat Model Manfaatkan model yang sudah ada (misalnya ArtikelModel) agar dapat diakses melalui API.
Membuat REST Controller
Buat file controller baru, misalnya Post.php, di dalam direktori app\Controllers
 File ini akan berisi fungsi-fungsi untuk:
index(): Menampilkan semua data dari database.
create(): Menambahkan data baru ke database.
show($id): Menampilkan data spesifik berdasarkan ID.
update($id): Mengubah data yang ada di database.
delete($id): Menghapus data dari database.

                           <?php
                           namespace App\Controllers;
                           use CodeIgniter\RESTful\ResourceController;
                           use CodeIgniter\API\ResponseTrait;
                           use App\Models\ArtikelModel;
                           
                           class Post extends ResourceController
                           {
                               use ResponseTrait;
                               // all users
                               public function index()
                               {
                                   $model = new ArtikelModel();
                                   $data['artikel'] = $model->orderBy('id', 'DESC')->findAll();
                                   return $this->respond($data);
                               }
                               // create
                               public function create()
                               {
                                   $model = new ArtikelModel();
                                   $data = [
                                       'judul' => $this->request->getVar('judul'),
                                       'isi' => $this->request->getVar('isi'),
                                   ];
                                   $model->insert($data);
                                   $response = [
                                       'status' => 201,
                                       'error' => null,
                                       'messages' => [
                                           'success' => 'Data artikel berhasil ditambahkan.'
                                       ]
                                   ];
                                   return $this->respondCreated($response);
                               }
                               // single user
                               public function show($id = null)
                               {
                                   $model = new ArtikelModel();
                                   $data = $model->where('id', $id)->first();
                                   if ($data) {
                                       return $this->respond($data);
                                   } else {
                                       return $this->failNotFound('Data tidak ditemukan.');
                                   }
                               }
                               // update
                               public function update($id = null)
                               {
                                   $model = new ArtikelModel();
                                   $id = $this->request->getVar('id');
                                   $data = [
                                       'judul' => $this->request->getVar('judul'),
                                       'isi' => $this->request->getVar('isi'),
                                   ];
                                   $model->update($id, $data);
                                   $response = [
                                       'status' => 200,
                                       'error' => null,
                                       'messages' => [
                                           'success' => 'Data artikel berhasil diubah.'
                                       ]
                                   ];
                                   return $this->respond($response);
                               }
                               // delete
                               public function delete($id = null)
                               {
                                   $model = new ArtikelModel();
                                   $data = $model->where('id', $id)->delete($id);
                                   if ($data) {
                                       $model->delete($id); // This line is redundant if $data is already true from previous delete call.
                                       $response = [
                                           'status' => 200,
                                           'error' => null,
                                           'messages' => [
                                               'success' => 'Data artikel berhasil dihapus.'
                                           ]
                                       ];
                                       return $this->respondDeleted($response);
                                   } else {
                                       return $this->failNotFound('Data tidak ditemukan.');
                                   }
                               }
                           }


### langkah 2 Membuat Routing REST API di app/Config/Routes.php
                        $routes->resource('post');

Untuk melihat daftar route yang dihasilkan
                        php spark routes

![image](https://github.com/user-attachments/assets/47fc22b1-dbfa-436b-af87-ffbb87646d54)


### langkah 3 testing Menguji REST API CodeIgniter dengan Postman atau uji di extension vscode dengan nama thunder client
# menmapilkan semua data dengan GET http://localhost:8080/post
![image](https://github.com/user-attachments/assets/7dc61186-6a94-47a9-b686-e9d7d2de9c07)

# menampilkan data spesifik (GET dengan ID) http://localhost:8080/post/25
![image](https://github.com/user-attachments/assets/963e9d58-f5ca-4c56-bd35-c1ced647d703)

# Mengubah Data (PUT) http://localhost:8080/post/37
![image](https://github.com/user-attachments/assets/5465a8d3-4156-4248-b60b-7effe12e1630)

# Menambahkan Data (POST) http://localhost:8080/post
![image](https://github.com/user-attachments/assets/e78b8520-d174-4fff-947c-5d232e1fe81e)

# Menghapus Data (DELETE) http://localhost:8080/post/25
![image](https://github.com/user-attachments/assets/e396ee1d-09ca-4acc-a5e2-8cc8e4e314db)











                                                                                                   
>>>>>>> 8e32abc33804f269f1b351231cd31039531283ea
