<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use App\Models\ProductModel;
use Dompdf\Dompdf;

class ProdukController extends BaseController
{
    protected $productModel; 
    protected $helpers = ['form']; // Helper untuk form_open_multipart

    public function __construct()
    {
        $this->productModel = new ProductModel();
    }

    // 1. TAMPILKAN DAFTAR PRODUK
    public function index()
    {
       return view('produk/index', [
            'products' => $this->productModel->findAll()
        ]);
    }

    // 2. PROSES TAMBAH DATA PRODUK BARU (OLEH ADMIN)
    public function store()
    {
        // Cek Keamanan: Pastikan hanya admin yang bisa nambah produk
        if (session()->get('role') !== 'admin') {
            session()->setFlashdata('failed', 'Hanya admin yang dapat menambahkan produk.');
            return redirect()->to(base_url('produk'));
        }

        $dataFoto = $this->request->getFile('foto');
        
        $dataForm = [
            'nama'   => $this->request->getPost('nama'),
            'harga'  => $this->request->getPost('harga'),
            'jumlah' => $this->request->getPost('jumlah') 
        ];

        // Logika Upload Foto
        if ($dataFoto && $dataFoto->isValid() && ! $dataFoto->hasMoved()) {
            $fileName = $dataFoto->getRandomName(); 
            $dataFoto->move('img/', $fileName);
            $dataForm['foto'] = $fileName;
        } else {
            $dataForm['foto'] = ''; // Default jika tidak ada foto
        }

        // Simpan ke Database
        $this->productModel->insert($dataForm);

        return redirect()->to(base_url('produk'))->with('success', 'Data Berhasil Ditambah');
    } 

    // 3. PROSES UPDATE DATA PRODUK
    public function update($id)
    {
        if (session()->get('role') !== 'admin') {
            session()->setFlashdata('failed', 'Hanya admin yang dapat mengedit produk.');
            return redirect()->to(base_url('produk'));
        }

        $dataProduk = $this->productModel->find($id);
        
        $dataForm = [
            'nama'   => $this->request->getPost('nama'),
            'harga'  => $this->request->getPost('harga'),
            'jumlah' => $this->request->getPost('jumlah') 
        ];

        // Logika update foto (Hapus foto lama jika ada centang checkbox)
        if ($this->request->getPost('check') == 1) {
            if ($dataProduk['foto'] != '' && file_exists("img/" . $dataProduk['foto'])) {
                unlink("img/" . $dataProduk['foto']);
            }

            $dataFoto = $this->request->getFile('foto');
            if ($dataFoto && $dataFoto->isValid() && ! $dataFoto->hasMoved()) {
                $fileName = $dataFoto->getRandomName();
                $dataFoto->move('img/', $fileName);
                $dataForm['foto'] = $fileName;
            } else {
                $dataForm['foto'] = '';
            }
        }

        $this->productModel->update($id, $dataForm);

        return redirect()->to(base_url('produk'))->with('success', 'Data Berhasil Diubah');
    }

    // 4. PROSES HAPUS DATA PRODUK
    public function delete($id)
    {
        if (session()->get('role') !== 'admin') {
            session()->setFlashdata('failed', 'Hanya admin yang dapat menghapus produk.');
            return redirect()->to(base_url('produk'));
        }

        $dataProduk = $this->productModel->find($id);
        
        // Hapus file fisik gambar dari folder img/ sebelum hapus data di database
        if ($dataProduk && $dataProduk['foto'] != '' && file_exists("img/" . $dataProduk['foto'])) {
            unlink("img/" . $dataProduk['foto']);
        }
        
        $this->productModel->delete($id);

        return redirect()->to(base_url('produk'))->with('success', 'Data Berhasil Dihapus');
    }

    // 5. FITUR DOWNLOAD PDF (REKAP PRODUK)
    public function download()
    {
        $products = $this->productModel->findAll();

        $html = view('produk/download_pdf', [
            'products' => $products
        ]);

        $filename = date('Y-m-d-H-i-s') . '-produk.pdf';
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($filename, [
            'Attachment' => true
        ]);
    }

    // 6. FITUR TAMBAH KE KERANJANG (CUSTOMER)
    // Fungsi ini dipakai saat user menekan tombol "Beli" pada card produk
    public function tambah($id)
    {
        if (session()->get('role') !== 'customer') {
            session()->setFlashdata('failed', 'Hanya customer dapat menambahkan produk ke keranjang.');
            return redirect()->to(base_url('produk'));
        }

        $product = $this->productModel->find($id);
        if (! $product) {
            session()->setFlashdata('failed', 'Produk tidak ditemukan.');
            return redirect()->to(base_url('produk'));
        }

        $cart = session()->get('cart') ?? [];

        if (isset($cart[$id])) {
            $cart[$id]['qty'] += 1;
            $cart[$id]['subtotal'] = $cart[$id]['harga'] * $cart[$id]['qty'];
        } else {
            $cart[$id] = [
                'id'       => $product['id'],
                'nama'     => $product['nama'], 
                'harga'    => $product['harga'], 
                'qty'      => 1,
                'subtotal' => $product['harga'],
            ];
        }

        session()->set('cart', $cart);
        session()->setFlashdata('success', 'Produk berhasil ditambahkan ke keranjang.');

        return redirect()->to(base_url('produk'));
    }
}