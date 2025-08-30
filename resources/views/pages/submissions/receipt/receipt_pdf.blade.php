<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt Pengajuan - {{ $submission->txtReceiptNumber }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #ffffff;
        }
        .receipt-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }
        .receipt-table {
            border: 2px solid black;
            font-family: Arial, sans-serif;
            color: black;
            width: 100%;
            border-collapse: collapse;
        }
        .receipt-header {
            border-bottom: 2px solid black;
        }
        .receipt-header th {
            text-align: center;
            padding: 24px 32px;
        }
        .receipt-header h5 {
            font-weight: bold;
            color: black;
            margin: 5px 0;
            font-size: 16px;
        }
        .receipt-body {
            border-bottom: 2px solid black;
        }
        .receipt-body td {
            text-align: center;
            padding: 24px 32px;
        }
        .receipt-title {
            text-decoration: underline;
            color: black;
            font-size: 24px;
            margin: 0 0 10px 0;
        }
        .letter-type {
            font-size: 20px;
            margin: 10px 0;
        }
        .info-table {
            margin: 16px auto;
            border-collapse: collapse;
        }
        .info-table td {
            text-align: left;
            padding: 4px 0;
            vertical-align: top;
        }
        .info-table td:first-child {
            padding-right: 32px;
            min-width: 150px;
        }
        .info-notes {
            text-align: left;
            margin: 20px auto;
            width: 500px;
            padding-left: 0;
        }
        .info-notes li {
            margin-bottom: 8px;
            line-height: 1.4;
        }
        .receipt-footer {
            text-align: center;
            padding: 12px 0;
        }
        .receipt-footer h5 {
            color: black;
            font-weight: 400;
            margin: 0;
        }
        .receipt-number {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <table class="receipt-table">
            <thead class="receipt-header">
                <tr>
                    <th>
                        <h5>Sistem Informasi Administrasi Pelayanan Akademik</h5>
                        <h5>(SIAPIK)</h5>
                        <h5>Pascasarjana Universitas Halu Oleo</h5>
                    </th>
                </tr>
            </thead>
            <tbody class="receipt-body">
                <tr>
                    <td>
                        <h3 class="receipt-title">BUKTI PENGAJUAN</h3>
                        <div class="letter-type">{{ $submission->letterType->txtNameLetterType }}</div>
                        <table class="info-table">
                            <tr>
                                <td>Tanggal Pengajuan</td>
                                <td>: {{ $submission->dtmInserted }}</td>
                            </tr>
                            <tr>
                                <td>NIM</td>
                                <td>: {{ $submission->user->mahasiswaProfile->txtNIM }}</td>
                            </tr>
                            <tr>
                                <td>Nama</td>
                                <td>: {{ $submission->user->txtFullName }}</td>
                            </tr>
                            <tr>
                                <td>Prodi</td>
                                <td>: {{ $submission->user->mahasiswaProfile->major->txtNameMajor }}</td>
                            </tr>
                            <tr>
                                <td>Keterangan</td>
                                <td>:</td>
                            </tr>
                        </table>
                        <ol class="info-notes">
                            <li>Bukti Pengajuan dibawa serta pada saat pengambilan surat</li>
                            <li>Proses surat 3 hari kerja (Kecuali Surat Cuti menunggu pengesahan dari Rektorat)</li>
                            <li>Dokumen persyaratan asli dibawa serta saat pengambilan surat</li>
                            <li>Cek secara berkala Riwayat Pengajuan pada Sistem Siapik</li>
                        </ol>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td class="receipt-footer">
                        <h5>Nomor Receipt: <span class="receipt-number">#{{ $submission->txtReceiptNumber }}</span></h5>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>