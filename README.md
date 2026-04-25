Berikut adalah hasil pengujian fitur **KitaKas** berdasarkan aspek kualitas yang telah ditentukan pada Daily Project 6.

## Link link publish web: https://kitakas.infinityfree.me/
    -- Password untuk Login = password123
## Link GitHub: https://github.com/mecaaww/KitaKas

### 1. Tabel Pengujian Fungsional (Black Box Testing)

| ID | Fitur | Skenario Pengujian | Hasil yang Diharapkan | Status |
|:---|:---|:---|:---|:---:|
| **TF-01** | **Autentikasi Gender** | Login menggunakan pilihan Gender & Password (tanpa email). | Masuk ke dashboard dengan tema warna sesuai gender (Pink/Biru). | ✅ Pass |
| **TF-02** | **Kalender Keuangan** | Melihat ringkasan harian pada halaman kalender. | Indikator nominal transaksi muncul tepat pada tanggal terkait. | ✅ Pass |
| **TF-03** | **Financial Goals** | Membuat target tabungan baru bersama pasangan. | Target tersimpan dan progress bar muncul di halaman tujuan. | ✅ Pass |
| **TF-04** | **Weekly Chart** | Melihat visualisasi tren pengeluaran mingguan di Dashboard. | Grafik batang (Chart.js) muncul secara dinamis sesuai data. | ✅ Pass |
| **TF-05** | **Split Bill Otomatis** | Menggunakan opsi bagi rata (50:50) saat mencatat transaksi. | Nominal terbagi otomatis ke saldo pribadi dan bersama. | ✅ Pass |
| **TF-06** | **Komentar Transaksi** | Menambahkan catatan tambahan pada tiap input transaksi. | Komentar tersimpan dan dapat dilihat pada riwayat transaksi. | ✅ Pass |
| **TF-07** | **Catat Transaksi** | Menginput data pemasukan dan pengeluaran ke database. | Data berhasil tersimpan dan mengupdate saldo sistem secara real-time. | ✅ Pass |

---

### 2. Tabel Evaluasi Kualitas (ISO 25010)

| Aspek Kualitas | Kriteria Keberhasilan | Hasil Pengujian | Status |
|:---|:---|:---|:---:|
| **Functional Suitability** | Menjalankan seluruh fitur utama (TF-01 sampai TF-07). | Fitur inti berhasil diimplementasikan 100% sesuai Use Case. | ✅ Pass |
| **Usability** | Kemudahan antarmuka bagi pengguna. | Desain responsif, warna tematik, dan navigasi sangat intuitif. | ✅ Pass |
| **Maintainability** | Struktur kode mudah dikelola dan dikembangkan. | Menggunakan arsitektur MVC Laravel yang modular dan rapi. | ✅ Pass |

---

### 📝 Catatan Pengembangan (Keterbatasan Sistem)
> **Mengenai Extension AI pada Fitur 7:**
> Berdasarkan desain awal, Fitur 7 (Catat Transaksi) direncanakan memiliki ekstensi berupa deteksi kategori otomatis berbasis AI. Namun, pada rilis Project 7 kali ini, **ekstensi AI tersebut ditiadakan** dikarenakan kompleksitas integrasi library *Natural Language Processing* (NLP) yang membutuhkan sumber daya server lebih tinggi. Pengembangan difokuskan sepenuhnya pada stabilitas fitur utama seperti **Kalender Keuangan** dan **Split Bill** agar memberikan akurasi data yang maksimal bagi pengguna.
