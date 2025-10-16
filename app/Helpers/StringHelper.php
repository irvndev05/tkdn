<?php

namespace App\Helpers;

class StringHelper
{
    /**
     * Safely limit a string to a specified length
     *
     * @param  string|null  $string
     * @param  int  $limit
     * @param  string  $end
     * @return string
     */
    public static function safeLimit($string, $limit = 100, $end = '...')
    {
        if (empty($string)) {
            return '';
        }

        $string = (string) $string;

        // Use iconv if available, otherwise fallback to substr
        if (function_exists('iconv') && extension_loaded('iconv')) {
            $limited = iconv_substr($string, 0, $limit, 'UTF-8');
        } else {
            $limited = substr($string, 0, $limit);
        }

        if (strlen($string) <= $limit) {
            return $string;
        }

        return rtrim($limited).$end;
    }

    /**
     * Get first character of a string safely
     *
     * @param  string|null  $string
     * @return string
     */
    public static function firstChar($string)
    {
        if (empty($string)) {
            return '';
        }

        $string = (string) $string;

        // Use iconv if available, otherwise fallback to substr
        if (function_exists('iconv') && extension_loaded('iconv')) {
            return iconv_substr($string, 0, 1, 'UTF-8');
        }

        return substr($string, 0, 1);
    }

    /**
     * Convert classification TKDN string to integer
     */
    public static function classificationTkdnToInt(?string $classification): ?int
    {
        if (empty($classification)) {
            return null;
        }

        $classification = trim($classification);

        // Mapping sesuai dengan option di form (1-7 saja)
        $mapping = [
            'Overhead & Manajemen' => 1,
            'Alat Kerja / Fasilitas' => 2,
            'Konstruksi & Fabrikasi' => 3,
            'Peralatan (Jasa Umum)' => 4,
            'Material (Bahan Baku)' => 5,
            'Peralatan (Barang Jadi)' => 6,
            'Summary' => 7,
        ];

        return $mapping[$classification] ?? null;
    }

    /**
     * Convert integer classification TKDN to string
     */
    public static function intToClassificationTkdn(?int $classification): ?string
    {
        if ($classification === null) {
            return null;
        }

        // Mapping sesuai dengan option di form (1-7 saja)
        $mapping = [
            1 => 'Overhead & Manajemen',
            2 => 'Alat Kerja / Fasilitas',
            3 => 'Konstruksi & Fabrikasi',
            4 => 'Peralatan (Jasa Umum)',
            5 => 'Material (Bahan Baku)',
            6 => 'Peralatan (Barang Jadi)',
            7 => 'Summary',
        ];

        return $mapping[$classification] ?? null;
    }
}
