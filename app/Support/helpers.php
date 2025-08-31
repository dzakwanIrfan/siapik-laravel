<?php

use Carbon\CarbonImmutable;

if (! function_exists('tahun_akademik')) {
    /**
     * Ubah tahun (mis. 2022) menjadi "2022/2023".
     */
    function tahun_akademik($startYear, string $separator = '/'): ?string
    {
        if ($startYear === null || $startYear === '') {
            return null;
        }

        // Ambil 4 digit pertama yang mirip tahun
        if (preg_match('/\d{4}/', (string) $startYear, $m)) {
            $y = (int) $m[0];
            return $y . $separator . ($y + 1);
        }

        // Kalau input sudah aneh, kembalikan apa adanya biar kelihatan di UI
        return (string) $startYear;
    }
}

if (! function_exists('ipk_terbilang')) {
    /**
     * Ubah IPK (3.98 / 3,98) menjadi "tiga koma sembilan delapan".
     */
    function ipk_terbilang($ipk): ?string
    {
        if ($ipk === null || $ipk === '') {
            return null;
        }

        // Normalisasi: ganti koma ke titik, buang selain angka & titik
        $s = str_replace(',', '.', (string) $ipk);
        $s = preg_replace('/[^0-9.]/', '', $s) ?? '';

        if ($s === '') {
            return null;
        }

        // Pecah bagian sebelum & sesudah desimal
        $pos   = strpos($s, '.');
        $before = $pos !== false ? substr($s, 0, $pos) : $s;
        $after  = $pos !== false ? substr($s, $pos + 1) : '';

        $map = [
            '0' => 'nol', '1' => 'satu', '2' => 'dua', '3' => 'tiga', '4' => 'empat',
            '5' => 'lima', '6' => 'enam', '7' => 'tujuh', '8' => 'delapan', '9' => 'sembilan',
        ];

        $words = [];
        foreach (str_split($before) as $ch) {
            if (isset($map[$ch])) $words[] = $map[$ch];
        }

        if ($after !== '') {
            $words[] = 'koma';
            foreach (str_split($after) as $ch) {
                if (isset($map[$ch])) $words[] = $map[$ch];
            }
        }

        return implode(' ', $words);
    }
}

if (! function_exists('tanggal_indo')) {
    /**
     * Format tanggal Indonesia: "DD bulan YYYY"
     * Contoh: '2004-12-16 09:16:56' -> '16 desember 2004'
     * Set $capitalized = true untuk '16 Desember 2004'.
     */
    function tanggal_indo($date, bool $capitalized = false): ?string
    {
        if ($date === null || $date === '') {
            return null;
        }

        try {
            $dt = $date instanceof DateTimeInterface
                ? CarbonImmutable::instance($date)
                : CarbonImmutable::parse((string) $date);
        } catch (\Throwable $e) {
            // Jika gagal parse, kembalikan apa adanya
            return (string) $date;
        }

        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $namaBulan = $bulan[(int) $dt->month] ?? '';
        if ($capitalized) {
            // Kapitalisasi huruf pertama bulan
            $namaBulan = mb_convert_case($namaBulan, MB_CASE_TITLE, 'UTF-8');
        }

        return ltrim($dt->day, '0') . ' ' . $namaBulan . ' ' . $dt->year;
    }
}
