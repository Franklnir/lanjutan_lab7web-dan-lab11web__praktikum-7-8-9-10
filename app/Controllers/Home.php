<?php

namespace App\Controllers;

use App\Models\ArtikelModel;
use App\Models\KategoriModel; // Pastikan ini di-import juga untuk join

class Home extends BaseController
{
    public function index()
    {
        // Membuat instance dari ArtikelModel
        $artikelModel = new ArtikelModel();

        // Mengambil artikel terbaru (latest) yang berstatus 'public'
        // dan menggabungkannya dengan data kategori
        $artikelData = $artikelModel
                            ->select('artikel.*, kategori.nama_kategori') // Pilih semua kolom artikel dan nama kategori
                            ->join('kategori', 'kategori.id_kategori = artikel.id_kategori', 'left') // Gabungkan tabel kategori
                            ->where('artikel.status', 'public') // Hanya tampilkan artikel dengan status 'public'
                            ->orderBy('artikel.id', 'DESC') // Ini PENTING: Urutkan berdasarkan ID terbaru di atas
                            ->limit(10) // Opsional: Batasi jumlah artikel yang ditampilkan (misalnya 10)
                            ->findAll();

        // Mengirimkan data artikel terbaru ke view dengan nama 'artikel'
        $data = [
            'title' => 'Beranda - Artikel Terbaru', // Judul halaman
            'artikel' => $artikelData, // Data artikel yang sudah diurutkan
        ];

        // Memanggil view 'artikel/home'
        return view('artikel/home', $data);
    }
}