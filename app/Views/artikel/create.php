<?= $this->include('template/admin_header'); ?>

<h2><?= esc($title); ?></h2>

<form action="" method="post" enctype="multipart/form-data">
    <!-- Judul Artikel -->
    <p>
        <label for="judul">Judul</label><br>
        <input type="text" name="judul" value="<?= old('judul'); ?>" required>
    </p>

    <!-- Isi Artikel -->
    <p>
        <label for="isi">Isi</label><br>
        <textarea name="isi" cols="50" rows="10" required><?= old('isi'); ?></textarea>
    </p>

    <!-- Dropdown Kategori -->
    <p>
        <label for="id_kategori">Kategori</label><br>
        <select name="id_kategori" id="id_kategori" required>
            <option value="">Pilih Kategori</option>
            <?php foreach ($kategori as $kategori_item): ?>
                <option value="<?= esc($kategori_item['id_kategori']); ?>" <?= old('id_kategori') == $kategori_item['id_kategori'] ? 'selected' : ''; ?>>
                    <?= esc($kategori_item['nama_kategori']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>

    <!-- Upload Gambar -->
    <p>
        <label for="gambar">Upload Gambar</label><br>
        <input type="file" name="gambar" required>
    </p>

    <!-- Submit Button -->
    <p>
        <input type="submit" value="Simpan">
    </p>
</form>

<?= $this->include('template/admin_footer'); ?>
