<?php

/**
 * Promo Akhir Tahun Helper
 * Berisi fungsi-fungsi perhitungan promo untuk checkout.
 */

if (!function_exists('hitung_biaya_jasa')) {
    /**
     * Hitung biaya jasa berdasarkan total belanja.
     * - Total <= Rp10.000.000 → 1%
     * - Total >  Rp10.000.000 → 2%
     *
     * @param  float $total_harga Total belanja sebelum promo.
     * @return float Nominal biaya jasa.
     */
    function hitung_biaya_jasa(float $total_harga): float
    {
        if ($total_harga <= 10000000) {
            return $total_harga * 0.01;
        }

        return $total_harga * 0.02;
    }
}

if (!function_exists('hitung_diskon_voucher')) {
    /**
     * Hitung diskon voucher berdasarkan kode voucher.
     * Voucher yang berlaku:
     *   PROMO2025  → 10%
     *   PROMO2026  → 15%
     *   AKHIRTAHUN → 25%
     *
     * Voucher tidak valid atau kosong → diskon 0.
     *
     * @param  float  $total_harga  Total belanja sebelum promo.
     * @param  string $voucher_code Kode voucher dari input pengguna.
     * @return float  Nominal diskon.
     */
    function hitung_diskon_voucher(float $total_harga, string $voucher_code): float
    {
        $vouchers = [
            'PROMO2025'  => 0.10,
            'PROMO2026'  => 0.15,
            'AKHIRTAHUN' => 0.25,
        ];

        $kode = strtoupper(trim($voucher_code));

        if (empty($kode) || !array_key_exists($kode, $vouchers)) {
            return 0;
        }

        return $total_harga * $vouchers[$kode];
    }
}

if (!function_exists('hitung_free_parfum')) {
    /**
     * Hitung nilai hadiah free parfum berdasarkan total belanja.
     *
     * Tingkatan hadiah:
     *   > Rp15.000.000 → Free Parfum Eksklusif 30ml senilai Rp1.500.000
     *   > Rp5.000.000  → Free Parfum Lokal 30ml senilai Rp500.000
     *   Lainnya        → 0
     *
     * @param  float $total_harga Total belanja sebelum promo.
     * @return float Nilai hadiah.
     */
    function hitung_free_parfum(float $total_harga): float
    {
        if ($total_harga > 15000000) {
            return 1500000;
        }

        if ($total_harga > 5000000) {
            return 500000;
        }

        return 0;
    }
}

if (!function_exists('info_free_parfum')) {
    /**
     * Kembalikan label deskriptif hadiah berdasarkan total belanja.
     *
     * @param  float $total_harga
     * @return string Label hadiah, atau string kosong jika tidak ada.
     */
    function info_free_parfum(float $total_harga): string
    {
        if ($total_harga > 15000000) {
            return 'Free Parfum Eksklusif 30ml';
        }

        if ($total_harga > 5000000) {
            return 'Free Parfum Lokal 30ml';
        }

        return '';
    }
}
