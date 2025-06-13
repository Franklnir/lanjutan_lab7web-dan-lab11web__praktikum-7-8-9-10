<?php
namespace App\Models;

use CodeIgniter\Model;

class ArtikelModel extends Model
{
    protected $table = 'artikel';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['judul', 'isi', 'status', 'slug', 'gambar', 'id_kategori'];

       // --- Tambahkan baris-baris ini untuk mengaktifkan timestamps otomatis ---
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime'; // Atau 'int' jika Anda menggunakan Unix timestamps
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Fungsi untuk mendapatkan artikel beserta kategori
    public function getArtikelDenganKategori()
    {
        return $this->db->table('artikel')
            ->select('artikel.*, kategori.nama_kategori')
            ->join('kategori', 'kategori.id_kategori = artikel.id_kategori')
            ->get()
            ->getResult();  // Mengembalikan hasil sebagai objek
    }
}
