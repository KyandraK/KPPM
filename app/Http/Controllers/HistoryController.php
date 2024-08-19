<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class HistoryController extends Controller
{
    public function exportPdf(Request $request)
    {
        $query = History::with('vehicle', 'request');

        if ($request->filled('tableFilters.vehicle_data.wheels')) {
            $query->whereHas('vehicle', function ($q) use ($request) {
                $q->where('wheels', $request->input('tableFilters.vehicle_data.wheels'));
            });
        }

        if ($request->filled('tableFilters.vehicle_data.model')) {
            $query->whereHas('vehicle', function ($q) use ($request) {
                $q->where('model', $request->input('tableFilters.vehicle_data.model'));
            });
        }

        if ($request->filled('tableFilters.vehicle_data.license_plate')) {
            $query->whereHas('vehicle', function ($q) use ($request) {
                $q->where('license_plate', $request->input('tableFilters.vehicle_data.license_plate'));
            });
        }

        if ($request->filled('tableFilters.created_at.created_from')) {
            $query->whereDate('created_at', '>=', $request->input('tableFilters.created_at.created_from'));
        }

        if ($request->filled('tableFilters.created_at.created_until')) {
            $query->whereDate('created_at', '<=', $request->input('tableFilters.created_at.created_until'));
        }

        $histories = $query->get();

        $minDate = $query->min('created_at');
        $maxDate = $query->max('created_at');

        $minDateFormatted = $minDate ? (new \DateTime($minDate))->format('d/m/Y') : '-';
        $maxDateFormatted = $maxDate ? (new \DateTime($maxDate))->format('d/m/Y') : '-';

        $pdf = Pdf::loadView('history', compact('histories', 'minDateFormatted', 'maxDateFormatted'));

        return $pdf->download('history_data.pdf');
    }
}
