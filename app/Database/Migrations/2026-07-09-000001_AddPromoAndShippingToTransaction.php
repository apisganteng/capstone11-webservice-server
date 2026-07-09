<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tambahkan kolom promo dan pengiriman ke tabel transaction.
 * Migrasi ini idempotent — tidak akan error jika kolom sudah ada.
 */
class AddPromoAndShippingToTransaction extends Migration
{
    public function up()
    {
        $db     = \Config\Database::connect();
        $fields = $db->getFieldNames('transaction');

        // ── Kolom promo ──────────────────────────────────────────────
        $promoColumns = [
            'biaya_jasa' => [
                'type'    => 'DOUBLE',
                'null'    => true,
                'default' => 0,
            ],
            'voucher_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null,
            ],
            'diskon_voucher' => [
                'type'    => 'DOUBLE',
                'null'    => true,
                'default' => 0,
            ],
            'free_parfum' => [
                'type'    => 'DOUBLE',
                'null'    => true,
                'default' => 0,
            ],
        ];

        // ── Kolom pengiriman ─────────────────────────────────────────
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

        $allColumns = array_merge($promoColumns, $shippingColumns);

        // Hanya tambahkan kolom yang belum ada di tabel
        $toAdd = [];
        foreach ($allColumns as $col => $def) {
            if (!in_array($col, $fields)) {
                $toAdd[$col] = $def;
            }
        }

        if (!empty($toAdd)) {
            $this->forge->addColumn('transaction', $toAdd);
        }

        // ── Tangani rename free_mouse → free_parfum ──────────────────
        // (kolom lama dari project sebelumnya)
        $fields = $db->getFieldNames('transaction'); // refresh setelah addColumn
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

        // ── Perluas voucher_code jika masih VARCHAR(20) ───────────────
        $fields = $db->getFieldNames('transaction');
        if (in_array('voucher_code', $fields)) {
            $colMeta = $db->getFieldData('transaction');
            foreach ($colMeta as $col) {
                if ($col->name === 'voucher_code' && (int)$col->max_length < 50) {
                    $this->forge->modifyColumn('transaction', [
                        'voucher_code' => [
                            'name'       => 'voucher_code',
                            'type'       => 'VARCHAR',
                            'constraint' => 50,
                            'null'       => true,
                            'default'    => null,
                        ],
                    ]);
                    break;
                }
            }
        }
    }

    public function down()
    {
        $db     = \Config\Database::connect();
        $fields = $db->getFieldNames('transaction');

        $toDrop = array_intersect(
            ['biaya_jasa', 'voucher_code', 'diskon_voucher', 'free_parfum',
             'nama_penerima', 'no_hp', 'email', 'provinsi', 'kota',
             'kecamatan', 'kode_pos', 'catatan'],
            $fields
        );

        if (!empty($toDrop)) {
            $this->forge->dropColumn('transaction', array_values($toDrop));
        }
    }
}
