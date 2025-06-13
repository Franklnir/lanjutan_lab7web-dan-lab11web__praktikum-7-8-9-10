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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
