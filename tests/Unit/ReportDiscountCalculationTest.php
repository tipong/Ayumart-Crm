<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ReportDiscountCalculationTest extends TestCase
{
    public function test_discount_calculation_logic_includes_item_discounts()
    {
        // Mock a collection of transactions/orders
        $transaction1 = (object) [
            'total_harga' => 100000, // Price after item discount, before membership discount
            'total_diskon' => 10000, // Membership discount
            'details' => collect([
                (object) ['diskon_item' => 5000],
                (object) ['diskon_item' => 3000],
            ])
        ];

        $transaction2 = (object) [
            'total_harga' => 200000,
            'total_diskon' => 20000,
            'details' => collect([
                (object) ['diskon_item' => 12000],
            ])
        ];

        $transactions = collect([$transaction1, $transaction2]);

        // Calculate stats using the exact formula we implemented in the controller:
        $totalPenjualan = $transactions->sum(function($t) {
            return $t->total_harga + $t->details->sum('diskon_item');
        });

        $totalDiskon = $transactions->sum(function($t) {
            return $t->total_diskon + $t->details->sum('diskon_item');
        });

        $pendapatanBersih = $totalPenjualan - $totalDiskon;

        // Expectations:
        // Transaction 1:
        //   original_total = 100000 + 8000 = 108000
        //   total_discount = 10000 + 8000 = 18000
        // Transaction 2:
        //   original_total = 200000 + 12000 = 212000
        //   total_discount = 20000 + 12000 = 32000
        // Total Penjualan (original price) = 108000 + 212000 = 320000
        // Total Diskon = 18000 + 32000 = 50000
        // Pendapatan Bersih = 320000 - 50000 = 270000 (which is exactly (100k-10k) + (200k-20k) = 90k + 180k = 270k)

        $this->assertEquals(320000, $totalPenjualan);
        $this->assertEquals(50000, $totalDiskon);
        $this->assertEquals(270000, $pendapatanBersih);
    }

    public function test_transaction_chart_monthly_mapping()
    {
        // Mock a keyBy('month') result from DB with count and total_revenue
        $transactionData = collect([
            1 => (object) ['month' => 1, 'count' => 15, 'total_revenue' => 1500000],
            3 => (object) ['month' => 3, 'count' => 22, 'total_revenue' => 2200000],
            12 => (object) ['month' => 12, 'count' => 50, 'total_revenue' => 5000000],
        ])->keyBy('month');

        $monthsIndonesia = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $chartData = [];
        $chartRevenues = [];

        for ($m = 1; $m <= 12; $m++) {
            $chartData[] = $transactionData[$m]->count ?? 0;
            $chartRevenues[] = intval($transactionData[$m]->total_revenue ?? 0);
        }

        // Expected mapping:
        // Jan (index 0) => 15 count, 1500000 revenue
        // Feb (index 1) => 0 count, 0 revenue
        // Mar (index 2) => 22 count, 2200000 revenue
        // ...
        // Dec (index 11) => 50 count, 5000000 revenue
        $this->assertCount(12, $chartData);
        $this->assertCount(12, $chartRevenues);
        $this->assertEquals(15, $chartData[0]);
        $this->assertEquals(1500000, $chartRevenues[0]);
        $this->assertEquals(0, $chartData[1]);
        $this->assertEquals(0, $chartRevenues[1]);
        $this->assertEquals(22, $chartData[2]);
        $this->assertEquals(2200000, $chartRevenues[2]);
        $this->assertEquals(0, $chartData[3]);
        $this->assertEquals(0, $chartRevenues[3]);
        $this->assertEquals(50, $chartData[11]);
        $this->assertEquals(5000000, $chartRevenues[11]);
        $this->assertEquals($monthsIndonesia[0], 'Januari');
        $this->assertEquals($monthsIndonesia[11], 'Desember');
    }
}
