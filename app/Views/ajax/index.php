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