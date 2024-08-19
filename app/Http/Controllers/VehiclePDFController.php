<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use Barryvdh\DomPDF\Facade\Pdf;

class VehiclePDFController extends Controller
{
    public function export(Request $request, $id)
    {
        $fromDate = $request->input('from_date');
        $untilDate = $request->input('until_date');

        $vehicle = Vehicle::with([
            'histories' => function ($query) use ($fromDate, $untilDate) {
                if ($fromDate) {
                    $query->whereDate('created_at', '>=', $fromDate);
                }
                if ($untilDate) {
                    $query->whereDate('created_at', '<=', $untilDate);
                }
            },
            'histories.post.inspection',
            'histories.post',
            'histories.request.user'
        ])->where('id', $id)->firstOrFail();

        $pdf = Pdf::loadView('vehicle', compact('vehicle'));

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('vehicle_histories.pdf');
    }
}
