<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Surat Pengembalian ke Instansi - Universitas Halu Oleo</title>
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

		.header-text { flex: 1; }

		.header h1,
		.header h2,
		.header h3 { margin: 0; text-transform: uppercase; }
		.header h1 { font-size: 14pt; font-weight: 400; }
		.header h2 { font-size: 14pt; font-weight: 400; }
		.header h3 { font-size: 14pt; font-weight: 600; }

		.header .address {
			font-size: 11pt;
			margin: 0 0 5px 0;
			line-height: 1.2;
		}

		.letter-head {
			display: flex;
			justify-content: space-between;
			font-size: 12pt;
		}

		.letter-head .left { text-align: left; }
		.letter-head .right { text-align: right; }

		.meta-table { margin-top: 5px; }
		.meta-table td { padding: 0 8px 0 0; vertical-align: top; }

		.subject { margin: 5px 0; font-size: 12pt; }

		.recipient { margin: 25px 0; font-size: 12pt; line-height: 1.2; }

		.content { margin: 20px 0 10px 0; text-align: justify; line-height: 1.2; }

		.student-info { margin: 10px 0; line-height: 1.2; }

		.closing { margin-top: 10px; text-align: justify; }

		.signature {
			margin-top: 60px;
			float: right;
			text-align: left;
			clear: both;
		}

		.signature-title { margin-bottom: 20px; }
		.signature-name {
			font-weight: bold;
			margin-top: 80px;
			text-decoration: underline;
		}
		.signature-nip { font-weight: bold; margin-top: 5px; }

		.blank-line {
			border-bottom: 1px solid #000;
			display: inline-block;
			width: 120px;
			margin: 0 5px;
		}
	</style>
</head>
<body>
	<!-- KOP SURAT -->
	<div class="header">
		<img src="{{ asset('images/logo.png') }}" alt="Logo Universitas Halu Oleo" class="logo" />
		<div class="header-text">
			<h1>Kementerian Pendidikan Tinggi, Sains<br />dan Teknologi</h1>
			<h2>Universitas Halu Oleo</h2>
			<h3>Program Pascasarjana</h3>
			<div class="address">
				Kampus Pascasarjana Jl. Mayjen S.Parman Kemaraya Kendari, 93121<br />
				Telp/Fax (0401) 3127187, Email : ppsuho@uho.ac.id, Web. : www.pasca.uho.ac.id
			</div>
		</div>
	</div>

	<!-- NOMOR & TANGGAL -->
	<div class="letter-head">
		<div class="left">
			<table class="meta-table" style="border: none;">
				<tr>
					<td>Nomor</td>
					<td>:</td>
					<td>{{ $submission->txtLetterNumber ?? 'Belum diisi oleh Akademik' }}</td>
				</tr>
				<tr>
					<td>Lampiran</td>
					<td>:</td>
					<td>-</td>
				</tr>
			</table>
		</div>
		<div class="right">Kendari, {{ tanggal_indo(now()) }}</div>
	</div>

	<!-- PERIHAL -->
	<div class="subject">Perihal : Pengembalian ke Instansi</div>

	<!-- PENERIMA -->
	<div class="recipient">
		Yth. {{ $data['txtTujuanSurat'] }}<br />
		Di-<br />
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tempat
	</div>

	<!-- PEMBUKA -->
	<div class="content">
		Dengan ini kami mengucapkan selamat atas keberhasilan Saudara atas nama :
	</div>

	<!-- DATA MAHASISWA -->
	<div class="student-info" style="margin-left: 1cm">
		<table style="width: 100%; border: none;">
			<tr>
				<td style="width: 200px; padding: 0;">Nama</td>
				<td style="padding: 0;">: {{ $submission->user->txtFullName }}</td>
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
				<td style="padding: 0;">: {{ $submission->user->mahasiswaProfile->concentrate->txtNameConcentrate ?? '-' }}</td>
			</tr>
			<tr>
				<td style="padding: 0;">Jenjang Pendidikan</td>
				<td style="padding: 0;">: {{ $submission->user->mahasiswaProfile->major->txtStrata == 'S2' ? 'Magister S2' : 'Doktor S3' }}</td>
			</tr>
		</table>
	</div>

	<!-- ISI SURAT -->
	<div class="content">
		Telah menyelesaikan studi Doktor pada Program Studi {{ $submission->user->mahasiswaProfile->major->txtNameMajor }} Universitas Halu Oleo pada tanggal {{ tanggal_indo($data['dtmSelesai']) }}. Sehubungan dengan itu maka yang bersangkutan kami kembalikan ke instansi Bapak/Ibu/Saudara(i) agar dapat kembali mengabdikan diri sebagaimana mestinya.
	</div>

	<div class="closing">
		Demikian kami sampaikan, atas perhatian dan kerjasama Bapak/Ibu/Saudara(i) diucapkan terima kasih.
	</div>

	<!-- TANDA TANGAN -->
	<div class="signature">
		<div class="signature-title">Direktur<br />Program Pascasarjana UHO</div>

		<div class="signature-name">Prof. Dr. Ir. La Ode Safuan, M.P.</div>
		<div class="signature-nip">NIP 196512311991031024</div>
	</div>
</body>
</html>