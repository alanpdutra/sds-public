<?php

namespace Tests\Unit;

use App\Support\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function test_br_to_decimal_converts_simple_values(): void
    {
        $this->assertEquals('0.01', Money::brToDecimal('0,01'));
        $this->assertEquals('1.00', Money::brToDecimal('1,00'));
        $this->assertEquals('10.50', Money::brToDecimal('10,50'));
    }

    public function test_br_to_decimal_converts_with_thousand_separators(): void
    {
        $this->assertEquals('1234.56', Money::brToDecimal('1.234,56'));
        $this->assertEquals('12345.67', Money::brToDecimal('12.345,67'));
        $this->assertEquals('123456.78', Money::brToDecimal('123.456,78'));
    }

    public function test_br_to_decimal_removes_currency_symbols(): void
    {
        $this->assertEquals('1234.56', Money::brToDecimal('R$ 1.234,56'));
        $this->assertEquals('1234.56', Money::brToDecimal('R$1.234,56'));
        $this->assertEquals('1234.56', Money::brToDecimal(' R$ 1.234,56 '));
    }

    public function test_br_to_decimal_handles_edge_cases(): void
    {
        $this->assertEquals('0.00', Money::brToDecimal('0'));
        $this->assertEquals('0.00', Money::brToDecimal('0,00'));
        $this->assertEquals('1.00', Money::brToDecimal('1'));
    }
}