<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use App\Services\RajaOngkirService;

use App\Models\TransactionModel;
use App\Models\TransactionDetailModel;

class TransaksiController extends BaseController
{
    protected $cart;
    protected $transactionModel;
    protected $transactionDetailModel;

    public function __construct()
    {
        helper(['number', 'form', 'promo']);
        $this->cart = service('cart');
        $this->transactionModel = new TransactionModel();
        $this->transactionDetailModel = new TransactionDetailModel();
    }

    public function index()
    {
        $data = [
            'items' => $this->cart->contents(),
            'total' => $this->cart->total()
        ];

        return view('v_keranjang', $data);
    }

    public function cart_add()
    {
        // Memasukkan data dari form ke dalam keranjang
        $this->cart->insert([
            'id'      => $this->request->getPost('id'),
            'qty'     => 1,
            'price'   => $this->request->getPost('harga'),
            'name'    => $this->request->getPost('nama'),
            'options' => [
                'foto' => $this->request->getPost('foto')
            ]
        ]);

        // Membuat notifikasi berhasil
        session()->setFlashdata(
            'success',
            'Produk berhasil ditambahkan ke keranjang. <a href="' . base_url('keranjang') . '">Lihat</a>'
        );

        // Kembali ke halaman utama
        return redirect()->to(base_url('/'));
    }

    public function cart_edit()
    {
        $i = 1;
        foreach ($this->cart->contents() as $item) {
            $qty = $this->request->getPost('qty' . $i++);

            $this->cart->update([
                'rowid' => $item['rowid'],
                'qty'   => $qty
            ]);
        }

        session()->setFlashdata('success', 'Keranjang berhasil diperbarui');
        return redirect()->to(base_url('keranjang'));
    }

    public function cart_delete($rowid)
    {
        $this->cart->remove($rowid);
        session()->setFlashdata('success', 'Produk berhasil dihapus dari keranjang');
        return redirect()->to(base_url('keranjang'));
    }

    public function cart_clear()
    {
        $this->cart->destroy();
        session()->setFlashdata('success', 'Keranjang berhasil dikosongkan');
        return redirect()->to(base_url('keranjang'));
    }

    public function checkout()
    {
        // Hitung ringkasan promo berdasarkan total keranjang saat ini
        $cart_total  = (float) $this->cart->total();
        $biaya_jasa  = hitung_biaya_jasa($cart_total);
        $free_parfum = hitung_free_parfum($cart_total);

        $data = [
            'title'          => 'Checkout',
            'cart_items'     => $this->cart->contents(),
            'cart_total'     => $cart_total,
            'biaya_jasa'     => $biaya_jasa,
            'free_parfum'    => $free_parfum,
            'label_hadiah'   => info_free_parfum($cart_total),
            'diskon_voucher' => 0,
        ];

        return view('checkout', $data);
    }

    public function destinations()
    {
        $search  = $this->request->getGet('q');
        $service = new RajaOngkirService();
        $response = $service->getDestination($search);

        $results = [];
        $data    = $response['data'] ?? [];

        foreach ($data as $item) {
            $results[] = [
                'id'   => $item['id'],
                'text' => $item['name']
            ];
        }

        return $this->response->setJSON(['results' => $results]);
    }

    public function costs()
    {
        $origin      = '64999';
        $destination = $this->request->getGet('destination');
        $weight      = '1000';
        $courier     = 'jne';

        $service  = new RajaOngkirService();
        $response = $service->getCost($origin, $destination, $weight, $courier);

        $results = [];
        $data    = $response['data'] ?? [];

        foreach ($data as $item) {
            $results[] = [
                'service'     => $item['service'],
                'description' => $item['description'],
                'cost'        => $item['cost'],
                'etd'         => $item['etd']
            ];
        }

        return $this->response->setJSON(['results' => $results]);
    }

    // ---------------------------------------------------------------
    // buy() — tetap dipertahankan agar route POST buy tidak rusak
    // ---------------------------------------------------------------
    public function buy()
    {
        $cartItems = $this->cart->contents();

        if (empty($cartItems)) {
            return redirect()->back();
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item['qty'] * $item['price'];
        }

        $ongkir = (int) $this->request->getPost('ongkir');

        $transaction = [
            'username'    => $this->request->getPost('username'),
            'alamat'      => $this->request->getPost('alamat'),
            'ongkir'      => $ongkir,
            'total_harga' => $subtotal + $ongkir,
            'status'      => 0,
        ];

        if (!$this->transactionModel->insert($transaction)) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal membuat transaksi');
        }

        $transactionId = $this->transactionModel->getInsertID();

        foreach ($cartItems as $item) {
            $this->transactionDetailModel->insert([
                'transaction_id' => $transactionId,
                'product_id'     => $item['id'],
                'jumlah'         => $item['qty'],
                'diskon'         => 0,
                'subtotal_harga' => $item['qty'] * $item['price']
            ]);
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->back()->with('error', 'Gagal membuat transaksi');
        }

        $this->cart->destroy();
        return redirect()->to(base_url());
    }

    // ---------------------------------------------------------------
    // buat_pesanan() — checkout utama dengan promo & data pengiriman
    // ---------------------------------------------------------------
    public function buat_pesanan()
    {
        // 1. Pastikan keranjang tidak kosong
        if (empty($this->cart->contents())) {
            return redirect()->to(base_url('keranjang'))->with('error', 'Keranjang belanja kosong.');
        }

        // 2. Validasi form data pengiriman (backend)
        $rules = [
            'nama_penerima' => 'required|min_length[3]|max_length[150]',
            'no_hp'         => 'required|min_length[8]|max_length[20]',
            'email'         => 'required|valid_email|max_length[150]',
            'alamat'        => 'required',
            // catatan bersifat opsional — tidak divalidasi
        ];

        $messages = [
            'nama_penerima' => [
                'required'   => 'Nama penerima wajib diisi.',
                'min_length' => 'Nama penerima minimal 3 karakter.',
            ],
            'no_hp' => [
                'required'   => 'Nomor HP wajib diisi.',
                'min_length' => 'Nomor HP minimal 8 digit.',
            ],
            'email' => [
                'required'    => 'Email wajib diisi.',
                'valid_email' => 'Format email tidak valid.',
            ],
            'alamat' => ['required' => 'Alamat lengkap wajib diisi.'],
        ];

        if (!$this->validate($rules, $messages)) {
            // Kembalikan ke checkout dengan error & input lama
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // 3. Hitung subtotal dari keranjang
        $subtotal = (float) $this->cart->total();
        $ongkir   = (float) $this->request->getPost('ongkir');

        // 4. Hitung promo menggunakan helper
        $voucher_code   = $this->request->getPost('voucher_code') ?? '';
        $biaya_jasa     = hitung_biaya_jasa($subtotal);
        $diskon_voucher = hitung_diskon_voucher($subtotal, $voucher_code);
        $free_parfum    = hitung_free_parfum($subtotal);

        // 5. Grand total
        $grand_total = $subtotal + $ongkir + $biaya_jasa - $diskon_voucher - $free_parfum;
        if ($grand_total < 0) {
            $grand_total = 0;
        }

        // 6. Simpan ke database dalam satu transaksi
        $db = \Config\Database::connect();
        $db->transBegin();

        $data_transaksi = [
            // data utama
            'username'    => session()->get('username'),
            'alamat'      => $this->request->getPost('alamat'),
            'ongkir'      => $ongkir,
            'total_harga' => $grand_total,
            'status'      => 'Pending',
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
            // promo
            'biaya_jasa'     => $biaya_jasa,
            'voucher_code'   => strtoupper(trim($voucher_code)),
            'diskon_voucher' => $diskon_voucher,
            'free_parfum'    => $free_parfum,
            // data pengiriman
            'nama_penerima' => $this->request->getPost('nama_penerima'),
            'no_hp'         => $this->request->getPost('no_hp'),
            'email'         => $this->request->getPost('email'),
            'provinsi'      => $this->request->getPost('provinsi'),
            'kota'          => $this->request->getPost('kota'),
            'kecamatan'     => $this->request->getPost('kecamatan'),
            'kode_pos'      => $this->request->getPost('kode_pos'),
            'catatan'       => $this->request->getPost('catatan') ?? '',
        ];

        $db->table('transaction')->insert($data_transaksi);
        $id_transaksi = $db->insertID();

        // 7. Simpan detail produk
        foreach ($this->cart->contents() as $item) {
            $db->table('transaction_detail')->insert([
                'transaction_id' => $id_transaksi,
                'product_id'     => $item['id'],
                'jumlah'         => $item['qty'],
                'diskon'         => 0,
                'subtotal_harga' => $item['qty'] * $item['price'],
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);
        }

        if ($db->transStatus() === false) {
            $db->transRollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memproses pesanan. Silakan coba lagi.');
        }

        $db->transCommit();

        // 8. Kosongkan keranjang dan arahkan ke history
        $this->cart->destroy();
        return redirect()->to(base_url('history'))
            ->with('success', 'Pesanan berhasil dibuat! Terima kasih.');
    }

    public function history()
    {
        $username = session()->get('username');

        $transactions   = $this->transactionModel->where('username', $username)->findAll();
        $transactionIds = array_column($transactions, 'id');

        $products = [];
        if (!empty($transactionIds)) {
            $products = $this->transactionDetailModel->getProductsByTransactionIds($transactionIds);
        }

        $data = [
            'username'     => $username,
            'transactions' => $transactions,
            'products'     => $products
        ];

        return view('v_history', $data);
    }
}
