<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class KeranjangController extends BaseController
{
    public function index()
    {
        if (session()->get('role') !== 'customer') {
            return redirect()->to(base_url('produk'));
        }

        return view('v_keranjang');
    }
}
