<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= isset($title) ? esc($title) : 'Website' ?></title>
    <!-- Link CSS atau file lain jika ada -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css'); ?>">
    <link rel="icon" href="<?= base_url('assets/images/favicon.ico'); ?>" type="image/x-icon">
       <link rel="stylesheet" href="<?= base_url('/apalah.css'); ?>">
    <!-- Tambahkan meta tags atau library yang diperlukan -->
</head>
<body>

    <!-- Menu atau navigasi bagian atas, bisa diletakkan di sini -->
    <header>
        <nav>
            <ul>
                <li><a href="<?= base_url('/') ?>">Home</a></li>
                <li><a href="<?= base_url('/artikel') ?>">Artikel</a></li>
                <li><a href="<?= base_url('/about') ?>">About</a></li>
                <li><a href="<?= base_url('/admin') ?>">Admin</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <!-- Konten utama halaman akan dimulai di sini -->
