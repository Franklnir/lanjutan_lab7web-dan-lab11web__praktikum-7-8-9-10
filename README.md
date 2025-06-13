# lanjutan-praktikum-7-8-9

# PRAKTIKUM 7
### membuat table kategori sebagai berikut :
![image](https://github.com/user-attachments/assets/eff28be4-9011-4968-806a-da5511bb4a8a)


### Mengubah Tabel Artikel Tambahkan foreign key `id_kategori` pada tabel `artikel` untuk membuat relasi dengan tabel `kategori`.
![image](https://github.com/user-attachments/assets/410040a6-7b40-4848-ad6f-82d35b25ab25)

### Membuat Model Kategori
![image](https://github.com/user-attachments/assets/e43e27cf-0112-4607-81e1-4a9f35842e5c)

### memodidikasi `ArtikelModel.php`
![image](https://github.com/user-attachments/assets/85eb1765-34ed-452e-a00e-667eebef9478)

### Memodifikasi View index.php, admin_index.php, form_add.php, form_edit.php, 


### Testing
Lakukan uji coba untuk memastikan semua fungsi berjalan dengan baik:
• Menampilkan daftar artikel dengan nama kategori.
![image](https://github.com/user-attachments/assets/919f6e12-1d0c-459a-9951-436d928826a1)

• Menambah artikel baru dengan memilih kategori.
![image](https://github.com/user-attachments/assets/3e160523-2363-454c-8df4-0ef90b5f238b)
![image](https://github.com/user-attachments/assets/dac82d63-2042-4fbe-8777-5a4b7d25d652)


• Mengedit artikel dan mengubah kategorinya.
![image](https://github.com/user-attachments/assets/dac87a84-56d1-4195-ab9b-4026a61c851d)
![image](https://github.com/user-attachments/assets/91d7461c-3eae-40a3-8a97-469014a9f04d)

• Menghapus artikel.
![image](https://github.com/user-attachments/assets/9113966e-2515-4f8d-b5ce-f2f4226e998f)


### Pertanyaan dan Tugas
1. Selesaikan semua langkah praktikum di atas.
   sudah
3. Modifikasi tampilan detail artikel (artikel/detail.php) untuk menampilkan nama kategori
artikel.
![image](https://github.com/user-attachments/assets/54cfe2df-1deb-491a-bebf-aa3f8fddfb23)

5. Tambahkan fitur untuk menampilkan daftar kategori di halaman depan (opsional).
![image](https://github.com/user-attachments/assets/51d9e891-2c92-4589-bc4f-e91160df16c3)

7. Buat fungsi untuk menampilkan artikel berdasarkan kategori tertentu (opsional).
   ![image](https://github.com/user-attachments/assets/3ba7f428-d5ee-4253-b954-150817a8e079)






# PRAKTIKUM 8

### Membuat AJAX Controller (AjaxController.php)
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

### Membuat View 
![image](https://github.com/user-attachments/assets/42fede8d-45a5-402e-b544-c3c18051f028)
                                                                                                            <?= $this->include('template/header'); ?>
                                                                                                            <style>
                                                                                                                /* Custom CSS for Grid Table Look */
                                                                                                                .grid-table {
                                                                                                                    width: 100%;
                                                                                                                    border-collapse: collapse; /* Collapse borders to create grid lines */
                                                                                                                    margin-top: 20px; /* Adjust as needed */
                                                                                                                    font-family: Arial, sans-serif; /* A common, readable font */
                                                                                                                    color: #333; /* Darker text for readability */
                                                                                                                }
                                                                                                                .grid-table th,
                                                                                                                .grid-table td {
                                                                                                                    border: 1px solid #ddd; /* Light grey border for grid lines */
                                                                                                                    padding: 10px 12px; /* Slightly more padding for better spacing */
                                                                                                                    text-align: left; /* Align text to the left */
                                                                                                                    vertical-align: top; /* Align content to the top for multi-line cells */
                                                                                                                }
                                                                                                            
                                                                                                                .grid-table th {
                                                                                                                    background-color: #f2f2f2; /* Light grey background for header */
                                                                                                                    font-weight: bold;
                                                                                                                    color: #555; /* Slightly darker header text */
                                                                                                                    text-transform: uppercase; /* Uppercase header text */
                                                                                                                    font-size: 0.9em; /* Slightly smaller font for headers */
                                                                                                                }
                                                                                                            
                                                                                                                /* Optional: Hover effect for rows */
                                                                                                                .grid-table tbody tr:hover {
                                                                                                                    background-color: #e9e9e9; /* Slightly darker hover color */
                                                                                                                }
                                                                                                            
                                                                                                                /* Adjust image size within the table cell if needed */
                                                                                                                .grid-table td img {
                                                                                                                    max-width: 60px; /* Smaller image in table cell */
                                                                                                                    height: auto;
                                                                                                                    display: block; /* Remove extra space below image */
                                                                                                                    margin: 0 auto; /* Center image */
                                                                                                                    border-radius: 4px; /* Slightly rounded corners for images */
                                                                                                                    box-shadow: 0 0 3px rgba(0,0,0,0.1); /* Subtle shadow for images */
                                                                                                                }
                                                                                                                /* Style for buttons within the table */
                                                                                                                .grid-table td .btn {
                                                                                                                    margin-right: 5px; /* Spacing between buttons */
                                                                                                                    margin-bottom: 5px; /* Spacing if buttons wrap */
                                                                                                                    padding: 6px 10px; /* Adjust button padding */
                                                                                                                    font-size: 0.85em; /* Smaller font for table buttons */
                                                                                                                    border-radius: 4px; /* Slightly rounded button corners */
                                                                                                                }
                                                                                                                /* General page styling */
                                                                                                                h1 {
                                                                                                                    color: #2c3e50;
                                                                                                                    margin-bottom: 20px;
                                                                                                                }
                                                                                                                .btn-success {
                                                                                                                    background-color: #28a745;
                                                                                                                    border-color: #28a745;
                                                                                                                }
                                                                                                                .btn-primary {
                                                                                                                    background-color: #007bff;
                                                                                                                    border-color: #007bff;
                                                                                                                }
                                                                                                                .btn-secondary {
                                                                                                                    background-color: #6c757d;
                                                                                                                    border-color: #6c757d;
                                                                                                                }
                                                                                                                .form-group label {
                                                                                                                    font-weight: bold;
                                                                                                                    margin-bottom: 5px;
                                                                                                                    display: block;
                                                                                                                }
                                                                                                            
                                                                                                                .form-control {
                                                                                                                    border-radius: 4px;
                                                                                                                }
                                                                                                            </style>
                                                                                                            <h1>Data Artikel</h1>
                                                                                                            <button id="btnAddArticle" class="btn btn-success mb-3">Tambah Artikel</button>
                                                                                                            <div id="articleFormContainer" class="card p-4 mb-4" style="display: none;">
                                                                                                                <h2>Form Artikel</h2>
                                                                                                                <form id="articleForm" enctype="multipart/form-data">
                                                                                                                    <input type="hidden" id="articleId" name="id">
                                                                                                                    <div class="form-group mb-3">
                                                                                                                        <label for="judul">Judul:</label>
                                                                                                                        <input type="text" class="form-control" id="judul" name="judul" required>
                                                                                                                    </div>
                                                                                                                    <div class="form-group mb-3">
                                                                                                                        <label for="isi">Isi:</label>
                                                                                                                        <textarea class="form-control" id="isi" name="isi" rows="5" required></textarea>
                                                                                                                    </div>
                                                                                                                    <div class="form-group mb-3">
                                                                                                                        <label for="id_kategori">Kategori:</label>
                                                                                                                        <select class="form-control" id="id_kategori" name="id_kategori" required>
                                                                                                                        </select>
                                                                                                                    </div>
                                                                                                                    <div class="form-group mb-3">
                                                                                                                        <label for="gambar">Gambar:</label>
                                                                                                                        <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                                                                                                                        <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah gambar.</small>
                                                                                                                        <div id="currentImage" class="mt-2" style="display:none;">
                                                                                                                            <strong>Gambar Saat Ini:</strong><br>
                                                                                                                            <img src="" alt="Current Article Image" style="max-width: 150px; height: auto;">
                                                                                                                            <button type="button" class="btn btn-sm btn-warning mt-1" id="btnRemoveImage">Hapus Gambar</button>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    <div class="form-group mb-4">
                                                                                                                        <label for="status">Status:</label>
                                                                                                                        <select class="form-control" id="status" name="status" required>
                                                                                                                            <option value="draft">Draft</option>
                                                                                                                            <option value="public">Public</option>
                                                                                                                        </select>
                                                                                                                    </div>
                                                                                                                    <button type="submit" class="btn btn-primary" id="btnSubmitForm">Simpan</button>
                                                                                                                    <button type="button" class="btn btn-secondary ms-2" id="btnCancelForm">Batal</button>
                                                                                                                </form>
                                                                                                                <hr>
                                                                                                            </div>
                                                                                                            
                                                                                                            <table class="grid-table" id="artikelTable">
                                                                                                                <thead>
                                                                                                                    <tr>
                                                                                                                        <th>ID</th>
                                                                                                                        <th>Judul</th>
                                                                                                                        <th>Isi</th>
                                                                                                                        <th>Kategori</th>
                                                                                                                        <th>Gambar</th>
                                                                                                                        <th>Status</th>
                                                                                                                        <th>Ditambahkan Pada</th>
                                                                                                                        <th>Terakhir Diperbarui</th>
                                                                                                                        <th>Aksi</th>
                                                                                                                    </tr>
                                                                                                                </thead>
                                                                                                                <tbody>
                                                                                                                    </tbody>
                                                                                                            </table>
                                                                                                            
                                                                                                            <script src="<?= base_url('assets/jquery-3.7.1.min.js') ?>"></script>
                                                                                                            <script>
                                                                                                                $(document).ready(function() {
                                                                                                                    // Path dasar untuk gambar, disesuaikan dengan folder 'gambar'
                                                                                                                    const BASE_IMAGE_URL = '<?= base_url('gambar/') ?>';
                                                                                                            
                                                                                                                    function showLoadingMessage() {
                                                                                                                        $('#artikelTable tbody').html('<tr><td colspan="9" style="text-align: center;">Loading data...</td></tr>');
                                                                                                                    }
                                                                                                            
                                                                                                                    function loadCategories() {
                                                                                                                        console.log("Attempting to load categories...");
                                                                                                                        $.ajax({
                                                                                                                            url: "<?= base_url('ajax/getCategories') ?>",
                                                                                                                            method: "GET",
                                                                                                                            dataType: "json",
                                                                                                                            success: function(data) {
                                                                                                                                console.log("Categories data received:", data);
                                                                                                                                var options = '<option value="">Pilih Kategori</option>';
                                                                                                                                if (data.length > 0) {
                                                                                                                                    for (var i = 0; i < data.length; i++) {
                                                                                                                                        options += '<option value="' + data[i].id_kategori + '">' + data[i].nama_kategori + '</option>';
                                                                                                                                    }
                                                                                                                                } else {
                                                                                                                                    console.warn("No categories found in data.");
                                                                                                                                    options += '<option value="">Tidak ada kategori tersedia</option>';
                                                                                                                                }
                                                                                                                                $('#id_kategori').html(options);
                                                                                                                            },
                                                                                                                            error: function(jqXHR, textStatus, errorThrown) {
                                                                                                                                console.error('Error loading categories:', textStatus, errorThrown, jqXHR.responseText);
                                                                                                                                $('#id_kategori').html('<option value="">Error memuat kategori</option>');
                                                                                                                            }
                                                                                                                        });
                                                                                                                    }
                                                                                                                    function loadData() {
                                                                                                                        showLoadingMessage();
                                                                                                                        $.ajax({
                                                                                                                            url: "<?= base_url('ajax/getData') ?>",
                                                                                                                            method: "GET",
                                                                                                                            dataType: "json",
                                                                                                                            success: function(data) {
                                                                                                                                var tableBody = "";
                                                                                                                                if (data.length === 0) {
                                                                                                                                    tableBody = '<tr><td colspan="9" style="text-align: center;">Tidak ada data artikel.</td></tr>';
                                                                                                                                } else {
                                                                                                                                    for (var i = 0; i < data.length; i++) {
                                                                                                                                        var row = data[i];
                                                                                                                                        tableBody += '<tr>';
                                                                                                                                        tableBody += '<td>' + row.id + '</td>';
                                                                                                                                        tableBody += '<td>' + row.judul + '</td>';
                                                                                                                                        tableBody += '<td>' + row.isi.substring(0, 100) + (row.isi.length > 100 ? '...' : '') + '</td>';
                                                                                                                                        tableBody += '<td>' + (row.nama_kategori ? row.nama_kategori : 'N/A') + '</td>';
                                                                                                                                        tableBody += '<td>';
                                                                                                                                        if (row.gambar) {
                                                                                                                                            tableBody += '<img src="' + BASE_IMAGE_URL + row.gambar + '" alt="Gambar Artikel">';
                                                                                                                                        } else {
                                                                                                                                            tableBody += 'Tidak ada gambar';
                                                                                                                                        }
                                                                                                                                        tableBody += '</td>';
                                                                                                                                        tableBody += '<td>' + row.status + '</td>';
                                                                                                                                        tableBody += '<td>' + (row.created_at_formatted ? row.created_at_formatted : 'N/A') + '</td>';
                                                                                                                                        tableBody += '<td>' + (row.updated_at_formatted ? row.updated_at_formatted : 'N/A') + '</td>';
                                                                                                                                        tableBody += '<td>';
                                                                                                                                        tableBody += '<button class="btn btn-info btn-sm btn-edit me-2" ' +
                                                                                                                                            'data-id="' + row.id + '" ' +
                                                                                                                                            'data-judul="' + row.judul + '" ' +
                                                                                                                                            'data-isi="' + encodeURIComponent(row.isi) + '" ' + // Encode ISI to handle special characters
                                                                                                                                            'data-status="' + row.status + '" ' +
                                                                                                                                            'data-id_kategori="' + row.id_kategori + '" ' +
                                                                                                                                            'data-gambar="' + (row.gambar || '') + '">Edit</button>';
                                                                                                                                        tableBody += '<button class="btn btn-danger btn-sm btn-delete" data-id="' + row.id + '">Delete</button>';
                                                                                                                                        tableBody += '</td>';
                                                                                                                                        tableBody += '</tr>';
                                                                                                                                    }
                                                                                                                                }
                                                                                                                                $('#artikelTable tbody').html(tableBody);
                                                                                                                            },
                                                                                                                            error: function(jqXHR, textStatus, errorThrown) {
                                                                                                                                $('#artikelTable tbody').html('<tr><td colspan="9" style="text-align: center; color: red;">Error loading data: ' + textStatus + ' ' + errorThrown + '</td></tr>');
                                                                                                                            }
                                                                                                                        });
                                                                                                                    }
                                                                                                            
                                                                                                                    // Panggil fungsi-fungsi saat dokumen siap
                                                                                                                    loadData();
                                                                                                                    loadCategories();
                                                                                                            
                                                                                                                    // Event listener untuk tombol "Tambah Artikel"
                                                                                                                    $('#btnAddArticle').on('click', function() {
                                                                                                                        $('#articleFormContainer').slideDown();
                                                                                                                        $('#articleForm')[0].reset(); // Reset form
                                                                                                                        $('#articleId').val(''); // Kosongkan ID untuk mode tambah
                                                                                                                        $('#btnSubmitForm').text('Simpan');
                                                                                                                        loadCategories(); // Muat ulang kategori saat membuka form
                                                                                                                        $('#currentImage').hide(); // Sembunyikan pratinjau gambar saat menambah
                                                                                                                        $('#currentImage img').attr('src', '');
                                                                                                                        $('#gambar').prop('required', true); // Gambar wajib diisi untuk artikel baru
                                                                                                                    });
                                                                                                            
                                                                                                                    // Event listener untuk tombol "Batal"
                                                                                                                    $('#btnCancelForm').on('click', function() {
                                                                                                                        $('#articleFormContainer').slideUp();
                                                                                                                    });
                                                                                                            
                                                                                                                    // Event listener untuk submit formulir (Tambah/Update)
                                                                                                                    $('#articleForm').on('submit', function(e) {
                                                                                                                        e.preventDefault();
                                                                                                            
                                                                                                                        var id = $('#articleId').val();
                                                                                                                        var url = id ? "<?= base_url('ajax/update/') ?>" + id : "<?= base_url('ajax/create') ?>";
                                                                                                                        var method = "POST";
                                                                                                            
                                                                                                                        var formData = new FormData(this);
                                                                                                            
                                                                                                                        if (id && $('#gambar').get(0).files.length === 0) {
                                                                                                                             formData.delete('gambar');
                                                                                                                        }
                                                                                                                        $.ajax({
                                                                                                                            url: url,
                                                                                                                            method: method,
                                                                                                                            data: formData,
                                                                                                                            processData: false,
                                                                                                                            contentType: false,
                                                                                                                            dataType: "json",
                                                                                                                            success: function(response) {
                                                                                                                                if (response.status === 'OK') {
                                                                                                                                    alert(response.message);
                                                                                                                                    loadData();
                                                                                                                                    $('#articleFormContainer').slideUp();
                                                                                                                                } else {
                                                                                                                                    var errorMessage = '';
                                                                                                                                    if (typeof response.message === 'object') {
                                                                                                                                        for (var key in response.message) {
                                                                                                                                            errorMessage += response.message[key] + '\n';
                                                                                                                                        }
                                                                                                                                    } else {
                                                                                                                                        errorMessage = response.message;
                                                                                                                                    }
                                                                                                                                    alert('Error: ' + errorMessage);
                                                                                                                                }
                                                                                                                            },
                                                                                                                            error: function(jqXHR, textStatus, errorThrown) {
                                                                                                                                alert('Terjadi kesalahan saat menyimpan data: ' + textStatus + ' ' + errorThrown + (jqXHR.responseJSON ? ' - ' + JSON.stringify(jqXHR.responseJSON.message) : ''));
                                                                                                                            }
                                                                                                                        });
                                                                                                                    });
                                                                                                            
                                                                                                                    // Event listener untuk tombol "Edit"
                                                                                                                    $(document).on('click', '.btn-edit', function() {
                                                                                                                        var id = $(this).data('id');
                                                                                                                        var judul = $(this).data('judul');
                                                                                                                        var isi = decodeURIComponent($(this).data('isi')); // Decode ISI
                                                                                                                        var status = $(this).data('status');
                                                                                                                        var id_kategori = $(this).data('id_kategori');
                                                                                                                        var gambar = $(this).data('gambar');
                                                                                                            
                                                                                                                        $('#articleId').val(id);
                                                                                                                        $('#judul').val(judul);
                                                                                                                        $('#isi').val(isi);
                                                                                                                        $('#status').val(status);
                                                                                                                        loadCategories(); // Muat ulang kategori agar opsi terpilih bisa diset
                                                                                                                        // Set timeout for category selection to ensure options are loaded
                                                                                                                        setTimeout(function() {
                                                                                                                            $('#id_kategori').val(id_kategori);
                                                                                                                        }, 200); // Small delay to allow categories to load
                                                                                                            
                                                                                                                        $('#gambar').val('');
                                                                                                            
                                                                                                                        if (gambar) {
                                                                                                                            $('#currentImage img').attr('src', BASE_IMAGE_URL + gambar);
                                                                                                                            $('#currentImage').show();
                                                                                                                        } else {
                                                                                                                            $('#currentImage').hide();
                                                                                                                            $('#currentImage img').attr('src', '');
                                                                                                                        }
                                                                                                                        $('#gambar').prop('required', false);
                                                                                                                        $('#articleFormContainer').slideDown();
                                                                                                                        $('#btnSubmitForm').text('Update');
                                                                                                                    });
                                                                                                            
                                                                                                                    // Event listener untuk tombol "Hapus Gambar"
                                                                                                                    $('#btnRemoveImage').on('click', function() {
                                                                                                                        if (confirm('Apakah Anda yakin ingin menghapus gambar ini?')) {
                                                                                                                            var articleId = $('#articleId').val();
                                                                                                                            if (articleId) {
                                                                                                                                $.ajax({
                                                                                                                                    url: "<?= base_url('ajax/removeImage/') ?>" + articleId,
                                                                                                                                    method: "POST",
                                                                                                                                    dataType: "json",
                                                                                                                                    success: function(response) {
                                                                                                                                        if (response.status === 'OK') {
                                                                                                                                            alert(response.message);
                                                                                                                                            $('#currentImage').hide();
                                                                                                                                            $('#currentImage img').attr('src', '');
                                                                                                                                            loadData();
                                                                                                                                        } else {
                                                                                                                                            alert('Error: ' + response.message);
                                                                                                                                        }
                                                                                                                                    },
                                                                                                                                    error: function(jqXHR, textStatus, errorThrown) {
                                                                                                                                        alert('Error removing image: ' + textStatus + ' ' + errorThrown);
                                                                                                                                    }
                                                                                                                                });
                                                                                                                            } else {
                                                                                                                                alert('Tidak dapat menghapus gambar karena artikel belum disimpan.');
                                                                                                                            }
                                                                                                                        }
                                                                                                                    });
                                                                                                            
                                                                                                                    // Event listener untuk tombol "Delete"
                                                                                                                    $(document).on('click', '.btn-delete', function(e) {
                                                                                                                        e.preventDefault();
                                                                                                                        var id = $(this).data('id');
                                                                                                            
                                                                                                                        if (confirm('Apakah Anda yakin ingin menghapus artikel ini?')) {
                                                                                                                            $.ajax({
                                                                                                                                url: "<?= base_url('ajax/delete/') ?>" + id,
                                                                                                                                method: "DELETE",
                                                                                                                                dataType: "json",
                                                                                                                                success: function(response) {
                                                                                                                                    if (response.status === 'OK') {
                                                                                                                                        alert(response.message);
                                                                                                                                        loadData();
                                                                                                                                    } else {
                                                                                                                                        alert('Error: ' + response.message);
                                                                                                                                    }
                                                                                                                                },
                                                                                                                                error: function(jqXHR, textStatus, errorThrown) {
                                                                                                                                    alert('Error deleting article: ' + textStatus + ' ' + errorThrown);
                                                                                                                                }
                                                                                                                            });
                                                                                                                        }
                                                                                                                    });
                                                                                                                });
                                                                                                            </script>
                                                                                                            <?= $this->include('template/footer'); ?>


