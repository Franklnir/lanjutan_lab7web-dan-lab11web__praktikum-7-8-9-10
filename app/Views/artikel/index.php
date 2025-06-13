<?= $this->include('template/header'); ?>

<div class="container">

    <h2>Daftar Artikel</h2>

    <!-- Tombol Tambah Artikel -->
    <a href="<?= base_url('/admin/artikel/create'); ?>" class="btn btn-primary mb-3">Tambah Artikel</a>

    <div class="container py-4">

        <h2 class="mb-4"><?= esc($title) ?></h2>

        <!-- Form pencarian dan filter kategori -->
        <form method="get" class="mb-4 d-flex gap-2">
            <input type="text" name="q" class="form-control" placeholder="Cari artikel..." value="<?= esc($q ?? '') ?>">

            <select name="id_kategori" class="form-select">
                <option value="">Semua Kategori</option>
                <?php foreach ($kategori as $kat): ?>
                    <option value="<?= $kat['id_kategori']; ?>" <?= ($id_kategori == $kat['id_kategori']) ? 'selected' : ''; ?>>
                        <?= esc($kat['nama_kategori']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn btn-secondary">Cari</button>
        </form>

        <!-- Tabel daftar artikel -->
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Judul</th>
                    <th>Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($artikel)): ?>
                    <?php foreach ($artikel as $item): ?>
                        <tr>
                            <td><?= esc($item['judul']); ?></td>
                            <td>
                                <?php
                                    $namaKategori = '-';
                                    foreach ($kategori as $kat) {
                                        if ($kat['id_kategori'] == $item['id_kategori']) {
                                            $namaKategori = $kat['nama_kategori'];
                                            break;
                                        }
                                    }
                                    echo esc($namaKategori);
                                ?>
                            </td>
                            <td>
                                <a href="<?= base_url('/admin/artikel/edit/'.$item['id']); ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="<?= base_url('/admin/artikel/delete/'.$item['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada artikel tersedia.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    </div>
</div>

<?= $this->include('template/footer'); ?>
