<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\HistoriesExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function export(Request $request)
    {
        $filters = $request->query('tableFilters', []);
        return Excel::download(new HistoriesExport($filters), 'histories.xlsx');
    }
}
