<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Alumni - Universitas Halu Oleo</title>
    <style>
        @page {
            size: A4;
            margin: 1cm 1cm;
        }
        
        @media print {
            body {
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 0cm 1cm !important;
                box-shadow: none !important;
                min-height: auto !important;
                font-size: 12pt !important;
            }
            
            .header {
                margin-bottom: 25px !important;
                page-break-inside: avoid;
            }
            
            .signature {
                page-break-inside: avoid;
                margin-top: 40px !important;
            }
            
            .letter-title {
                page-break-inside: avoid;
                margin: 10px 0 5px 0 !important;
            }
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.2;
            margin: 0;
            padding: 2cm;
            background-color: white;
            color: #000;
            width: 210mm;
            max-width: 210mm;
            margin: 0 auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            min-height: 297mm;
            box-sizing: border-box;
        }
        
        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #000;
        }
        
        .logo {
            width: 115px;
            height: 115px;
            margin-right: 20px;
            object-fit: contain;
        }
        
        .header-text {
            flex: 1;
        }
        
        .header h1 {
            font-size: 14pt;
            font-weight: 400;
            margin: 0;
            text-transform: uppercase;
        }
        
        .header h2 {
            font-size: 14pt;
            font-weight: 400;
            margin: 0;
            text-transform: uppercase;
        }
        
        .header h3 {
            font-size: 14pt;
            font-weight: 600;
            margin: 0;
            text-transform: uppercase;
        }
        
        .header .address {
            font-size: 11pt;
            margin: 0px 0 5px 0;
            line-height: 1.2;
        }
        
        .letter-title {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            margin: 10px 0 5px 0;
            text-transform: uppercase;
        }
        
        .letter-number {
            text-align: center;
            font-size: 12pt;
            margin: 0px 0 15px 0;
        }
        
        .content {
            margin: 20px 0 10px 0;
            text-align: justify;
            line-height: 1.2;
        }
        
        .student-info {
            margin: 10px 0;
            line-height: 1.2;
        }
        
        .closing {
            margin-top: 20px;
            text-align: justify;
        }
        
        .signature {
            margin-top: 60px;
            float: right;
            text-align: left;
            clear: both;
        }
        
        .signature-date {
            margin-bottom: 15px;
        }
        
        .signature-title {
            margin-bottom: 20px;
        }
        
        .signature-name {
            font-weight: bold;
            margin-top: 80px;
            text-decoration: underline;
        }
        
        .signature-nip {
            font-weight: bold;
            margin-top: 5px;
        }
        
        .blank-line {
            border-bottom: 1px solid #000;
            display: inline-block;
            width: 100px;
            margin: 0 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('images/logo.png') }}" alt="Logo Universitas Halu Oleo" class="logo">
        <div class="header-text">
            <h1>Kementerian Pendidikan Tinggi, Sains<br>dan Teknologi</h1>
            <h2>Universitas Halu Oleo</h2>
            <h3>Program Pascasarjana</h3>
            <div class="address">
                Kampus Pascasarjana Jl. Mayjen S.Parman Kemaraya Kendari, 93121<br>
                Telp/Fax (0401) 3127187, Email : ppsuho@uho.ac.id, Web. : www.pasca.uho.ac.id
            </div>
        </div>
    </div>
    
    <div class="letter-title" style="text-decoration: underline;">
        Surat Keterangan Alumni
    </div>
    
    <div class="letter-number">
        Nomor : {{ $submission->txtLetterNumber ?? 'Belum diisi oleh Akademik' }}
    </div>
    
    <div class="content">
        Direktur Program Pascasarjana Universitas Halu Oleo menerangkan bahwa yang tersebut di bawah ini :
    </div>
    
    <div class="student-info" style="margin-left: 1cm">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 200px; padding: 0;">Nama</td>
                <td style="padding: 0;">: {{ $submission->user->txtFullName }}</td>
            </tr>
            <tr>
                <td style="padding: 0;">Tempat & Tanggal Lahir</td>
                <td style="padding: 0;">: {{ $submission->user->txtBirthPlace }}, {{ tanggal_indo($submission->user->dtmBirthDate) }}</td>
            </tr>
            <tr>
                <td style="padding: 0;">NIM</td>
                <td style="padding: 0;">: {{ $submission->user->mahasiswaProfile->txtNIM }}</td>
            </tr>
            <tr>
                <td style="padding: 0;">Program Studi</td>
                <td style="padding: 0;">: {{ $submission->user->mahasiswaProfile->major->txtNameMajor }}</td>
            </tr>
            <tr>
                <td style="padding: 0;">Konsentrasi</td>
                <td style="padding: 0;">: {{ $submission->user->mahasiswaProfile->concentrate->txtNameConcentrate }}</td>
            </tr>
            <tr>
                <td style="padding: 0;">IPK</td>
                <td style="padding: 0;">: {{ $data['intIPK'] }}</td>
            </tr>
            <tr>
                <td style="padding: 0;">Jenjang Pendidikan</td>
                <td style="padding: 0;">: {{ $submission->user->mahasiswaProfile->major->txtStrata == 'S2' ? 'Magister S2' : 'Doktor S3' }}</td>
            </tr>
        </table>
    </div>
    
    <div class="content">
        Yang bersangkutan adalah benar Mahasiswa yang terdaftar pada Tahun Akademik {{ tahun_akademik($submission->user->mahasiswaProfile->txtYear) }} dan telah menyelesaikan Studi Pascasarjana Program Studi {{ $submission->user->mahasiswaProfile->major->txtNameMajor }} UHO pada tanggal {{ tanggal_indo($data['dtmYudisium']) }} dengan Indeks Prestasi Kumulatif (IPK) {{ ipk_terbilang($data['intIPK']) }} {{ $data['intIPK'] }}.
    </div>
    
    <div class="closing">
        Demikian surat keterangan Alumni ini dibuat dan diberikan kepada yang bersangkutan untuk dipergunakan sebagaimana mestinya.
    </div>
    
    <div class="signature">
        <div class="signature-date">
            Kendari, {{ tanggal_indo(now()) }}
        </div>
        
        <div class="signature-title">
            Direktur,
        </div>
        
        <div class="signature-name">
            Prof. Dr. Ir. La Ode Safuan, M.P.
        </div>
        <div class="signature-nip">
            NIP 196512311991031024
        </div>
    </div>
</body>
</html>