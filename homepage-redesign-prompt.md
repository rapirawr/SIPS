# 🏠 Prompt: Homepage Redesign Laravel — Hero 3D Three.js + Premium Typography

---

## 🧠 Peran & Tujuan

Kamu adalah senior frontend developer dan UI/UX designer kelas dunia yang spesialis membangun interface Laravel yang premium dan beranimasi. Tugasmu adalah merombak total UI homepage menjadi tampilan modern, futuristik, dan berkesan — bukan template biasa. Setiap keputusan desain harus terasa disengaja, bukan hasil copy-paste template.

---

## 🎯 Konteks Project

| Field               | Value                                                     |
| ------------------- | --------------------------------------------------------- |
| **Nama Project**    | `SIPS`                                                    |
| **Deskripsi**       | `Sistem Informasi Pengaduan Sarana dan Prasarana Sekolah` |
| **Stack**           | Laravel Blade + Tailwind CSS + Vanilla JS                 |
| **Target Pengguna** | `Siswa, Guru, dan Staf Sekolah`                           |
| **Tema**            | Modern dengan sentuhan glassmorphism                      |

---

## 📁 Struktur Output

Pisahkan output ke file-file berikut sesuai konvensi Laravel:

- `resources/views/home.blade.php` — Struktur HTML utama
- `resources/css/home.css` — Semua styling custom
- `resources/js/three-hero.js` — Logika Three.js untuk hero section
- `resources/js/animations.js` — Scroll reveal, typewriter, animasi lainnya

Gunakan `@push('scripts')` dan `@push('styles')` untuk inject asset di Blade. Semua komentar kode wajib dalam Bahasa Indonesia.

---

## 🖥️ Hero Section — 3D dengan Three.js

Buat hero section fullscreen dengan canvas Three.js sebagai background. Canvas diposisikan di belakang konten teks menggunakan z-index. Gunakan Three.js versi r128 via CDN.

**Objek 3D:**
Pilih objek yang paling relevan dengan tema project — bisa berupa geometri bola mengkilap, rumah low-poly yang berputar, sistem partikel mengambang, atau floating cube. Objek harus berputar otomatis secara perlahan dan halus.

**Pencahayaan:**
Gunakan kombinasi ambient light dan point light berwarna senada dengan palet warna utama untuk menciptakan kesan dramatis dan tiga dimensi.

**Interaksi Mouse:**
Objek 3D mengikuti arah kursor secara halus menggunakan lerp (linear interpolation) — terasa responsif tapi tidak kaku. Jika mouse tidak bergerak selama beberapa detik, objek kembali berotasi otomatis.

**Konten Teks Hero:**

- Eyebrow label kecil di atas headline
- Headline utama berukuran sangat besar dan bold
- Satu atau dua kata dalam headline diberi treatment khusus (warna accent atau italic)
- Subheadline singkat dan elegan
- Satu tombol CTA yang mengarah ke halaman login atau dashboard

---

## ✍️ Tipografi — Premium & Ekspresif

### Filosofi

Typography bukan sekadar font — ini adalah suara visual brand. Hindari font generik seperti Arial, Roboto, atau Inter. Pilih font yang punya karakter kuat dan tidak biasa. Pasangkan satu font display untuk heading dengan satu font body untuk teks panjang.

### Pilihan Font

Pilih **satu** kombinasi dari Google Fonts berikut dan terapkan konsisten di seluruh halaman:

- **Opsi A — Futuristik & Tegas:** Syne (display) + DM Sans (body)
- **Opsi B — Editorial & Elegan:** Fraunces (display) + Outfit (body)
- **Opsi C — Modern Geometric:** Raleway (display) + Plus Jakarta Sans (body)

### Skala Tipografi

Gunakan fluid typography dengan `clamp()` agar otomatis responsif di semua ukuran layar tanpa media query tambahan. Tentukan skala dari teks terkecil (caption/label) hingga terbesar (hero headline). Hero headline harus benar-benar besar dan dominan — jangan tanggung.

### Hierarki Elemen

Terapkan hierarki tipografi yang jelas untuk setiap elemen berikut:

- **Eyebrow** — huruf kecil, spasi antar huruf lebar, uppercase, warna accent
- **Hero Headline** — font display, ukuran maksimal, weight paling berat, letter-spacing negatif, efek gradient text
- **Kata Kunci Disorot** — satu-dua kata dalam headline diberi warna berbeda atau italic, dengan animasi garis bawah yang muncul masuk dari kiri
- **Lead / Subheadline** — font body, weight tipis, warna redup, line-height longgar
- **Section Heading** — font display, ukuran besar, selalu dipasangkan dengan eyebrow di atasnya
- **Card Title & Body** — hierarki jelas antara judul card dan isi deskripsinya
- **Angka / Statistik** — font display, ukuran sangat besar, menggunakan tabular numbers agar sejajar
- **Navigasi** — uppercase, spasi antar huruf sedang, ukuran kecil
- **Tombol CTA** — uppercase, weight semi-bold, spasi antar huruf sedikit
- **Ghost Text Dekoratif** — teks besar transparan sebagai watermark background, tidak bisa diklik

### Efek Tipografi Spesial

Implementasikan efek-efek berikut di `animations.js`:

- **Char Split Reveal** — headline hero muncul per huruf secara berurutan dengan animasi slide-up, menciptakan kesan dramatis saat halaman pertama dibuka
- **Typewriter Effect** — subheadline atau satu baris teks berganti-ganti frasa dengan animasi ketik dan hapus secara loop
- **Gradient Text Bergerak** — gradient pada teks bergerak mengalir dari kiri ke kanan secara terus-menerus
- **Animated Underline** — garis bawah pada kata kunci muncul dengan animasi scale dari kiri ke kanan

---

## ✨ Animasi Halaman

### Navbar

Saat halaman baru dibuka, navbar transparan. Saat pengguna scroll ke bawah, navbar berubah menjadi frosted glass dengan backdrop blur dan sedikit border bawah. Transisi harus halus.

### Scroll Reveal

Setiap section dan card muncul dengan animasi fade-in + slide-up saat masuk ke viewport menggunakan IntersectionObserver. Card dalam grid muncul bertahap dengan delay berbeda per card, menciptakan efek cascade yang elegan.

### Card Hover

Card sedikit terangkat ke atas saat di-hover disertai shadow yang lebih dalam dan berwarna senada dengan accent. Transisi halus dan tidak terlalu cepat.

### Background Ambient

Background hero menggunakan radial gradient berlapis yang sedikit bergerak atau bernapas secara lambat — menciptakan kedalaman dan atmosfer tanpa mengganggu keterbacaan teks.

### Page Load

Saat halaman pertama dibuka, ada urutan animasi yang terasa orkestrasi: navbar muncul, lalu objek 3D fade-in, lalu teks headline muncul per huruf, lalu subheadline dan CTA muncul bertahap. Keseluruhannya terasa seperti sebuah "pembukaan" yang dramatis.

---

## 🎨 Panduan Visual

### Warna

- Background utama: hampir hitam dengan sedikit nuansa biru atau ungu
- Warna primary: ungu elektrik atau cyan
- Accent: warna kontras yang membuat elemen penting menonjol
- Teks: putih untuk heading, putih redup untuk body text

### Glassmorphism

Card dan elemen UI menggunakan efek kaca — background semi-transparan, backdrop blur, dan border tipis hampir transparan. Efek ini hanya bekerja baik di atas background yang berwarna, bukan putih polos.

### Depth & Layer

Buat kesan kedalaman dengan memainkan opacity, blur, dan ukuran elemen. Elemen yang lebih dekat lebih opaque dan tajam, elemen dekoratif di background lebih transparan dan blur.

---

## 📐 Struktur Halaman

Halaman terdiri dari section-section berikut secara berurutan:

1. **Navbar** — sticky, transparan saat atas, glassmorphism saat scroll
2. **Hero** — fullscreen, canvas Three.js di background, teks dan CTA di depan
3. **Stats** — angka-angka besar yang menarik perhatian
4. **Features** — card fitur utama dalam grid
5. **How It Works** — langkah-langkah penggunaan dengan visual yang jelas
6. **CTA Bottom** — ajakan dengan headline besar dan gradient dramatis
7. **Footer** — minimalis, rapi, tipografi kecil tertata

---

## ✅ Checklist Output

Pastikan semua hal berikut ada dalam output:

- File `home.blade.php` dengan struktur HTML lengkap dan semantic
- File `home.css` dengan semua variabel font, skala tipografi, dan styling
- File `three-hero.js` dengan scene Three.js, pencahayaan, mouse parallax, dan auto-rotate
- File `animations.js` dengan char split, typewriter, scroll reveal, dan navbar scroll behavior
- Semua komentar kode dalam Bahasa Indonesia
- Tipografi responsif otomatis tanpa media query tambahan
- Semua animasi berjalan smooth di 60fps

---

## 🎯 Kesan Akhir

> **Premium. Futuristik. Terasa seperti produk sungguhan, bukan template.**
> Orang yang membuka halaman ini untuk pertama kali harus berhenti sejenak dan berkata: _"Wah."_
