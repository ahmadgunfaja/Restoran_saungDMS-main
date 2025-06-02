# Restoran Saung DMS - Sistem Pemesanan & Pembayaran Online

## Overview
Sistem ini adalah aplikasi restoran modern berbasis Laravel yang mendukung pemesanan menu, checkout, pembayaran online (Tripay), monitoring transaksi admin, dan transparansi biaya (pajak, fee channel, dsb). Sistem didesain agar user dan admin dapat memantau status pesanan dan pembayaran secara real-time, serta mudah diintegrasikan dengan channel pembayaran digital.

---

## Fitur Utama
- **Pemesanan Menu & Checkout**: User dapat memilih menu, mengisi data diri, memilih meja, dan melakukan checkout.
- **Perhitungan Otomatis**: Subtotal, pajak 10%, fee channel pembayaran (flat & persen) dihitung otomatis dan transparan di UI.
- **Integrasi Tripay**: Pembayaran digital dengan berbagai channel (QRIS, bank, e-wallet, dsb) via Tripay. Payload ke Tripay sudah sesuai requirement terbaru.
- **Instruksi Pembayaran**: Instruksi pembayaran dan QRIS tampil otomatis di summary order.
- **Monitoring Transaksi Admin**: Admin dapat memantau semua transaksi, status pembayaran, dan detail order.
- **Keamanan**: Validasi signature callback Tripay, pengecualian CSRF hanya untuk callback, dan tidak ada credential di codebase.
- **Service Layer**: Logic pembayaran dan callback terpisah di service agar controller clean dan maintainable.

---

## Alur Penggunaan (User)
1. **Pilih Menu**: User memilih menu dan menambahkannya ke cart.
2. **Checkout**: User mengisi data diri, memilih meja, dan memilih channel pembayaran.
3. **Summary Order**: User melihat ringkasan pesanan (subtotal, pajak, fee, total bayar, instruksi pembayaran, QRIS jika channel QRIS).
4. **Pembayaran**: User klik "Lanjut Pembayaran" dan diarahkan ke Tripay/QRIS. Setelah bayar, status order otomatis update.
5. **Cek Status**: User dapat melihat daftar pesanan dan status pembayaran di halaman "Daftar Pesanan".

---

## Alur Admin
1. **Login Admin**: Admin login ke dashboard admin.
2. **Monitoring Transaksi**: Admin dapat melihat semua transaksi, filter, dan detail pembayaran di menu transaksi.
3. **Validasi & Update**: Status order dan pembayaran otomatis update dari callback Tripay.

---

## Flow Teknis
- **Checkout**: Semua perhitungan subtotal, pajak, fee dilakukan di backend dan frontend. Payload ke Tripay berisi order_items (termasuk pajak sebagai item), amount = subtotal+pajak.
- **Tripay Integration**: Signature dibuat dengan private key, payload dikirim ke Tripay, response disimpan di tabel payment_transactions.
- **Callback Tripay**: Endpoint `/callback/tripay` menerima notifikasi, validasi signature, update status order & transaksi.
- **QRIS**: Jika channel QRIS, QR code dari Tripay (`qris_url`) atau hasil generate lokal (`qris_screenshot`) akan tampil di summary.
- **Keamanan**: CSRF hanya di-exclude untuk callback, route admin pakai middleware auth, signature pakai config.

---

## Struktur Folder Penting
- `app/Http/Controllers/` : Controller utama (Menu, Tripay, Callback, Admin)
- `app/Services/` : Service layer untuk Tripay & pembayaran
- `app/Models/` : Model Eloquent (Order, OrderItem, PaymentTransaction)
- `resources/views/menus/` : Blade untuk checkout & summary
- `resources/views/admin/` : Blade untuk monitoring transaksi admin
- `routes/web.php` : Semua route utama
- `config/tripay.php` : Konfigurasi Tripay

---

## Catatan Teknis
- **Fee channel**: Dikirim dari frontend (hidden input) dan dihitung ulang di backend.
- **Order_items**: Pajak dikirim sebagai item terpisah agar payload Tripay valid.
- **Error Handling**: Semua proses order pakai DB transaction, error akan rollback otomatis.
- **QRIS**: Jika response Tripay ada `qris_url`, QR Tripay akan tampil. Jika tidak, fallback ke QR lokal.

---

## Cara Menjalankan
1. `composer install`
2. `cp .env.example .env` lalu isi konfigurasi DB & Tripay
3. `php artisan migrate`
4. `php artisan serve`
5. Akses di browser: `http://localhost:8000`

---

## FAQ
- **Kenapa instruksi pembayaran/QRIS tidak muncul?**
  - Pastikan channel pembayaran mendukung instruksi/QRIS dan response Tripay sudah lengkap.
- **Bagaimana jika status pembayaran tidak update?**
  - Cek endpoint callback Tripay, pastikan signature valid dan route tidak kena middleware auth/CSRF.
- **Bagaimana menambah channel pembayaran?**
  - Cukup aktifkan di Tripay, sistem akan otomatis menampilkan channel yang tersedia.

---

## Support
Jika ada pertanyaan teknis, silakan hubungi developer/support yang tertera di dokumentasi internal.

---

## [2025-06-02] Perubahan & Perbaikan Terbaru

### 1. Sinkronisasi Perhitungan Amount & Order Items (Order Biasa & Reservasi)
- Jika order reservasi, data menu diambil dari `order->menu_items` (bukan cart), sehingga perhitungan subtotal, pajak, dan amount tetap benar.
- Logic ini otomatis di service Tripay, sehingga amount dan payload Tripay selalu valid untuk order biasa maupun reservasi.
- Order items (termasuk pajak/TAX10) selalu diinsert ke tabel order_items, baik order baru maupun reservasi.

### 2. Update Field Amount Setelah Response Tripay
- Field `amount` di tabel order diupdate setelah menerima response dari Tripay (`$transaction['data']['amount']`), agar selalu sinkron dengan nominal yang harus dibayar user.
- Jika ada fee channel, pajak, dsb, total amount yang tampil di UI dan database sudah sesuai dengan real amount dari Tripay.

### 3. Breakdown Pembayaran Transparan di UI
- Halaman thankyou dan summary order kini menampilkan breakdown: subtotal, pajak, fee channel, dan total bayar.
- Data diambil langsung dari order_items dan payment_transaction, sehingga user/admin bisa audit detail pembayaran dengan mudah.

### 4. Perbaikan Robustness
- Insert order_items selalu dihapus dulu sebelum insert ulang, agar tidak terjadi duplikasi jika proses dipanggil ulang.
- Logic perhitungan amount dan payload Tripay sudah robust untuk semua flow (order biasa & reservasi).
