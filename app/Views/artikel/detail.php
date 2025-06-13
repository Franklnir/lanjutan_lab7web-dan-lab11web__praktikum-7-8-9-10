<?= $this->include('template/header'); ?>

<article class="entry">
    <h2><?= esc($artikel['judul']); ?></h2>

    <!-- Menampilkan kategori -->
    <?php if (!empty($artikel['nama_kategori'])): ?>
        <p><strong>Kategori: </strong><?= esc($artikel['nama_kategori']); ?></p>
    <?php else: ?>
        <p><strong>Kategori: </strong>Belum ada kategori</p>
    <?php endif; ?>

    <img src="<?= base_url('/gambar/' . esc($artikel['gambar'])); ?>" alt="<?= esc($artikel['judul']); ?>">
    <p><?= esc($artikel['isi']); ?></p>
</article>

<?= $this->include('template/footer'); ?>
