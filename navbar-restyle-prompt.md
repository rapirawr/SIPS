# 🧭 Prompt: Restyling Navbar Laravel — Sticky + Transparan dengan Scroll Behavior

---

## 🧠 Peran & Tujuan

Kamu adalah senior frontend developer dan UI/UX designer yang ahli membangun komponen navigasi premium untuk aplikasi Laravel. Tugasmu adalah merombak total navbar yang sudah ada menjadi versi yang lebih modern, elegan, dan beranimasi — bukan sekadar perbaikan kecil, tapi redesign yang terasa seperti produk kelas atas.

---

## 🎯 Konteks Project

| Field | Value |
|---|---|
| **Nama Project** | `SIPS` |
| **Deskripsi** | `Sistem Informasi Pengaduan Sarana dan Prasarana Sekolah` |
| **Stack** | Laravel Blade + Tailwind CSS + Vanilla JS |
| **Kondisi Navbar Saat Ini** | Sudah ada styling tapi perlu dirombak total |
| **Posisi** | Fixed / sticky di bagian atas halaman |
| **Tema** | Transparan saat di atas, berubah saat scroll |

---

## 📁 Struktur Output

Sesuaikan output dengan konvensi Laravel berikut:

- Komponen navbar di `resources/views/components/navbar.blade.php` atau langsung di `resources/views/layout/app.blade.php` — sesuaikan dengan struktur project yang sudah ada
- Styling navbar di file CSS yang sudah ada atau buat `resources/css/navbar.css` jika belum ada
- Logika scroll behavior di `resources/js/navbar.js` atau tambahkan ke file JS yang sudah ada

Gunakan `@push('scripts')` dan `@push('styles')` jika inject asset dilakukan dari luar layout utama. Semua komentar kode wajib dalam Bahasa Indonesia.

---

## 🎨 Perilaku Visual Navbar

### State 1 — Saat di Paling Atas Halaman
Navbar sepenuhnya transparan tanpa background. Teks dan ikon berwarna putih atau terang. Tidak ada shadow, tidak ada border. Navbar terasa menyatu dengan hero section di bawahnya.

### State 2 — Saat Pengguna Scroll ke Bawah
Navbar berubah menjadi frosted glass — background semi-transparan dengan efek backdrop blur. Muncul border tipis di bagian bawah navbar yang hampir transparan. Shadow halus muncul untuk memberi kesan kedalaman. Perubahan dari state 1 ke state 2 harus terjadi secara smooth dengan transisi yang halus — tidak boleh terasa "pop" atau mendadak.

### Threshold Scroll
Perubahan terjadi setelah pengguna scroll sekitar 60 hingga 80 piksel dari atas halaman.

---

## 🧩 Anatomi Navbar

### Kiri — Logo / Brand
- Nama project atau logo dalam bentuk teks dengan font display yang bold dan berkarakter
- Huruf pertama atau satu kata kunci bisa diberi warna accent sebagai identitas visual
- Saat di-hover, logo sedikit bergerak ke atas atau berkilau dengan efek subtle

### Tengah — Menu Navigasi (Desktop)
- Daftar link navigasi utama sesuai halaman yang ada di project
- Teks uppercase, spasi antar huruf sedikit lebar, ukuran kecil dan rapi
- Setiap link punya efek hover berupa garis bawah yang muncul dari tengah ke kiri dan kanan, atau fade-in warna accent
- Link yang sedang aktif diberi tanda visual yang jelas — bisa garis bawah permanen, warna berbeda, atau dot kecil di bawahnya

### Kanan — Aksi / Auth
- Jika pengguna belum login: tombol Login (ghost/outline) dan tombol Register (solid dengan warna primary)
- Jika pengguna sudah login: avatar atau inisial nama pengguna, dengan dropdown menu yang elegan saat diklik
- Dropdown berisi opsi seperti profil, dashboard, dan logout — dengan animasi muncul dari atas ke bawah

### Mobile — Hamburger Menu
- Ikon hamburger yang berubah menjadi ikon silang (X) saat menu dibuka, dengan animasi morph yang smooth
- Menu mobile muncul sebagai fullscreen overlay atau slide-down panel dari atas
- Background menu mobile menggunakan glassmorphism gelap
- Setiap link menu mobile muncul bertahap dari atas ke bawah satu per satu (staggered reveal)
- Menutup menu saat link diklik atau saat area luar di-tap

---

## ✍️ Tipografi Navbar

Gunakan font yang konsisten dengan keseluruhan halaman:

- **Logo / Brand** — font display, weight paling berat, ukuran sedang hingga besar
- **Link navigasi** — font body, weight medium, uppercase, letter-spacing sedikit lebar
- **Tombol CTA** — font body, weight semi-bold, uppercase, letter-spacing kecil

Semua ukuran font navbar harus terasa proporsional — tidak terlalu besar hingga mendominasi, tidak terlalu kecil hingga sulit dibaca.

---

## ✨ Animasi & Interaksi

### Page Load
Saat halaman pertama dibuka, navbar tidak langsung muncul. Elemen-elemen navbar muncul bertahap dari atas dengan animasi fade-in dan slide-down ringan — logo muncul pertama, lalu link navigasi satu per satu, lalu tombol aksi. Keseluruhan animasi selesai dalam waktu kurang dari satu detik.

### Hover pada Link
Efek hover harus terasa responsif dan premium. Hindari efek yang terlalu sederhana seperti hanya perubahan warna saja. Gunakan kombinasi perubahan warna dengan elemen dekoratif seperti garis bawah animasi, titik kecil, atau subtle glow.

### Scroll Transition
Transisi antara state transparan dan frosted glass menggunakan CSS transition pada properti background, backdrop-filter, box-shadow, dan border. Durasi transisi sekitar 300 milidetik dengan easing yang smooth.

### Active State
Link yang sedang aktif harus jelas terlihat berbeda dari link lainnya tanpa terasa mencolok. Gunakan pendekatan yang konsisten dengan tema visual keseluruhan.

---

## 🎨 Panduan Visual

### Warna
Gunakan CSS variables yang sudah didefinisikan di project agar konsisten dengan warna global halaman. Navbar tidak boleh terasa seperti komponen yang "ditempel" — harus menyatu dengan keseluruhan desain.

### Glassmorphism saat Scroll
Background menggunakan warna gelap dengan opacity sekitar 70 hingga 85 persen. Backdrop blur cukup kuat untuk memberikan efek kaca yang jelas namun tidak mengaburkan konten di belakangnya secara berlebihan. Border bawah tipis dengan opacity sangat rendah.

### Shadow
Shadow saat scroll berwarna senada dengan warna primary atau accent project — bukan hitam polos. Ini membuat navbar terasa lebih hidup dan terintegrasi dengan palet warna keseluruhan.

---

## 📐 Responsivitas

- **Desktop (lg ke atas)** — layout horizontal penuh dengan logo, menu tengah, dan aksi di kanan
- **Tablet (md)** — menu navigasi bisa disembunyikan sebagian atau tetap tampil dengan ukuran lebih kecil
- **Mobile (sm ke bawah)** — hanya tampil logo dan ikon hamburger, semua link masuk ke mobile menu

Pastikan tidak ada elemen yang overflow atau terpotong di semua ukuran layar.

---

## ✅ Checklist Output

Pastikan semua hal berikut ada dalam output:

- Kode Blade navbar yang sudah dirombak total, bersih dari styling lama
- CSS navbar dengan state transparan, state glassmorphism, transisi scroll, hover effects, dan active state
- JavaScript untuk deteksi scroll dan toggle class state navbar
- JavaScript untuk toggle mobile menu dengan animasi open/close
- Navbar terintegrasi dengan sistem auth Laravel — kondisional antara guest dan user yang sudah login
- Semua komentar kode dalam Bahasa Indonesia
- Responsif sempurna di mobile, tablet, dan desktop
- Tidak ada konflik dengan styling global yang sudah ada

---

## 🎯 Kesan Akhir

> **Navbar yang terasa ringan, premium, dan hidup.**
> Saat pertama scroll, transisinya harus terasa seperti kaca yang perlahan mengembun — smooth, elegan, dan tidak mengejutkan.
