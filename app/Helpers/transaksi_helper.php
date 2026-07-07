<?php

if (!function_exists('hitung_ppn')) {
    function hitung_ppn(float $total_harga): float
    {
        return round($total_harga * 0.11, 0);
    }
}

if (!function_exists('hitung_biaya_admin')) {
    function hitung_biaya_admin(float $total_harga): float
    {
        if ($total_harga > 40000000) {
            return round($total_harga * 0.01, 0);
        }

        if ($total_harga > 20000000) {
            return round($total_harga * 0.008, 0);
        }

        return round($total_harga * 0.006, 0);
    }
}

if (!function_exists('hitung_diskon_voucher')) {
    function hitung_diskon_voucher(float $total_harga, ?string $voucher_code): float
    {
        $voucherCode = strtoupper(trim($voucher_code ?? ''));
        $rates = [
            'FLASH10'  => 0.10,
            'FLASH15'  => 0.15,
            'MEMBER20' => 0.20,
        ];

        if (empty($voucherCode) || ! array_key_exists($voucherCode, $rates)) {
            return 0;
        }

        return round($total_harga * $rates[$voucherCode], 0);
    }
}
