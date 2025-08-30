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
	
	
	<!-- Receipt Display -->
	<div class="card" id="receipt-container">
		<div class="card-body">
			@include('pages.submissions.receipt.includes._format_receipt')
		</div>
	</div>
	
	<!-- Action Buttons -->
	<div class="card">
		<div class="card-body">
			<div class="d-flex justify-content-center gap-2">
				<a href="{{ route('submissions.receipt.download', $submission->intSubmission_ID) }}" 
				   class="btn btn-success">
					<i class="bi bi-download"></i> Download PDF
				</a>
				<button onclick="printReceipt()" class="btn btn-primary">
					<i class="bi bi-printer"></i> Cetak Receipt
				</button>
			</div>
		</div>
	</div>

	<!-- Print Styles -->
	<style>
		@media print {
			body * {
				visibility: hidden;
			}
			#receipt-container, #receipt-container * {
				visibility: visible;
			}
			#receipt-container {
				position: absolute;
				left: 0;
				top: 0;
				width: 100%;
			}
			.card {
				border: none !important;
				box-shadow: none !important;
			}
		}
	</style>

	<script>
		function printReceipt() {
			// Open print dialog
			window.print();
		}
	</script>
@endsection