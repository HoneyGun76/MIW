<?php
// terbilang.php

function terbilang($x, $currency = 'IDR') {
    // Convert to integer first to avoid float precision issues
    $x = (int)round($x);
    
    // Handle zero case
    if ($x === 0) {
        return $currency === 'USD' ? 'Nol Dolar' : 'Nol Rupiah';
    }
    
    $angka = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
    
    if ($x < 12) {
        $result = $angka[$x];
    } elseif ($x < 20) {
        $result = terbilang_no_currency($x - 10) . " Belas";
    } elseif ($x < 100) {
        $result = terbilang_no_currency((int)($x / 10)) . " Puluh" . 
                 ($x % 10 > 0 ? " " . terbilang_no_currency($x % 10) : "");
    } elseif ($x < 200) {
        $result = "Seratus" . ($x - 100 > 0 ? " " . terbilang_no_currency($x - 100) : "");
    } elseif ($x < 1000) {
        $result = terbilang_no_currency((int)($x / 100)) . " Ratus" . 
                 ($x % 100 > 0 ? " " . terbilang_no_currency($x % 100) : "");
    } elseif ($x < 2000) {
        $result = "Seribu" . ($x - 1000 > 0 ? " " . terbilang_no_currency($x - 1000) : "");
    } elseif ($x < 1000000) {
        $result = terbilang_no_currency((int)($x / 1000)) . " Ribu" . 
                 ($x % 1000 > 0 ? " " . terbilang_no_currency($x % 1000) : "");
    } elseif ($x < 1000000000) {
        $result = terbilang_no_currency((int)($x / 1000000)) . " Juta" . 
                 ($x % 1000000 > 0 ? " " . terbilang_no_currency($x % 1000000) : "");
    } elseif ($x < 1000000000000) {
        $result = terbilang_no_currency((int)($x / 1000000000)) . " Milyar" . 
                 ($x % 1000000000 > 0 ? " " . terbilang_no_currency($x % 1000000000) : "");
    } else {
        return $currency === 'USD' ? 'Jumlah terlalu besar Dolar' : 'Jumlah terlalu besar Rupiah';
    }
    
    // Clean up any double spaces and trim
    $result = preg_replace('/\s+/', ' ', trim($result));
    
    return $currency === 'USD' ? $result . ' Dolar' : $result . ' Rupiah';
}

// Helper function without currency suffix for internal recursive calls
function terbilang_no_currency($x) {
    $angka = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
    
    if ($x < 12) {
        return $angka[$x];
    } elseif ($x < 20) {
        return terbilang_no_currency($x - 10) . " Belas";
    } elseif ($x < 100) {
        return terbilang_no_currency((int)($x / 10)) . " Puluh" . 
               ($x % 10 > 0 ? " " . terbilang_no_currency($x % 10) : "");
    } elseif ($x < 200) {
        return "Seratus" . ($x - 100 > 0 ? " " . terbilang_no_currency($x - 100) : "");
    } elseif ($x < 1000) {
        return terbilang_no_currency((int)($x / 100)) . " Ratus" . 
               ($x % 100 > 0 ? " " . terbilang_no_currency($x % 100) : "");
    } elseif ($x < 2000) {
        return "Seribu" . ($x - 1000 > 0 ? " " . terbilang_no_currency($x - 1000) : "");
    } elseif ($x < 1000000) {
        return terbilang_no_currency((int)($x / 1000)) . " Ribu" . 
               ($x % 1000 > 0 ? " " . terbilang_no_currency($x % 1000) : "");
    } elseif ($x < 1000000000) {
        return terbilang_no_currency((int)($x / 1000000)) . " Juta" . 
               ($x % 1000000 > 0 ? " " . terbilang_no_currency($x % 1000000) : "");
    } elseif ($x < 1000000000000) {
        return terbilang_no_currency((int)($x / 1000000000)) . " Milyar" . 
               ($x % 1000000000 > 0 ? " " . terbilang_no_currency($x % 1000000000) : "");
    }
    
    return "Jumlah terlalu besar";
}
?>