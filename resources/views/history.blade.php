<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Peminjaman Kendaraan Dinas</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            margin: 20px;
            color: #000;
            font-size: 12pt;
        }
        h4, h3, h5 {
            text-align: center;
            margin: 0;
            font-weight: normal;
        }
        table {
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            font-size: 10pt;
        }
        th {
            background-color: #F0F0F0;
            font-weight: normal;
        }
        tr:nth-child(even) {
            background-color: #F9F9F9;
        }
        .page-break {
            page-break-after: always;
        }
        .empty-message {
            text-align: center;
            font-style: italic;
            padding: 20px;
            font-size: 10pt;
        }
    </style>
</head>
<body>
    <h4>Daftar Peminjaman Kendaraan Dinas</h4>
    <h3>Pusat Pengelolaan Komplek Kemayoran</h3>
    <h5>Periode peminjaman: {{ $minDateFormatted }} s.d {{ $maxDateFormatted }}</h5>
    <table>
        <thead>
            <tr>
                <th>Tanggal Peminjaman</th>
                <th>Waktu Peminjaman</th>
                <th>Nama Peminjam</th>
                <th>Jenis Kendaraan</th>
                <th>Model/ Tipe</th>
                <th>No. Polisi</th>
                <th>Status Peminjaman</th>
            </tr>
        </thead>
        <tbody>
            @if($histories->isEmpty())
                <tr>
                    <td colspan="7" class="empty-message">Tidak ada data peminjaman tersedia untuk periode ini.</td>
                </tr>
            @else
                @foreach ($histories as $history)
                    <tr>
                        <td>{{ $history->created_at->format('d/m/Y') }}</td>
                        <td>{{ $history->created_at->format('H:i:s') }}</td>
                        <td>{{ $history->request->user->name }}</td>
                        <td>{{ $history->vehicle->wheels }}</td>
                        <td>{{ $history->vehicle->model }}</td>
                        <td>{{ $history->vehicle->license_plate }}</td>
                        <td>{{ $history->request->status }}</td>
                    </tr>
                    @if($loop->iteration % 15 == 0)
                        <tr class="page-break"></tr>
                    @endif
                @endforeach
            @endif
        </tbody>
    </table>
</body>
</html>
