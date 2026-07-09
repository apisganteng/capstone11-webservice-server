<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // membuat data
        $data = [
            [
                'nama' => 'YSL Y EDP',
                'harga'  => 2200000,
                'jumlah' => 10,
                'foto' => 'YSL_Y_EDP.jpg',
                'created_at' => date("Y-m-d H:i:s"),
            ],
            [
                'nama' => 'Le Male Elixir',
                'harga'  =>1500000,
                'jumlah' => 15,
                'foto' => 'Le_Male_Elixir.jpg',
                'created_at' => date("Y-m-d H:i:s"),
            ],
            [
                'nama' => 'Dior Sauvage',
                'harga'  => 2400000,
                'jumlah' => 7,
                'foto' => 'Dior_Sauvage.jpg',
                'created_at' => date("Y-m-d H:i:s"),
            ],
            [
                'nama' => 'Gucci Bloom',
                'harga'  => 2300000,
                'jumlah' => 9,
                'foto' => 'Gucci_Bloom.jpg',
                'created_at' => date("Y-m-d H:i:s"),
            ]
        ];

        foreach ($data as $item) {
            // hindari insert ganda berdasarkan nama produk
            $exists = $this->db->table('product')
                ->where('nama', $item['nama'])
                ->get()
                ->getRowArray();

            if (! $exists) {
                $this->db->table('product')->insert($item);
            }
        }
    }
}