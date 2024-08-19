<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        @page {
            size: A4;
            margin: 25mm;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .header-table {
            width: 100%;
            margin-bottom: 5px;
        }
        .logo {
            height: 80px;
        }
        .header-line {
            border-bottom: 1px solid #000;
            width: 100%;
        }
        .header-content {
            text-align: center;
        }
        .header-content h4, .header-content h3, .header-content p {
            margin: 0;
        }
        .content {
            flex: 1;
            margin: 20px;
        }
        .footer {
            width: 100%;
            text-align: center;
            font-size: 8pt;
            font-weight: bold;
            font-style: italic;
            position: fixed;
            bottom: 0mm;
            left: 0;
        }
        table {
            border-collapse: collapse;
        }
        td, th {
            margin: 0;
            padding: 0;
        }
        .value-cell {
            padding-left: 30px;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }
        .spacer {
            height: 2em;
        }
        .approval {
            text-align: center;
            margin-top: 50px;
        }
        .centered-text {
            text-align: center;
            display: inline-block;
            width: 180px; /* Adjust width to ensure proper centering */
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 20%;">
                <img src="{{ public_path('storage/images/logo.png') }}" alt="Logo" class="logo">
            </td>
            <td style="width: 80%;">
                <div class="header-content">
                    <h4 style="letter-spacing: 1px;">KEMENTERIAN SEKRETARIAT NEGARA RI</h4>
                    <h3 style="letter-spacing: 1px;">PUSAT PENGELOLAAN KOMPLEK KEMAYORAN</h3>
                    <p style="font-size: 8pt;">Jalan Merpati Blok B-14 No. 2, Gunung Sahari Utara, Jakarta Pusat 10720</p>
                    <p style="font-size: 8pt;">Telp. (021) 4207688, Faks. (021) 6543123</p>
                </div>
            </td>
        </tr>
    </table>
    <div class="header-line"></div>
    <div class="content">
        <p>Perihal  : Pemakaian Kendaraan Dinas Jakarta, 2024</p>
        <p>Kepada<br>Kepala Divisi Administrasi Umum<br>PPK Kemayoran</p>
        <p style="line-height: 1.5;">Dengan ini kami mohon ijin untuk menggunakan kendaraan operasional untuk keperluan dinas:</p>
        <div style="line-height: 1.5;">
            <table>
                <tr>
                    <td>Kendaraan</td>
                    <td class="value-cell">: {{ $request->wheels }}</td>
                </tr>
                <tr>
                    <td>Nomor Polisi</td>
                    <td class="value-cell">: {{ $vehicle }}</td>
                </tr>
                <tr>
                    <td>Hari/Tanggal</td>
                    <td class="value-cell">: {{ $departureDate }}</td>
                </tr>
                <tr>
                    <td>Keperluan</td>
                    <td class="value-cell">: {{ $request->reason }}</td>
                </tr>
                <tr>
                    <td>Berangkat Pukul</td>
                    <td class="value-cell">: {{ $departureTime }}</td>
                </tr>
                <tr>
                    <td>Kembali Pukul</td>
                    <td class="value-cell">: {{ $returnTime }}</td>
                </tr>
            </table>
        </div>
        <div class="signatures">
            <table width="100%" style="line-height: 1.5;">
                <tr>
                    <td style="text-align: left;">
                        <p>Mengetahui,</p>
                        <p>Kepala {{ $userDiv }}</p>
                        <div style="height: 2em;"></div>
                        <p>(<span class="centered-text">{{ $kepalaName }}</span>)</p>
                    </td>
                    <td style="text-align: right;">
                        <p style="padding-right: 55px;">Pemohon,</p>
                        <p>&nbsp;</p>
                        <div style="height: 2em;"></div>
                        <p>(<span class="centered-text">{{ $user }}</span>)</p>
                    </td>
                </tr>
            </table>
        </div>
        <div class="approval">
            <p>Menyetujui,</p>
            <p>Kepala Divisi Administrasi Umum</p>
            <div class="spacer"></div>
            <p>(Novayanti Sidauruk)</p>
        </div>
    </div>
    <div class="footer">
        <p>Note: Surat peminjaman ini tidak sah apabila tidak ditandatangani Kepala Divisi Administrasi Umum</p>
    </div>
</body>
</html>
