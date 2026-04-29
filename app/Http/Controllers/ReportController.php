<?php

namespace App\Http\Controllers;

use App\Exports\MonthlyCollectionExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function monthly(Request $request)
    {
        $monthStr = (string) $request->query('month', now()->format('Y-m'));
        try {
            $month = Carbon::createFromFormat('Y-m', $monthStr)->startOfMonth();
        } catch (\Throwable $e) {
            $month = now()->startOfMonth();
        }

        $filename = 'collection-'.$month->format('Y-m').'.xlsx';
        return Excel::download(new MonthlyCollectionExport($month), $filename);
    }
}
