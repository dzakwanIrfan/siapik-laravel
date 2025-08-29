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
	<div class="card border shadow-sm">
		<div class="card-header bg-white border-bottom">
			<div class="d-flex align-items-center justify-content-between">
				<div>
					<h5 class="mb-0">{{ $submission->letterType->txtNameLetterType }}</h5>
					<small class="text-muted">Kode: {{ $submission->letterType->txtCode }}</small>
				</div>
				<div class="text-end">
					<span class="badge bg-primary me-2">No: {{ $submission->txtReceiptNumber }}</span>
					<span class="badge bg-success text-uppercase">{{ $submission->txtStatus }}</span>
				</div>
			</div>
		</div>

		<div class="card-body">
			<div class="row my-3">
				<div class="col-md-6">
					<div class="list-group">
						<div class="list-group-item d-flex justify-content-between">
							<span class="text-muted">Tanggal</span>
							<span>{{ optional($submission->created_at)->format('d/m/Y H:i') }}</span>
						</div>
						@if ($submission->user)
							<div class="list-group-item d-flex justify-content-between">
								<span class="text-muted">Pemohon</span>
								<span>{{ $submission->user->name }}</span>
							</div>
						@endif
					</div>
				</div>
				<div class="col-md-6 text-md-end mt-3 mt-md-0">
					<a href="{{ route('submissions.download', $submission->intSubmission_ID) }}" class="btn btn-outline-primary me-2">
						<i class="bi bi-download me-1"></i> Unduh (PDF)
					</a>
					<button class="btn btn-primary" onclick="window.print()">
						<i class="bi bi-printer me-1"></i> Cetak
					</button>
				</div>
			</div>

			<div class="table-responsive">
				<table class="table table-striped align-middle">
					<thead>
						<tr>
							<th style="width: 30%">Field</th>
							<th>Nilai</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($submission->values->sortBy('intLetterField_ID') as $val)
							<tr>
								<td class="fw-medium">{{ $val->txtFieldLabel }}</td>
								<td>
									@if ($val->txtFieldType === 'file' && $val->txtFieldValue)
										@php
											$meta = is_array($val->jsonFieldMeta)
												? $val->jsonFieldMeta
												: (is_string($val->jsonFieldMeta) ? json_decode($val->jsonFieldMeta, true) : []);
											$url  = asset('storage/' . $val->txtFieldValue);
											$mime = $meta['mime'] ?? '';
											$isImg = substr($mime, 0, 6) === 'image/';
										@endphp
										<div class="d-flex align-items-center gap-3">
											<a href="{{ $url }}" target="_blank" class="btn btn-sm btn-outline-primary">
												<i class="bi bi-box-arrow-up-right me-1"></i> Buka
											</a>
											@if ($isImg)
												<img src="{{ $url }}" alt="preview" class="img-thumbnail" style="max-height: 64px;">
											@endif
											<span class="text-muted small">
												{{ $meta['original_name'] ?? basename($val->txtFieldValue) }}
												@if (!empty($meta['size']))
													· {{ number_format($meta['size'] / 1024, 1) }} KB
												@endif
											</span>
										</div>
									@else
										{{ $val->txtFieldValue }}
									@endif
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>

			<div class="alert alert-info mt-4">
				<i class="bi bi-info-circle me-2"></i>
				Simpan atau cetak halaman ini sebagai bukti pengajuan.
			</div>
		</div>
	</div>
@endsection
