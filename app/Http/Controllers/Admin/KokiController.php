<?php

namespace App\Http\Controllers\Admin;

use App\Models\Koki;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class KokiController extends Controller
{
    /**
     * Menampilkan daftar koki yang masih menunggu persetujuan admin.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch all kokis or apply any filters you need
        $kokis = Koki::all();

        // Return the view with the kokis data
        return view('admin.koki.index', compact('kokis'));
    }
    public function create()
    {
        return view('admin.koki.create');
    }

    // Store a newly created Koki in the database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:kokis,email',
            'no_hp' => 'required|string|max:15',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create the Koki (chef)
        Koki::create([
            'name' => $request->name,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'password' => bcrypt($request->password),  // Hash the password
            'status' => 'pending',  // Default status
        ]);

        // Redirect back with a success message
        return redirect()->route('admin.koki.index')->with('status', 'Koki created successfully!');
    }
    public function pendingKoki()
    {
        // Ambil koki yang statusnya 'pending'
        $kokis = Koki::where('status', 'pending')->get();

        // Kirim data koki ke tampilan
        return view('admin.koki.pending', compact('kokis'));
    }

    /**
     * Menyetujui koki yang baru mendaftar.
     *
     * @param  \App\Models\Koki  $koki
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approveKoki($id)
    {
        $koki = Koki::find($id);
        if ($koki) {
            $koki->status = 'active';  // Mengubah status menjadi approved
            $koki->save();
            return redirect()->route('admin.koki.index')->with('success', 'Koki berhasil disetujui.');
        }
        return redirect()->route('admin.koki.index')->with('error', 'Koki tidak ditemukan.');
    }

    /**
     * Menolak koki yang baru mendaftar.
     *
     * @param  \App\Models\Koki  $koki
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rejectKoki(Koki $koki)
    {
        // Update status koki menjadi 'rejected'
        $koki->update(['status' => 'rejected']);

        // Redirect ke halaman daftar koki yang pending
        return redirect()->route('admin.koki.index')->with('status', 'Koki ditolak!');
    }

    public function edit($id)
    {
        $koki = Koki::findOrFail($id);
        return view('admin.koki.edit', compact('koki'));
    }

    public function update(Request $request, $id)
    {
        $koki = Koki::findOrFail($id);
        $koki->update([
            'name' => $request->name,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            // Pastikan Anda tidak mengubah status di sini
        ]);
        return redirect()->route('admin.koki.index')->with('success', 'Koki berhasil diperbarui');
    }

    public function destroy($id)
    {
        $koki = Koki::findOrFail($id);
        $koki->delete();
        return redirect()->route('admin.koki.index')->with('danger', 'Koki berhasil dihapus');
    }
}
