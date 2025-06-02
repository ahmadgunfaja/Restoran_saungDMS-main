<?php

namespace App\Http\Controllers\Koki;  

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KokiController extends Controller
{
    // Menampilkan dashboard koki
    public function dashboard()
    {
        // Ambil semua menu yang tersedia
        $menus = Menu::all();  // Sesuaikan dengan kebutuhan Anda, bisa ditambahkan filter atau paginasi

        return view('koki.dashboard', compact('menus'));
    }
}
