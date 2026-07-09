<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class ProfileController extends BaseController
{
    public function index()
    {
        // Ambil semua data yang dibutuhkan dari session
        $data = [
            'username'     => session()->get('username'),
            'role'         => session()->get('role'),
            'email'        => session()->get('email'),
            'waktu_login'  => session()->get('waktu_login'),
            'status_login' => session()->get('isLoggedIn') ? 'Sudah Login' : 'Belum Login',
        ];

        // Tampilkan view profil dengan layout NiceAdmin
        return view('v_profile', $data);
    }
}
