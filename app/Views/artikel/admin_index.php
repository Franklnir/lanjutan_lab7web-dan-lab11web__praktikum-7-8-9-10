<?= $this->include('template/admin_header'); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4"><?= $title; ?></h2>

            <div class="search-container">
                <form id="search-form">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="search-box" class="form-label">Cari Artikel</label>
                            <input type="text" name="q" id="search-box" value="<?= $q; ?>"
                                   placeholder="Cari judul artikel" class="form-control">
                        </div>

                        <div class="filter-group">
                            <label for="category-filter" class="form-label">Kategori</label>
                            <select name="kategori_id" id="category-filter" class="form-select">
                                <option value="">Semua Kategori</option>
                                <?php foreach ($kategori as $k): ?>
                                    <option value="<?= $k['id_kategori']; ?>" <?= ($kategori_id == $k['id_kategori']) ? 'selected' : ''; ?>>
                                        <?= $k['nama_kategori']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-group">
                            <button type="submit" class="btn btn-primary">Cari</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="sort-container">
                <div class="d-flex align-items-center">
                    <label for="sort-select" class="form-label me-2 mb-0">Urutkan:</label>
                    <select id="sort-select" class="form-select w-auto">
                        <option value="artikel.id|desc">Terbaru</option>
                        <option value="artikel.judul|asc">Judul A-Z</option>
                        <option value="artikel.judul|desc">Judul Z-A</option>
                    </select>
                </div>
            </div>

            <div id="loading" class="loading-container" style="display: none;">
                <div class="loader"></div>
                <span>Memuat data...</span>
            </div>

            <div id="article-container" class="article-container"></div>
            <div id="pagination-container" class="pagination-container"></div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="/admin-articles.css">
<script src="<?= base_url('assets/jquery-3.7.1.min.js') ?>"></script>

<script>
$(document).ready(function () {
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
            success: function (data) {
                renderArticles(data.artikel);
                renderPagination(data.pager, data.q, data.kategori_id);
            },
            complete: function () {
                loading.hide();
            }
        });
    };

    const renderArticles = (articles) => {
        let html = '<table class="article-table">';
        html += `
            <thead>
                <tr>
                    <th width="80px">ID</th>
                    <th>Judul</th>
                    <th width="180px">Kategori</th>
                    <th width="120px">Status</th>
                    <th width="180px">Aksi</th>
                </tr>
            </thead>
            <tbody>`;

        if (articles.length > 0) {
            articles.forEach(article => {
                const statusClass = article.status === 'published' ? 'status-published' : 'status-draft';
                html += `
                <tr>
                    <td>${article.id}</td>
                    <td>
                        <div class="article-title">
                            <a href="/artikel/detail/${article.id}" class="text-decoration-none">${article.judul}</a>
                        </div>
                        <div class="article-excerpt">${article.isi.substring(0, 100)}...</div>
                    </td>
                    <td>${article.nama_kategori}</td>
                    <td><span class="${statusClass}">${article.status}</span></td>
                    <td>
                        <div class="article-actions">
                            <a class="btn btn-sm btn-info" href="/admin/artikel/edit/${article.id}">Ubah</a>
                            <a class="btn btn-sm btn-danger" onclick="return confirm('Yakin menghapus data?');" href="/admin/artikel/delete/${article.id}">Hapus</a>
                        </div>
                    </td>
                </tr>`;
            });
        } else {
            html += '<tr><td colspan="5" class="text-center py-4">Tidak ada data artikel yang ditemukan</td></tr>';
        }

        html += '</tbody></table>';
        articleContainer.html(html);
    };

    const renderPagination = (pager, q, kategori_id) => {
        let html = '<nav><ul class="pagination justify-content-center">';

        if (pager.currentPage > 1) {
            const prevUrl = `${pager.baseUrl}?page=${pager.currentPage - 1}&q=${q}&kategori_id=${kategori_id}&sort=${currentSort}&order=${currentOrder}`;
            html += `<li class="page-item"><a class="page-link" href="${prevUrl}">&laquo;</a></li>`;
        }

        for (let i = 1; i <= pager.totalPages; i++) {
            const isActive = i === pager.currentPage;
            const pageUrl = `${pager.baseUrl}?page=${i}&q=${q}&kategori_id=${kategori_id}&sort=${currentSort}&order=${currentOrder}`;
            html += `<li class="page-item ${isActive ? 'active' : ''}"><a class="page-link" href="${pageUrl}">${i}</a></li>`;
        }

        if (pager.currentPage < pager.totalPages) {
            const nextUrl = `${pager.baseUrl}?page=${pager.currentPage + 1}&q=${q}&kategori_id=${kategori_id}&sort=${currentSort}&order=${currentOrder}`;
            html += `<li class="page-item"><a class="page-link" href="${nextUrl}">&raquo;</a></li>`;
        }

        html += '</ul></nav>';
        paginationContainer.html(html);
    };

    searchForm.on('submit', function (e) {
        e.preventDefault();
        const q = searchBox.val();
        const kategori_id = categoryFilter.val();
        fetchData(`/admin/artikel?q=${q}&kategori_id=${kategori_id}&sort=${currentSort}&order=${currentOrder}`);
    });

    categoryFilter.on('change', function () {
        searchForm.trigger('submit');
    });

    sortSelect.on('change', function () {
        const val = $(this).val().split('|');
        currentSort = val[0];
        currentOrder = val[1];
        searchForm.trigger('submit');
    });

    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        const url = $(this).attr('href');
        if (url && url !== '#') {
            fetchData(url);
        }
    });

    fetchData(`/admin/artikel?sort=${currentSort}&order=${currentOrder}`);
});
</script>

<?= $this->include('template/admin_footer'); ?>
