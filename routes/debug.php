<?php

use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

Route::get('/test-member-growth', function () {
    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    $memberChartData = array_fill(0, 12, 0);
    $memberChartLabels = [];

    $now = Carbon::now();
    $twelveMonthsAgo = $now->copy()->subMonths(12);

    echo "Now: " . $now . "<br>";
    echo "12 months ago: " . $twelveMonthsAgo . "<br>";

    $currentDate = $twelveMonthsAgo->copy()->startOfMonth()->addMonth();

    for ($i = 0; $i < 12; $i++) {
        $monthNum = $currentDate->month;
        $yearNum = $currentDate->year;
        $memberChartLabels[] = $months[$monthNum - 1] . " '" . substr($yearNum, -2);
        echo "Label $i: " . $memberChartLabels[$i] . " (" . $currentDate . ")<br>";
        $currentDate->addMonth();
    }

    // Test data from database
    $testData = [
        (object)['month' => 1, 'year' => 2026, 'count' => 2],
        (object)['month' => 3, 'year' => 2026, 'count' => 1]
    ];

    echo "<hr>Processing Test Data:<br>";
    foreach ($testData as $data) {
        $dataDate = Carbon::create($data->year, $data->month, 1)->startOfMonth();
        $monthAfterRange = $twelveMonthsAgo->copy()->addMonths(12)->startOfMonth();
        $rangeStart = $twelveMonthsAgo->copy()->startOfMonth()->addMonth();

        echo "Data: " . $dataDate . " (count=" . $data->count . ")<br>";
        echo "  Range: " . $rangeStart . " to " . $monthAfterRange . "<br>";

        if ($dataDate >= $rangeStart && $dataDate < $monthAfterRange) {
            $monthIndex = $dataDate->copy()->startOfMonth()->diffInMonths($rangeStart);
            echo "  -> In range, monthIndex: $monthIndex<br>";
            if ($monthIndex >= 0 && $monthIndex < 12) {
                $memberChartData[$monthIndex] = $data->count;
                echo "  -> Mapped to index $monthIndex<br>";
            }
        } else {
            echo "  -> OUT OF RANGE<br>";
        }
    }

    echo "<hr>Final Chart Data:<br>";
    print_r($memberChartData);
});
