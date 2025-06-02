<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\KokiController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\PaymentTransactionController;
use App\Http\Controllers\Koki\KokiController as KokiKokiController;
use App\Http\Controllers\Koki\MenuController;
use App\Http\Controllers\Koki\CategoryController;
use App\Http\Controllers\Koki\KokiAuthController as ControllersKokiAuthController;
use App\Http\Controllers\Frontend\CategoryController as FrontendCategoryController;
use App\Http\Controllers\Frontend\MenuController as FrontendMenuController;
use App\Http\Controllers\Frontend\ReservationController as FrontendReservationController;
use App\Http\Controllers\Frontend\PaymentController as FrontendPaymentController;
use App\Http\Controllers\Frontend\BlogController as FrontendBlogController;
use App\Http\Controllers\Frontend\WelcomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TripayCallbackController;
use App\Http\Controllers\TripayController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ================= ADMIN LOGIN =================
Route::get('/admin/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
// ================= END ADMIN LOGIN =============

// ================= KOKI LOGIN ==================
Route::get('koki/login', [ControllersKokiAuthController::class, 'showLoginForm'])->name('koki.login');
Route::post('koki/login', [ControllersKokiAuthController::class, 'login']);
Route::post('koki/logout', [ControllersKokiAuthController::class, 'logout'])->name('koki.logout');
// ================= END KOKI LOGIN ==============

Route::get('/', [WelcomeController::class, 'index']);

Route::get('/categories', [FrontendCategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category}', [FrontendCategoryController::class, 'show'])->name('categories.show');
// Step 1: Reservasi Detail
Route::get('/reservation/step-one', [FrontendReservationController::class, 'stepOne'])->name('reservations.step-one');
Route::post('/reservation/step-one', [FrontendReservationController::class, 'storeStepOne'])->name('reservations.store.step-one');

// Step 2: Memilih Meja & Opsi Memesan Menu
// Step 2: Memilih Meja & Opsi Memesan Menu
Route::get('/reservation/step-two', [FrontendReservationController::class, 'stepTwo'])->name('reservations.step-two');
Route::post('/reservation/step-two', [FrontendReservationController::class, 'storeStepTwo'])->name('reservations.store.step-two');

// Step 3: Memilih Menu
Route::get('reservations/step-three', [FrontendReservationController::class, 'stepThree'])->name('reservations.step-three');
Route::post('reservations/step-three', [FrontendReservationController::class, 'storeStepThree'])->name('reservations.store.step-three');;

// Step 4: Pembayaran
Route::get('reservations/step-four', [FrontendReservationController::class, 'stepFour'])->name('reservations.step-four');
Route::post('reservations/step-four', [FrontendReservationController::class, 'storeStepFour'])->name('reservations.store.step-four');;

// Display a detailed blog post
Route::get('/detail/{slug}', [FrontendBlogController::class, 'show'])->name('blog.detail');
Route::get('/blog', [FrontendBlogController::class, 'index'])->name('blog.index');
//Route::get('/blog', [FrontendBlogController::class, 'index'])->name('blog.index');

// Ubah route thankyou agar pakai order id
Route::get('/thankyou/{order}', [FrontendReservationController::class, 'thankyou'])->name('thankyou');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth', 'admin'])->name('admin.')->prefix('admin')->group(function () {

    //dashboard
    Route::get('/', [AdminController::class, 'index'])->name('index');

    //table
    Route::resource('/tables', TableController::class);

    //reservation
    Route::resource('/reservations', ReservationController::class);

    // Route::post('/admin/orders/{orderId}/validate-payment', [FrontendPaymentController::class, 'validatePayment'])->name('admin.validatePayment');
    // Route::middleware('admin')->get('/admin/orders', [FrontendPaymentController::class, 'showAdminOrders'])->name('admin.orders');

    //oreder
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::resource('/admin/orders', AdminOrderController::class);
    Route::get('/orders/{order}/edit', [AdminOrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{order}', [AdminOrderController::class, 'update'])->name('admin.orders.update');
    Route::delete('/orders/{order}', [AdminOrderController::class, 'destroy'])->name('admin.orders.destroy');

    //koki
    Route::get('/koki/pending', [KokiController::class, 'pendingKoki'])->name('koki.pending');
    Route::post('/koki/{koki}/approve', [KokiController::class, 'approveKoki'])->name('koki.approve');
    Route::post('/koki/{koki}/reject', [KokiController::class, 'rejectKoki'])->name('koki.reject');
    Route::get('/koki', [KokiController::class, 'index'])->name('koki.index');
    Route::get('/koki/create', [KokiController::class, 'create'])->name('koki.create');
    Route::post('/koki', [KokiController::class, 'store'])->name('koki.store');
    Route::get('/koki/{koki}/edit', [KokiController::class, 'edit'])->name('koki.edit');
    Route::put('/koki/{koki}', [KokiController::class, 'update'])->name('koki.update');
    Route::delete('/koki/{koki}', [KokiController::class, 'destroy'])->name('koki.destroy');

    //blog
    Route::resource('blog', BlogController::class);
    Route::get('blogs', [BlogController::class, 'index'])->name('blog');

    //transactions
    Route::get('/transactions', [PaymentTransactionController::class, 'index'])->name('transactions.index');
});


Route::get('koki/dashboard', [KokiKokiController::class, 'dashboard'])->name('koki.dashboard');

Route::resource('koki/menus', MenuController::class)->names([

    'create' => 'koki.menus.create',
    'store' => 'koki.menus.store',
    'show' => 'koki.menus.show',
    'edit' => 'koki.menus.edit',
    'update' => 'koki.menus.update',
    'destroy' => 'koki.menus.destroy',
]);
Route::get('koki/menus', [MenuController::class, 'index'])->name('koki.menus.index');

Route::resource('koki/categories', CategoryController::class)->names([

    'create' => 'koki.categories.create',
    'store' => 'koki.categories.store',
    'show' => 'koki.categories.show',
    'edit' => 'koki.categories.edit',
    'update' => 'koki.categories.update',
    'destroy' => 'koki.categories.destroy',
]);
Route::get('koki/categories', [CategoryController::class, 'index'])->name('koki.categories.index');

//order
Route::get('/menus', [FrontendMenuController::class, 'index'])->name('menus.index');
Route::post('/menus/{menuId}/add-to-cart', [FrontendMenuController::class, 'addToCart'])->name('menus.addToCart');
Route::get('/checkout', [FrontendMenuController::class, 'checkout'])->name('menus.checkout');
Route::post('/menus/{menuId}/update-quantity', [FrontendMenuController::class, 'updateQuantity'])->name('menus.updateQuantity');
Route::post('/checkout', [TripayController::class, 'placeOrder'])->name('menus.placeOrder');
Route::post('/menus/{menuId}/remove-from-cart', [FrontendMenuController::class, 'removeFromCart'])->name('menus.removeFromCart');
//Route::get('/qris-payment', [FrontendMenuController::class, 'qrisPayment'])->name('menus.qrisPayment');
// Payment Routes
Route::get('/payment/qris', [FrontendPaymentController::class, 'processQrisPayment'])->name('payment.qris');
Route::get('/payment/qris/{orderId}', [FrontendPaymentController::class, 'qrisPayment'])->name('payment.qris.order');
Route::get('/payment/status', [FrontendMenuController::class, 'checkPaymentStatus'])->name('payment.status');
Route::post('/payment/upload/{orderId}', [FrontendPaymentController::class, 'uploadQrisScreenshot'])->name('menus.uploadQrisScreenshot');
// Route::post('/payment/upload', [FrontendPaymentController::class, 'uploadQrisScreenshot'])->name('menus.uploadQrisScreenshot');

//login koki
Route::get('koki/register', [ControllersKokiAuthController::class, 'showRegistrationForm'])->name('koki.register');
Route::post('koki/register', [ControllersKokiAuthController::class, 'register']);

// Route untuk proses pembayaran order dengan Tripay
Route::get('/payment/order/{orderId}', [FrontendPaymentController::class, 'payOrder'])->name('payment.order');
// Route untuk menerima callback/notification dari Tripay
Route::post('/payment/tripay-callback', [FrontendPaymentController::class, 'tripayCallback'])->name('payment.tripay.callback');
// Route untuk halaman sukses/gagal pembayaran
Route::get('/payment/success', [FrontendPaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/failed', [FrontendPaymentController::class, 'failed'])->name('payment.failed');

// Route untuk mendapatkan daftar payment channel Tripay
Route::get('/payment/tripay/channels', [FrontendPaymentController::class, 'tripayChannels'])->name('payment.tripay.channels');

// Route untuk menampilkan daftar order user
Route::get('/orders', [\App\Http\Controllers\Frontend\MenuController::class, 'orders'])->name('menus.orders');

// Route untuk menampilkan summary order setelah checkout
Route::get('/order/summary/{order}', [\App\Http\Controllers\Frontend\MenuController::class, 'summary'])->name('menus.summary');

Route::post('/callback/tripay', [TripayCallbackController::class, 'handle'])->name('payment.callback');
require __DIR__ . '/auth.php';
