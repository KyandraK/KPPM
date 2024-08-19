<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Request as RequestModel;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class PDFController extends Controller
{
    public function print($id)
    {
        $options = new \Dompdf\Options;
        $options->setIsRemoteEnabled(true);

        $request = RequestModel::with(['vehicle', 'user'])->findOrFail($id);

        $departureDate = Carbon::parse($request->departure_time)->format('d/m/Y');
        $departureTime = Carbon::parse($request->departure_time)->format('d/m/Y H.i');
        $returnTime = Carbon::parse($request->return_time)->format('d/m/Y H:i');
        $vehicle = $request->vehicle ? $request->vehicle->license_plate : '-';
        $user = $request->user ? $request->user->name : '.........';
        $userDiv = $request->user ? $request->user->division : '.........';

        $userDivision = $request->user ? $request->user->division : null;
        $kepalaUser = Role::where('name', 'kepala')
            ->first()
            ->users()
            ->where('division', $userDivision)
            ->first();
        $kepalaName = $kepalaUser ? $kepalaUser->name : '.........';

        $data = [
            'request' => $request,
            'departureDate' => $departureDate,
            'departureTime' => $departureTime,
            'returnTime' => $returnTime,
            'vehicle' => $vehicle,
            'user' => $user,
            'userDiv' => $userDiv,
            'kepalaName' => $kepalaName,
        ];

        $pdf = Pdf::loadView('pdf', $data);

        return $pdf->download('request.pdf');
    }
}
