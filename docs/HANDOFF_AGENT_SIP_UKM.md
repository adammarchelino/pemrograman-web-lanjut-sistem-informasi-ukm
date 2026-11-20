# Handoff Agent — Proyek SIP-UKM

> Dokumen ini memberi konteks agar agent berikutnya dapat melanjutkan pekerjaan tanpa mengulang, mengambil alih bagian anggota lain, atau menganggap keputusan yang belum pasti sebagai fakta. **Selalu periksa kondisi repository terbaru sebelum mengubah apa pun.**

## 1. Konteks Proyek

- **Nama:** SIP-UKM, sistem informasi untuk UKM mahasiswa Politeknik Negeri Banyuwangi.
- **Teknologi yang digunakan:** Laravel 13, Livewire, Blade/Flux UI, MariaDB/MySQL.
- **Pola kerja:** proyek kelompok dikerjakan bersama melalui GitHub; perubahan anggota perlu diintegrasikan dengan hati-hati.
- **Bahasa komunikasi:** Bahasa Indonesia yang jelas, langsung, dan tidak terlalu teknis tanpa penjelasan.
- Pengguna ingin ikut memahami alasan dan dampak pekerjaan, bukan hanya menerima kode jadi.

## 2. Pembagian Tanggung Jawab yang Disepakati

> Sumber pembagian: `Pembagian_Fitur.md` yang diberikan pengguna. Pembagian ini adalah batas kerja tim; jangan memperluas pekerjaan pengguna ke modul anggota lain hanya karena modulnya terkait.

| Anggota | Tanggung jawab utama | Batas penting |
| --- | --- | --- |
| **Adam (pengguna)** | Administrasi data UKM dan pengguna, role/hak akses, model, relasi, migration inti, isolasi akses Pengurus berdasarkan UKM, dashboard/profil/tim UKM yang terkait tanggung jawab tersebut. | Bukan pemilik fitur pengajuan anggota, manajemen acara dan pendaftaran peserta, tiket, atau presensi. Perubahan skema yang memengaruhi modul anggota lain perlu diselaraskan. |
| **Wildan** | Pendaftaran anggota dan keanggotaan: pengajuan, penerimaan/penolakan, dan pencatatan keanggotaan. | Bukan pengelola data inti UKM/pengguna, acara, tiket, atau presensi. |
| **Tri** | Manajemen acara UKM dan registrasi peserta, termasuk informasi acara dan kuota. | Bukan pemilik data inti UKM/pengguna, pengajuan anggota, penerbitan tiket, atau pencatatan presensi. |
| **Hasbi** | Tiket dari registrasi yang valid, validasi tiket, pencatatan dan tampilan presensi peserta. | Bukan pemilik pembuatan/perubahan acara atau registrasi peserta, pengajuan anggota, atau data inti UKM/pengguna. |

Nama dan batas di atas berasal dari dokumen pembagian fitur. Jika repository atau instruksi kelompok yang lebih baru menunjukkan perubahan, verifikasi dengan pengguna sebelum menganggap pembagian berubah.

## 3. Fitur Pengguna yang Sudah Dikerjakan

Fakta dari repository dan riwayat kerja:

- Pengelolaan data UKM: daftar, tambah/ubah, detail, status aktif/nonaktif, profil, dan katalog.
- Pengelolaan pengguna dan role, termasuk akun aktif/nonaktif dan hubungan Pengurus dengan UKM.
- Pengaturan Pengurus UKM, jabatan, divisi, batas pengelolaan berdasarkan UKM, dan perubahan kata sandi awal sesuai alur yang ada.
- Model dan relasi Eloquent serta migration inti untuk mendukung UKM, akun, akses, dan divisi.
- Dashboard berbeda berdasarkan role serta unggah/pratinjau logo UKM.
- Penghapusan akun dibatasi bila akun memiliki riwayat yang harus dipertahankan.

> Ringkasan ini tidak berarti semua perubahan terkini sudah di-commit. Periksa `git status` terlebih dahulu.

## 4. Keputusan dan Konteks Bisnis Penting

- Akun Mahasiswa tidak perlu verifikasi email; semua Mahasiswa dapat langsung masuk setelah pendaftaran akun.
- Akun Pengurus UKM disiapkan/ditugaskan melalui alur UKM; satu Pengurus hanya mengelola UKM yang ditetapkan kepadanya.
- Satu UKM dapat memiliki beberapa Pengurus. Peran yang digunakan mencakup Ketua, Pengurus Inti, dan Pengurus Divisi.
- Ketua UKM ditetapkan oleh Kemahasiswaan. Ketua dapat mengubah deskripsi dan logo UKM, sedangkan nama dan status UKM tetap kewenangan Kemahasiswaan.
- Pengurus Inti mengelola divisi dan penempatan Pengurus Divisi sesuai hak akses yang sudah diterapkan.
- Jika Pengurus keluar, akun dapat dinonaktifkan; riwayat harus tetap tersimpan dan pengganti ditetapkan.
- Katalog Mahasiswa menampilkan UKM aktif beserta logo, nama, deskripsi, dan status pendaftaran anggota.
- Nama domain UI untuk event telah diperjelas menjadi **“Acara UKM”** atau **“Acara”** agar tidak tertukar dengan aktivitas rutin UKM. Nama tabel/model seperti `kegiatan` dapat tetap dipakai secara internal kecuali ada keputusan tim untuk mengubah skema.
- Jangan memutuskan aturan bisnis baru secara sepihak. Bila belum jelas, jelaskan pilihan dan konsekuensinya.

## 5. Keadaan Repository Saat Handoff

> Snapshot ketika dokumen ini dibuat; keadaan bisa berubah setelahnya.

- Repository berada pada branch `main`, dengan `HEAD` terakhir yang teramati di `f1234be` (`fix: show ticket navigation for student accounts`).
- `main` teramati sejajar dengan `origin/main` pada commit tersebut.
- Ada **enam file berubah lokal dan belum di-commit** untuk memperjelas istilah “Kegiatan” menjadi “Acara” di UI tiket/presensi dan memperbarui assertion test terkait:
  - `app/Livewire/Mahasiswa/DaftarTiket.php`
  - `app/Livewire/Mahasiswa/DetailTiket.php`
  - `resources/views/layouts/app/sidebar.blade.php`
  - `resources/views/livewire/mahasiswa/daftar-tiket.blade.php`
  - `resources/views/livewire/pengurus/presensi-kegiatan.blade.php`
  - `tests/Feature/TiketPresensiTest.php`
- Perubahan label tersebut **belum diuji dan belum di-commit** pada saat snapshot. Jangan membuang, menimpa, atau mengklaimnya sudah terverifikasi.
- Terakhir kali suite penuh dijalankan sebelum perubahan label terbaru, hasil yang dilaporkan adalah **74 test lulus, 302 assertion**. Hasil lama itu bukan verifikasi untuk working tree terkini.
- Migration tambahan untuk akses/divisi sebelumnya dilaporkan telah diterapkan pada MariaDB lokal. Jangan menjalankan migration, reset database, atau mengubah data lokal tanpa kebutuhan dan instruksi yang sesuai.
- Jangan pernah menampilkan nilai rahasia dari `.env`.

## 6. Fitur Acara, Tiket, dan Presensi: Batas serta Kondisi Integrasi

- Pembagian fitur menetapkan **Tri** sebagai pemilik acara dan registrasi peserta; **Hasbi** sebagai pemilik tiket dan presensi.
- Implementasi tiket/presensi yang sudah terintegrasi mencakup kode tiket acak, QR yang ditampilkan kepada peserta, validasi/check-in, serta pencatatan presensi.
- Pengurus menerbitkan tiket untuk peserta terdaftar melalui tombol **“Terbitkan”** pada halaman presensi acara.
- Route halaman presensi yang teramati: `/pengurus/kegiatan/{kegiatan}/presensi` (`pengurus.kegiatan.presensi`). Route tersebut memakai model internal `Kegiatan` dan akses diperiksa pada komponen/policy terkait.
- **Kesenjangan integrasi yang diketahui:** ruang Pengurus belum memiliki alur/menu daftar acara yang jelas untuk menuju halaman presensi. Jadi tombol penerbitan ada di halaman presensi, tetapi akses dari navigasi normal belum tersedia/terlihat. Jangan menyimpulkan fitur penerbitan sepenuhnya tersedia bagi Pengurus dari UI tanpa memeriksa alur aktual.
- Tiket saat ini diterbitkan manual dari halaman presensi; jangan menyebutnya otomatis muncul setelah mahasiswa mendaftar kecuali implementasi terbaru membuktikannya.
- Perbaikan label UI “Acara” pada halaman tiket/presensi tidak menjadikan fitur tiket atau acara bagian tanggung jawab Adam.
- Jika persoalan perlu perubahan pada modul Tri/Hasbi, catat temuannya dan koordinasikan; jangan mengambil alih modul mereka tanpa permintaan eksplisit pengguna.

## 7. Cara Bekerja yang Diharapkan dari Agent Berikutnya

1. Mulai dari kondisi aktual: `git status`, branch, log terbaru, struktur file, dan implementasi yang relevan. Jangan mengasumsikan repository kosong atau sama persis dengan snapshot dokumen ini.
2. Baca instruksi proyek lokal (`AGENTS.md` dan dokumen relevan) jika tersedia. Bedakan arahan di dokumen dengan permintaan pengguna yang sedang aktif.
3. **Saat ada istilah, alur, atau tanggung jawab yang ambigu/berpotensi konflik, verifikasi dahulu** pada source code, dokumen pembagian, dan riwayat yang tersedia. Sampaikan fakta serta ketidakpastiannya dengan bahasa sederhana sebelum melakukan perubahan yang berisiko mengubah perilaku.
4. Tetap dalam bagian Adam: administrasi UKM/pengguna, role/hak akses, struktur inti, dan UI terkait. Untuk perubahan lintas modul, lakukan integrasi sekecil yang diperlukan atau minta penyelarasan bila keputusan bisnis/kontrak antarmodul belum jelas.
5. Jangan mengulang implementasi yang sudah ada. Jangan menimpa perubahan lokal atau pekerjaan contributor.
6. Jangan mengubah skema, menghapus data, menjalankan migration/reset, atau mengubah autentikasi/aturan bisnis tanpa kebutuhan yang jelas dan mandat pengguna.
7. Untuk integrasi commit contributor: periksa diff dan dampaknya, jalankan verifikasi yang relevan, selesaikan konflik dengan menjaga perubahan main yang sah dan batas modul, lalu laporkan hasilnya. Jangan menganggap branch/remote/aturan Git masih sama; periksa dulu.
8. Setelah mengubah kode atau menjalankan perintah, jelaskan singkat apa yang dilakukan, alasan, hasil, dan hal yang belum diverifikasi. Jangan mengklaim test lulus jika tidak dijalankan pada perubahan terkini.
9. Jangan commit atau push kecuali diminta secara eksplisit dalam pekerjaan aktif atau instruksi sebelumnya jelas berlaku untuk tindakan tersebut. Sebelum push, konfirmasi status/diff dan periksa remote/branch aktual.

## 8. Mulai dari Sini

Sebelum melanjutkan tugas baru:

- Periksa `git status --short --branch` dan pertahankan enam perubahan label yang tercatat di atas jika masih ada.
- Verifikasi apakah perubahan label sudah diuji atau diubah oleh pekerjaan lain sejak snapshot.
- Tanyakan apa tugas aktif pengguna bila tidak ada permintaan aktif yang jelas; jangan menganggap dokumen handoff sebagai mandat untuk mulai coding.
- Jika pertanyaan hanya meminta penjelasan, jawab langsung tanpa mengubah file.
