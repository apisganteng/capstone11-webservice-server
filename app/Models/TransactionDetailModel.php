<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionDetailModel extends Model
{
    protected $table            = 'transaction_detail'; //disesuaikan
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true; //disesuaikan
    protected $protectFields    = true;
    protected $allowedFields    = ['transaction_id', 'product_id', 'quantity', 'price']; //disesuaikan

    public function getProductsByTransactionIds($transactionIds)
    {
        // Kalau nggak ada transaksi, langsung balikin array kosong
        if (empty($transactionIds)) {
            return [];
        }

        // Ambil detail transaksi dan di-JOIN sama tabel produk biar dapet foto & nama
        $builder = $this->db->table($this->table);
        $builder->select('transaction_detail.*, product.nama, product.harga, product.foto'); 
        $builder->join('product', 'product.id = transaction_detail.product_id');
        $builder->whereIn('transaction_detail.transaction_id', $transactionIds);
        
        $results = $builder->get()->getResultArray();

        // Kelompokkin data berdasarkan transaction_id biar gampang dibaca sama View lu
        $groupedProducts = [];
        foreach ($results as $row) {
            $groupedProducts[$row['transaction_id']][] = $row;
        }

        return $groupedProducts;
    }

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true; //disesuaikan
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = true; //disesuaikan
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
