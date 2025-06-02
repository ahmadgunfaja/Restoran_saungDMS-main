<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsKoki
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  \Closure  $next
     * @return mixed
     */
     public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('kokis')->user();

        // Cek apakah status koki adalah approved
        if ($user && $user->status === 'approved') {
            return $next($request);
        }

        // Jika koki belum disetujui, redirect ke halaman yang sesuai (misalnya login)
        return redirect()->route('koki.login')->with('error', 'Akun Anda belum disetujui oleh admin.');
    }
}
