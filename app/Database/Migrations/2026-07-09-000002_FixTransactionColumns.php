<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migrasi perbaikan:
 * 1. Rename free_mouse → free_parfum
 * 2. Perluas voucher_code menjadi VARCHAR(50)
 * 3. Tambahkan kolom pengiriman yang belum ada
 */
class FixTransactionColumns extends Migration
{
    public function up()
    {
        // ── 1. Rename free_mouse → free_parfum ──────────────────────
        // Cek dulu apakah free_mouse ada dan free_parfum belum ada
        $db     = \Config\Database::connect();
        $fields = $db->getFieldNames('transaction');

        if (in_array('free_mouse', $fields) && !in_array('free_parfum', $fields)) {
            $this->forge->modifyColumn('transaction', [
                'free_mouse' => [
                    'name'    => 'free_parfum',
                    'type'    => 'DOUBLE',
                    'null'    => true,
                    'default' => 0,
                ],
            ]);
        }

        // ── 2. Perluas voucher_code ke VARCHAR(50) ───────────────────
        if (in_array('voucher_code', $fields)) {
            $this->forge->modifyColumn('transaction', [
                'voucher_code' => [
                    'name'       => 'voucher_code',
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'default'    => null,
                ],
            ]);
        }

        // ── 3. Tambahkan kolom pengiriman yang belum ada ─────────────
        $shippingColumns = [
            'nama_penerima' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'default'    => null,
            ],
            'no_hp' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'default'    => null,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'default'    => null,
            ],
            'provinsi' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
            ],
            'kota' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
            ],
            'kecamatan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
            ],
            'kode_pos' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'default'    => null,
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ];

        $toAdd = [];
        foreach ($shippingColumns as $col => $def) {
            if (!in_array($col, $fields)) {
                $toAdd[$col] = $def;
            }
        }

        if (!empty($toAdd)) {
            $this->forge->addColumn('transaction', $toAdd);
        }
    }

    public function down()
    {
        $db     = \Config\Database::connect();
        $fields = $db->getFieldNames('transaction');

        // Kembalikan free_parfum → free_mouse
        if (in_array('free_parfum', $fields) && !in_array('free_mouse', $fields)) {
            $this->forge->modifyColumn('transaction', [
                'free_parfum' => [
                    'name'    => 'free_mouse',
                    'type'    => 'DOUBLE',
                    'null'    => true,
                    'default' => 0,
                ],
            ]);
        }

        // Hapus kolom pengiriman jika ada
        $shipping = ['nama_penerima', 'no_hp', 'email', 'provinsi', 'kota', 'kecamatan', 'kode_pos', 'catatan'];
        $toDrop   = array_intersect($shipping, $fields);
        if (!empty($toDrop)) {
            $this->forge->dropColumn('transaction', array_values($toDrop));
        }
    }
}
