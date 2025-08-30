@extends('layouts.default_layout', ['layoutNavbar' => true])
@section('title', 'Receipt Pengajuan')

@section('page_title')
	<!-- BEGIN breadcrumb -->
	<nav aria-label="breadcrumb" class="breadcrumb-header float-end float-lg-end">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="">Home</a></li>
			<li class="breadcrumb-item"><a href="{{ route('submissions.index') }}">Riwayat Pengajuan</a></li>
			<li class="breadcrumb-item active" aria-current="page">Receipt</li>
		</ol>
	</nav>
	<!-- END breadcrumb -->

	<!-- BEGIN page-header -->
	<div>
		<h3>Receipt Pengajuan</h3>
		<p class="text-subtitle text-muted">Sistem Informasi Pembuatan Surat</p>
	</div>
	<!-- END page-header -->
@endsection

@section('content')
	<div class="alert alert-primary"><i class="bi bi-exclamation-triangle"></i> Cetak bukti pengajuan untuk dapat mengambil dokumen.</div>
	<div class="card">
		<div class="card-body">
			@include('pages.submissions.receipt.includes._format_receipt')
		</div>
	</div>
@endsection
