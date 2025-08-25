<?php

namespace App\Support;

final class Money
{
    /**
     * Convert Brazilian currency format to decimal
     * 
     * @param string $input Brazilian format like "1.234,56" or "R$ 1.234,56"
     * @return string Decimal format like "1234.56"
     */
    public static function brToDecimal(string $input): string
    {
        // Remove currency symbols, spaces, etc.
        $clean = preg_replace('/[^\d,.\-]/', '', $input);
        
        // Remove thousand separators (dots)
        $clean = str_replace('.', '', $clean);
        
        // Convert comma to dot for decimal separator
        $clean = str_replace(',', '.', $clean);
        
        // Format with 2 decimal places using dot
        return number_format((float) $clean, 2, '.', '');
    }
}