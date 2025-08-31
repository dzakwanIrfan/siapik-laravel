<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Surat Tugas Pembimbing Ujian Disertasi - Universitas Halu Oleo</title>
  <style>
    @page { size: A4; margin: 1cm; }

    body {
      margin: 0;
      background: #fff;
      font-family: "Times New Roman", serif;
      color: #000;
    }

    .page {
      width: 210mm;
      min-height: 297mm;
      margin: 0 auto;
      box-sizing: border-box;
      padding: 2cm;
      display: flex;
      flex-direction: column;
      box-shadow: 0 0 10px rgba(0,0,0,.1);
      font-size: 12pt;
      line-height: 1.2;
    }

    .header {
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      margin-bottom: 15px;
      border-bottom: 2px solid #000;
    }

    .logo { width: 115px; height: 115px; margin-right: 20px; object-fit: contain; }
    .header-text { flex: 1; }

    .header h1, .header h2, .header h3 { margin: 0; text-transform: uppercase; }
    .header h1 { font-size: 14pt; font-weight: 400; }
    .header h2 { font-size: 14pt; font-weight: 400; }
    .header h3 { font-size: 14pt; font-weight: 600; }

    .header .address { font-size: 11pt; margin: 0 0 5px 0; line-height: 1.2; }

    .letter-title {
      text-align: center;
      font-weight: bold;
      text-decoration: underline;
      font-size: 12pt;
      margin: 0;
      text-transform: uppercase;
    }

    .letter-number { text-align: center; font-size: 12pt; margin: 0 0 10px 0; }

    .reference { margin: 5px 0; }
    .content { margin: 0; text-align: justify; }

    .examiners-table { 
        width: 100%; 
        border-collapse: collapse; 
        margin: 5px 0; 
    }

    .examiners-table th, .examiners-table td {
        border: 1px solid #000; 
        padding: 5px; 
        text-align: left; 
        font-size: 12pt;
    }
    .examiners-table th { 
        text-align: center; 
        font-weight: bold; 
    }

    .student-info, .exam-details { 
        margin: 5px 30px; 
    }

    .thesis-title { 
        margin: 10px 0; 
        font-weight: bold; 
        text-align: center; 
        line-height: 1.3; 
    }

    .signature {
      margin-top: 10mm;
      text-align: right;
      align-self: flex-end;
    }

    .signature-date { margin-bottom: 15px; }
    .signature-title { margin-bottom: 0; line-height: 1.2; }
    .signature-name { font-weight: bold; margin-top: 100px; text-decoration: underline; }
    .signature-nip { font-weight: bold; margin-top: 5px; }

    .notes { font-size: 11pt; }
    .notes strong { text-decoration: underline; }

    .blank-line, .blank-line-short, .blank-line-medium {
      border-bottom: 1px solid #000; display: inline-block; margin: 0 5px;
    }
    .blank-line { width: 150px; }
    .blank-line-short { width: 100px; }
    .blank-line-medium { width: 200px; }

    @media print {
        body { background: #fff; }
        .page {
            width: 210mm;
            min-height: calc(297mm - 0);
            padding: 0 1cm;
            box-shadow: none;
            font-size: 12pt !important;
            display: block;
            position: relative;
        }
        .header, .letter-title, .signature { page-break-inside: avoid; break-inside: avoid;}
        .signature { margin-top: 10mm; }
        .notes {
            position: absolute;
            left: 10mm;
            right: 10mm;
            bottom: 0;
            margin: 0;
            break-inside: avoid;
            page-break-inside: avoid;
        }
        table, thead, tbody, tr, th, td { break-inside: avoid; page-break-inside: avoid; }
        .notes{ max-width: calc(100% - 20mm); }
        .header, .letter-title, .signature{ break-inside: avoid; page-break-inside: avoid; }
    }
  </style>
</head>
<body>
    <div class="page">
        <div class="header">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Universitas Halu Oleo" class="logo"/>
            <div class="header-text">
                <h1>Kementerian Pendidikan Tinggi, Sains<br/>dan Teknologi</h1>
                <h2>Universitas Halu Oleo</h2>
                <h3>Program Pascasarjana</h3>
                <div class="address">
                Kampus Pascasarjana Jl. Mayjen S.Parman Kemaraya Kendari, 93121<br/>
                Telp/Fax (0401) 3127187, Email : ppsuho@uho.ac.id, Web. : www.pasca.uho.ac.id
                </div>
            </div>
        </div>

        <div class="letter-title">Surat Tugas Pembimbing {{ $data['txtJenisUjian'] }}</div>

        <div class="letter-number">
            Nomor : {{ $submission->txtLetterNumber ?? 'Belum diisi oleh Akademik' }}
        </div>

        <div class="reference">
            Berdasarkan Keputusan Direktur Universitas Halu Oleo nomor : {{ $submission->txtLetterNumber ?? 'Belum diisi oleh Akademik' }} tanggal {{ tanggal_indo(now()) }} tentang penetapan dosen penguji {{ $data['txtJenisUjian'] }}, maka saudara yang namanya tercantum dibawah ini :
        </div>

        <table class="examiners-table">
        <thead>
            <tr>
            <th style="width: 50px; padding: 1px;">NO</th>
            <th style="padding: 1px;">NAMA</th>
            <th style="width: 120px; padding: 1px;">JABATAN</th>
            </tr>
        </thead>
        <tbody>
            <tr>
            <td style="text-align:center; padding:1px;">1</td>
            <td style="padding:1px 5px;">{{ $data['txtKetua'] ?? '-' }}</td>
            <td style="padding:1px; text-align:center;">Ketua</td>
            </tr>
            <tr>
            <td style="text-align:center; padding:1px;">2</td>
            <td style="padding:1px 5px;">{{ $data['txtSekretaris'] ?? '-' }}</td>
            <td style="padding:1px; text-align:center;">Sekretaris</td>
            </tr>
            <tr>
            <td style="text-align:center; padding:1px;">3</td>
            <td style="padding:1px 5px;">{{ $data['txtAnggota1'] ?? '-' }}</td>
            <td style="padding:1px; text-align:center;">Anggota</td>
            </tr>
            <tr>
            <td style="text-align:center; padding:1px;">4</td>
            <td style="padding:1px 5px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>{{ $data['txtPembimbing1'] ?? '-' }}</span>
                    <span>(P.1)</span>
                </div>
            </td>
            <td style="padding:1px; text-align:center;">Anggota</td>
            </tr>
            <tr>
            <td style="text-align:center; padding:1px;">5</td>
            <td style="padding:1px 5px;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span>{{ $data['txtPembimbing2'] ?? '-' }}</span>
                    <span>(P.2)</span>
                </div>
            </td>
            <td style="padding:1px; text-align:center;">Anggota</td>
            </tr>
        </tbody>
        </table>

        <div class="content">Untuk menjadi pembimbing pada Ujian Tesis mahasiswa :</div>

        <div class="student-info">
            <table style="width:100%; border:none;">
                <tr>
                    <td style="width:150px; padding:0;">Nama</td>
                    <td style="padding:0;">:</td>
                    <td style="padding:0;">{{ $submission->user->txtFullName }}</td>
                </tr>
                <tr>
                    <td style="padding:0;">Nomor Registrasi</td>
                    <td style="padding:0;">:</td>
                    <td style="padding:0;">{{ $submission->user->mahasiswaProfile->txtNIM }}</td>
                </tr>
                <tr>
                    <td style="padding:0;">Program Studi</td>
                    <td style="padding:0;">:</td>
                    <td style="padding:0;">{{ $submission->user->mahasiswaProfile->major->txtNameMajor }}</td>
                </tr>
            </table>
        </div>

        <div class="content">Yang akan dilaksanakan pada :</div>

        <div class="exam-details">
            <table style="width:100%; border:none;">
                <tr>
                    <td style="width:150px; padding:0;">Hari/Tanggal</td>
                    <td style="padding:0 5px;">:</td>
                    <td style="padding:0;">{{ $data['txtHariTanggal'] ?? 'Belum diisi oleh Akademik' }}</td>
                </tr>
                <tr>
                    <td style="padding:0;">Jam</td>
                    <td style="padding:0 5px;">:</td>
                    <td style="padding:0;">{{ $data['txtJam'] ?? 'Belum diisi oleh Akademik' }}</td>
                </tr>
                <tr>
                    <td style="padding:0;">Tempat</td>
                    <td style="padding:0 5px;">:</td>
                    <td style="padding:0;">{{ $data['txtTempat'] ?? 'Belum diisi oleh Akademik' }}</td>
                </tr>
                <tr>
                    <td style="padding:0; vertical-align:top;">Judul</td>
                    <td style="padding:0 5px; vertical-align:top;">:</td>
                    <td style="padding:0; vertical-align:top; text-align:justify;">{{ $data['txtJudul'] }}</td>
                </tr>
            </table>
        </div>

        <div class="content">Demikian penugasan ini untuk dilaksanakan dengan penuh tanggung jawab.</div>

        <div class="signature">
            <div class="signature-date">Kendari, {{ tanggal_indo(now()) }}</div>
            <div class="signature-title">
                Wakil Direktur Bidang Akademik & Kemahasiswaan<br/>
                Pascasarjana Universitas Halu Oleo,
            </div>
            <div class="signature-name">Prof. Dr. Ir. Muhidin, M.Si</div>
            <div class="signature-nip">NIP 196512251994031008</div>
        </div>

        <div class="notes">
            <strong>Catatan :</strong><br/>
            • Dosen penguji memakai Baju Batik/Kain Tenun<br/>
            • Minimal 3 (tiga) hari sebelum ujian, naskah proposal/hasil penelitian/tesis sudah harus sampai pada penguji
        </div>
    </div>

    <!-- Halaman 2 (HTML, presisi isi PDF) -->
    <div class="page" aria-label="Lampiran" style="page-break-before: always; font-size:12pt;">
        <table style="width:100%; border-collapse:collapse; margin-bottom:8px;">
            <tbody>
                <tr>
                    <td style="width:120px; vertical-align:top;">Lampiran</td>
                    <td style="width:10px; vertical-align:top;">:</td>
                    <td>Keputusan Direktur Pascasarjana Universitas Haluoleo</td>
                </tr>
                <tr>
                    <td style="vertical-align:top;">Nomor</td>
                    <td style="vertical-align:top;">: {{ $submission->txtLetterNumber ?? 'Belum diisi oleh Akademik' }}</td>
                    <td></td>
                </tr>
                <tr>
                    <td style="vertical-align:top;">Tanggal</td>
                    <td style="vertical-align:top;">: {{ tanggal_indo($submission->dtmLetterDate) }}</td>
                    <td></td>
                </tr>
                <tr>
                    <td style="vertical-align:top;">Tentang</td>
                    <td style="vertical-align:top;">:</td>
                    <td style="text-align:justify;">Pengangkatan Dosen Pembimbing {{ $data['txtJenisUjian'] }} Mahasiswa Program Studi {{ $submission->user->mahasiswaProfile->major->txtNameMajor }} Pascasarjana Universitas Haluoleo.</td>
                </tr>
            </tbody>
        </table>

        <table style="width: 100%; border-collapse: collapse; margin: 5px 0;">
            <thead>
                <tr>
                    <th style="border: 1px solid black; padding: 5px; text-align: center;">NO</th>
                    <th style="width: 100px; border: 1px solid black; padding: 5px; text-align: center;">NAMA MAHASISWA / STAMBUK</th>
                    <th style="width: 120px; border: 1px solid black; padding: 5px; text-align: center;">JUDUL TESIS</th>
                    <th style="width: 90px; border: 1px solid black; padding: 5px; text-align: center;">PROGRAM STUDI</th>
                    <th style="border: 1px solid black; padding: 5px; text-align: center;">PEMBIMBING</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align:center; vertical-align:top; text-align: center; border: 1px solid black; padding: 5px; text-align: center;">01</td>
                    <td style="vertical-align:top; text-align: center; border: 1px solid black; padding: 5px; text-align: center;">
                        <div>{{ $submission->user->txtFullName }}</div>
                        <div style="margin-top:2px;">{{ $submission->user->mahasiswaProfile->txtNIM }}</div>
                    </td>
                    <td style="vertical-align:top; text-align: center; border: 1px solid black; padding: 5px; text-align: center;">{{ $data['txtJudul'] }}</td>
                    <td style="vertical-align:top; text-align: center; border: 1px solid black; padding: 5px; text-align: center;">{{ $submission->user->mahasiswaProfile->major->txtNameMajor }}</td>
                    <td style="vertical-align:top; border: 1px solid black; padding: 5px; text-align: center; width: 100%;">
                        <table style="width:100%; border-collapse:collapse; border: 0 solid black; margin: 0 auto;">
                            @if ($data['txtKetua'])
                                <tr style="text-align: left;">
                                    <td style="width: 100px;">Ketua</td>
                                    <td>: {{ $data['txtKetua'] }}</td>
                                </tr>
                            @endif
                            @if ($data['txtSekretaris'])
                                <tr style="text-align: left;">
                                    <td style="width: 100px;">Sekretaris</td>
                                    <td>: {{ $data['txtSekretaris'] }}</td>
                                </tr>
                            @endif
                            @if ($data['txtAnggota'])
                                <tr style="text-align: left;">
                                    <td>Anggota</td>
                                    <td>: {{ $data['txtAnggota'] }}</td>
                                </tr>
                            @endif
                            @if ($data['txtPembimbing1'])
                                <tr style="text-align: left;">
                                    <td>Pembimbing 1</td>
                                    <td>: {{ $data['txtPembimbing1'] }}</td>
                                </tr>
                            @endif
                            @if ($data['txtPembimbing2'])
                                <tr style="text-align: left;">
                                    <td>Pembimbing 2</td>
                                    <td>: {{ $data['txtPembimbing2'] }}</td>
                                </tr>
                            @endif
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="page" style="page-break-before: always">
        <table style="font-size: 11pt;">
            <tr>
            <td colspan="3" style="text-align: center; font-weight: bold">
                <div>KEPUTUSAN</div>
                <div>DIREKTUR PASCASARJANA</div>
                <div>UNIVERSITAS HALU OLEO</div>
                <div>NOMOR: {{ $submission->txtLetterNumber ?? 'Belum diisi oleh Akademik' }}</div>
                <div>Tentang</div>
                <div>PENETAPAN DOSEN PENGUJI PADA SEMINAR PROPOSAL MAHASISWA</div>
                <div>PASCASARJANA UNIVERSITAS HALU OLEO</div>
                <div>DIREKTUR PASCASARJANA</div>
            </td>
            </tr>


            <tr>
                <td colspan="3" style="text-align: justify;">
                    <table border="0" cellpadding="1" cellspacing="1" width="100%">
                        <tr>
                            <td style="width: 1cm; vertical-align: top;">Mengingat</td>
                            <td style="vertical-align: top;">:</td>
                            <td style="vertical-align: top;">a.</td>
                            <td>bahwa dalam rangka penyelesaian studi mahasiswa Pascasarjana Universitas Halu Oleo, perlu
                            diadakan Seminar Proposal;</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="vertical-align: top;">b.</td>
                            <td>bahwa Seminar Proposal dimaksud untuk mengetahui kemampuan Ilmiah dalam hal penguasaan
                            materi yang telah dipelajari dalam rangka pengembangan profesi/keahlian;</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="vertical-align: top;">c.</td>
                            <td>bahwa mahasiswa tersebut dalam lampiran surat keputusan ini adalah mahasiswa Pascasarjana
                            Universitas Halu Oleo yang telah memenuhi syarat untuk mengikuti Seminar Proposal;</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="vertical-align: top;">d.</td>
                            <td>bahwa untuk maksud tersebut diatas perlu ditetapkan dengan surat keputusan penetapan penguji
                            pada Seminar Proposal;</td>
                        </tr>


                        <tr>
                            <td style="width: 2cm; vertical-align: top;">Mengingat</td>
                            <td>:</td>
                            <td style="vertical-align: top;">1.</td>
                            <td>Undang-Undang Nomor : 20 Tahun 2003; tentang sistem Pendidikan Nasional;</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="vertical-align: top;">2.</td>
                            <td>Undang – Undang Nomor 12 Tahun 2012 Tentang Pendidikan Tinggi;</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="vertical-align: top;">3.</td>
                            <td>Peraturan Pemerintah No: 66 Tahun 2010, tentang Perubahan atas Peraturan Pemerintah No : 17
                            Tahun 2010 tentang Pengelolaan dan Penyelenggaraan Pendidikan;</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="vertical-align: top;">4.</td>
                            <td>Keputusan Presiden R.I. No: 37 Tahun 1981 tentang Pendirian Universitas Halu Oleo; </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="vertical-align: top;">5.</td>
                            <td>Keputusan Mendiknas R.I. Nomor : 43 Tahun 2012 tentang Statuta Universitas Halu Oleo; </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="vertical-align: top;">6.</td>
                            <td>Peraturan Menteri Pendidikan dan Kebudayaan Nomor 149 Tahun 2014 Tentang Organisasi dan Tata
                            Kelola Universitas Halu Oleo.;</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="vertical-align: top;">7.</td>
                            <td>Keputusan Menteri Riset Teknologi dan Pendidikan Tinggi Nomor : 327/M/KPT.KP/2017 Tentang
                            Pengangkatan Rektor Universitas Halu Oleo Periode 2017 - 2021Tanggal 17 Juli 2017;</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="vertical-align: top;">8.</td>
                            <td>Keputusan Rektor Universitas Halu Oleo No. 09/H.29/SK/PP/2008, Tahun 2008, tentang Pembukaan
                            Pascasarjana di Universitas Halu Oleo;</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="vertical-align: top;">9.</td>
                            <td>Keputusan Rektor Universitas Halu Oleo No. 10/H.29/SK/OT/2008, Tahun 2008, tentang
                            pembentukan Struktur Organisasi Pascasarjana Universitas Halu Oleo;</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="vertical-align: top;">10.</td>
                            <td>Surat Keputusan Rektor Universitas Halu Oleo Nomor: 1282/UN.29/SK/KP/2017, , tentang
                            Pemberhentian dan Pengangkatan Pejabat Non Struktural ( Tugas Tambahan Dosen ) Universitas
                            Halu Oleo;</td>
                        </tr>


                        <tr>
                            <td style="width: 2cm">Memperhatikan</td>
                            <td>:</td>
                            <td style="vertical-align: top;">1.</td>
                            <td>Petunjuk Pelaksanaan sistem kredit semester pada Perguruan Tinggi Tahun 2014;</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="vertical-align: top;">2.</td>
                            <td>Surat Direktur Pascasarjana tentang mahasiswa peserta ujian dan penetapan penguji pada
                            Seminar Proposal Januari 2023;</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="vertical-align: top;">3.</td>
                            <td>Peraturan Rektor Universitas Halu Oleo Nomor 1 Tahun 2019 tentang peraturan Akademik di
                            Lingkup Universitas Halu Oleo;</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="vertical-align: top;">4.</td>
                            <td>Surat Keputusan Direktur Pascasarjana Universitas Halu Oleo No. 7575/UN29.19/SK/2019 Tentang
                            Panduan Akademik Pascasarjana Universitas Halu Oleo;</td>
                        </tr>
                        <tr>
                            <td>Menetapkan</td>
                            <td></td>
                            <td colspan="2" style="text-align: center"><b>MEMUTUSKAN :</b></td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">Pertama</td>
                            <td style="vertical-align: top;">:</td>
                            <td colspan="2" style="vertical-align: top;">Mengangkat Dosen pembimbing pada Seminar Proposal mahasiswa Pascasarjana
                            Universitas Halu
                            Oleo sebagaimana tersebut dalam lampiran surat keputusan ini;</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">Kedua</td>
                            <td style="vertical-align: top;">:</td>
                            <td colspan="2" style="vertical-align: top;">Konsekuensi keuangan yang timbul akibat dari keputusan ini dibebankan pada anggaran DIPA BLU UHO;</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top;">Ketiga</td>
                            <td style="vertical-align: top;">:</td>
                            <td colspan="2" style="vertical-align: top;">Surat Keputusan ini berlaku sejak tanggal ditetapkan, dengan ketentuan apabila dikemudian hari terdapat kekeliruan dalam keputusan ini akan diadakan perbaikan sebagaimana mestinya;</td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td colspan="3">
                    <table border="0" align="right" style="margin-top: 70px;">
                        <tr>
                            <td>Kendari, {{ tanggal_indo(now()) }}</td>
                        </tr>
                        <tr>
                            <td style="padding-bottom: 70px">Direktur</td>
                        </tr>

                        <tr>
                            <td style="font-weight: bold; text-decoration: underline;">Prof. Dr. Ir. H. Takdir Saili, M. Si.</td>
                        </tr>
                        <tr>
                            <td style="font-weight: bold;">NIP 196902121994031003</td>
                        </tr>
                    </table>
                </td>
            </tr>

        </table>
    </div>

</body>
</html>