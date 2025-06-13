# lanjutan-praktikum-7-8-9

# PRAKTIKUM 7
### langkah 1. membuat table kategori sebagai berikut :
![image](https://github.com/user-attachments/assets/eff28be4-9011-4968-806a-da5511bb4a8a)


### langkah 2. Mengubah Tabel Artikel Tambahkan foreign key `id_kategori` pada tabel `artikel` untuk membuat relasi dengan tabel `kategori`.
![image](https://github.com/user-attachments/assets/410040a6-7b40-4848-ad6f-82d35b25ab25)

### langkah 3. Membuat Model Kategori
![image](https://github.com/user-attachments/assets/e43e27cf-0112-4607-81e1-4a9f35842e5c)

### langkah 4.memodidikasi `ArtikelModel.php`
![image](https://github.com/user-attachments/assets/85eb1765-34ed-452e-a00e-667eebef9478)

### langkah 5.Memodifikasi View index.php, admin_index.php, form_add.php, form_edit.php, 


### langkah 6.Testing Lakukan uji coba untuk memastikan semua fungsi berjalan dengan baik:
• Menampilkan daftar artikel dengan nama kategori.
![image](https://github.com/user-attachments/assets/aa591427-5817-4fce-8d0b-f7f948b71a29)

![image](https://github.com/user-attachments/assets/919f6e12-1d0c-459a-9951-436d928826a1)

• Menambah artikel baru dengan memilih kategori.
![image](https://github.com/user-attachments/assets/3e160523-2363-454c-8df4-0ef90b5f238b)
![image](https://github.com/user-attachments/assets/dac82d63-2042-4fbe-8777-5a4b7d25d652)


• Mengedit artikel dan mengubah kategorinya.
![image](https://github.com/user-attachments/assets/dac87a84-56d1-4195-ab9b-4026a61c851d)
![image](https://github.com/user-attachments/assets/91d7461c-3eae-40a3-8a97-469014a9f04d)

• Menghapus artikel.
![image](https://github.com/user-attachments/assets/9113966e-2515-4f8d-b5ce-f2f4226e998f)


### langkah 7.Pertanyaan dan Tugas
1. Selesaikan semua langkah praktikum di atas.
   sudah
3. Modifikasi tampilan detail artikel (artikel/detail.php) untuk menampilkan nama kategori
artikel.
![image](https://github.com/user-attachments/assets/5a76794e-99ae-43d0-b3dd-8d9d330febb2)

![image](https://github.com/user-attachments/assets/54cfe2df-1deb-491a-bebf-aa3f8fddfb23)

5. Tambahkan fitur untuk menampilkan daftar kategori di halaman depan (opsional).
![image](https://github.com/user-attachments/assets/51d9e891-2c92-4589-bc4f-e91160df16c3)

7. Buat fungsi untuk menampilkan artikel berdasarkan kategori tertentu (opsional).
   ![image](https://github.com/user-attachments/assets/3ba7f428-d5ee-4253-b954-150817a8e079)






# PRAKTIKUM 8


### langkah 1.instal ajax versi terbaru dan letakkan di di rektori publik/asset/js/
![image](https://github.com/user-attachments/assets/f32e9d8a-0545-4713-8a4b-c8a136ee7507)


### langkah 2.Membuat AJAX Controller (AjaxController.php)
![image](https://github.com/user-attachments/assets/4b4fbd64-1745-4c36-a288-8236acc5e829)
![image](https://github.com/user-attachments/assets/8f5c74f7-83c5-4a1c-a85d-418861a9629b)



                                      
### langkah 3.pastikan url js nya di panggil di view/ajax/index
![image](https://github.com/user-attachments/assets/4e6bf5a1-fb4c-493a-a8d1-871ba2993007)

                                      
### langkah 4.Membuat View di dalam view/ajax/idex.php
![image](https://github.com/user-attachments/assets/19ddac0c-a73b-4ae4-ae96-d9e7106bd9d6)

![image](https://github.com/user-attachments/assets/0c7d6cb0-55af-478b-87e1-4f7609be3924)

### langkah 5.testing ajax
![image](https://github.com/user-attachments/assets/d36c9d43-748e-48c7-bac2-0f104103c7ee)

data otomatis tampil di kolom data artikel secara realtime ketika di klik edit tanpa perlu refress

![image](https://github.com/user-attachments/assets/350aeb4c-5181-4cf2-9590-366ebfe19a69)


### langkah 5.saya menambahkan waktu bedasarkan data terbaru dan terbaru di update dan manampilkan data artikel paling atas yang paling terbaru
![image](https://github.com/user-attachments/assets/81388675-121c-4b05-bad3-b45eeb3bacaf)


                                                                                                   
