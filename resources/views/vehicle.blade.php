<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Histories</title>
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
    <h5>{{ $vehicle->model }} {{ $vehicle->license_plate }}</h5>
    <table>
        <thead>
            <tr>
                <th>Tanggal Peminjaman</th>
                <th>Nama Peminjam</th>
                <th>KM Sebelum</th>
                <th>KM Sesudah</th>
            </tr>
        </thead>
        <tbody>
            @if($vehicle->histories->isEmpty())
                <tr>
                    <td colspan="4" class="empty-message">Tidak ada data peminjaman tersedia.</td>
                </tr>
            @else
                @foreach($vehicle->histories as $history)
                    <tr>
                        <td>{{ $history->created_at->format('d/m/Y') }}</td>
                        <td>{{ $history->request->user->name }}</td>
                        <td>{{ $history->post->inspection->kilometer }}</td>
                        <td>{{ $history->post->post_kilometer }}</td>
                    </tr>
                    @if($loop->iteration % 20 == 0)
                        <tr class="page-break"></tr>
                    @endif
                @endforeach
            @endif
        </tbody>
    </table>
</body>
</html>
