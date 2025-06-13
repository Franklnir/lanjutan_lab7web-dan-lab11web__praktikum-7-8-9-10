<?= $this->include('template/admin_header'); ?>

<h2><?= esc($title); ?></h2>

<form action="<?= base_url('/admin/artikel/store'); ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field(); ?> <!-- Include CSRF token untuk keamanan -->

    <!-- Judul Input -->
    <p>
        <label for="judul">Judul</label><br>
        <input type="text" name="judul" id="judul" value="<?= old('judul'); ?>" required>
    </p>

    <!-- Isi Textarea -->
    <p>
        <label for="isi">Isi</label><br>
        <textarea name="isi" id="isi" cols="50" rows="10" required><?= old('isi'); ?></textarea>
    </p>

    <!-- Kategori Dropdown -->
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

    <!-- Error Messages (Optional) -->
    <?php if (isset($validation) && $validation->getErrors()): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($validation->getErrors() as $error): ?>
                    <li><?= esc($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Submit Button -->
    <p>
        <input type="submit" value="Simpan" class="btn btn-large">
    </p>
</form>

<?= $this->include('template/admin_footer'); ?>
