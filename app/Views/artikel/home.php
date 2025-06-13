<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>



<h3>Daftar Artikel</h3>
<ul>
    <?php foreach ($artikel as $artikel_item): ?>
        <li>
            <h4><a href="<?= base_url('/artikel/' . esc($artikel_item['slug'])); ?>"><?= esc($artikel_item['judul']); ?></a></h4>
            <p><?= esc($artikel_item['isi']); ?></p>
            <!-- Menampilkan gambar (jika ada) -->
            <?php if (!empty($artikel_item['gambar'])): ?>
                <img src="<?= base_url('gambar/' . esc($artikel_item['gambar'])); ?>" alt="<?= esc($artikel_item['judul']); ?>" width="200">
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
</ul>




<?= $this->endSection() ?>
