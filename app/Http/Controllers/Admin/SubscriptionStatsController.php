<?php
declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Services\Subscriptions\SubscriptionPercentagesCalculator;
use App\Services\Subscriptions\SubscriptionMonthlyNetAggregator;
use App\Services\Subscriptions\SubscriptionAgeMetricsCalculator;
use App\Services\Subscriptions\CsvExportService;
use Illuminate\View\View;

class SubscriptionStatsController extends Controller
{
    public function index(): View
    {
        return view('admin.subscriptions.stats');
    }

    public function percentages(SubscriptionPercentagesCalculator $percentages): JsonResponse
    {
        $payload = $percentages();
        return response()->json($payload);
    }

    /**
     * Altas, bajas y neto por mes de un año dado.
     * year param (int) default actual.
     */
    public function monthlyNet(SubscriptionMonthlyNetAggregator $monthlyNet): JsonResponse
    {
        $year = (int) (request()->query('year') ?? date('Y'));
        return response()->json($monthlyNet($year));
    }

    public function ages(SubscriptionAgeMetricsCalculator $ages): JsonResponse
    {
        return response()->json($ages());
    }

    public function exportAgesJson(SubscriptionAgeMetricsCalculator $ages): JsonResponse
    {
        $payload = $ages();
        $filename = 'distribucion_edades_'.date('Ymd_His').'.json';
        return response()->json($payload)->withHeaders([
            'Content-Disposition' => 'attachment; filename="'.$filename.'"'
        ]);
    }

    public function exportAgesExcel(SubscriptionAgeMetricsCalculator $ages, CsvExportService $csv): BinaryFileResponse
    {
        $payload = $ages();
    // Export only rows (range/count/percentage) ensuring array type
    $rows = is_array($payload['rows']) ? $payload['rows'] : $payload['rows']->toArray();
    return $csv($rows, 'distribucion_edades');
    }

    // Exports JSON for percentages
    public function exportPercentagesJson(SubscriptionPercentagesCalculator $percentages): JsonResponse
    {
        $payload = $percentages();
        $filename = 'porcentajes_suscripciones_'.date('Ymd_His').'.json';
        return response()->json($payload)->withHeaders([
            'Content-Disposition' => 'attachment; filename="'.$filename.'"'
        ]);
    }

    // Exports JSON for monthly net
    public function exportMonthlyNetJson(SubscriptionMonthlyNetAggregator $monthlyNet): JsonResponse
    {
        $year = (int) (request()->query('year') ?? date('Y'));
        $payload = $monthlyNet($year);
        $filename = 'altas_bajas_neto_'.$payload['year'].'_'.date('Ymd_His').'.json';
        return response()->json($payload)->withHeaders([
            'Content-Disposition' => 'attachment; filename="'.$filename.'"'
        ]);
    }

    // Excel exports (simple array to CSV-like XLS via Laravel Excel fallback)
    public function exportPercentagesExcel(SubscriptionPercentagesCalculator $percentages, CsvExportService $csv): BinaryFileResponse
    {
        $payload = $percentages();
        return $csv($payload['data'], 'porcentajes_suscripciones');
    }

    public function exportMonthlyNetExcel(SubscriptionMonthlyNetAggregator $monthlyNet, CsvExportService $csv): BinaryFileResponse
    {
        $year = (int) (request()->query('year') ?? date('Y'));
        $payload = $monthlyNet($year);
        return $csv($payload['data'], 'altas_bajas_neto_'.$payload['year']);
    }
}
