@php($layoutNavbar = true)

@extends('layouts.default_layout')
@section('title', 'Sistem Persuratan')

@section('page_title')
  <!-- BEGIN breadcrumb -->
  <nav aria-label="breadcrumb" class="breadcrumb-header float-end float-lg-end">
      <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="">Dashboard</a></li>
          <li class="breadcrumb-item active" aria-current="page">Daftar Surat</li>
      </ol>
  </nav>
  <!-- END breadcrumb -->

  <!-- BEGIN page-header -->
  <div>
    <h3>Daftar Surat</h3>
    <p class="text-subtitle text-muted">Sistem Informasi Administrasi Pelayanan Akademik</p>
  </div>
  <!-- END page-header -->
@endsection

@section('content')
  <div class="card">
    <div class="card-header">
      <h4 class="card-title">Default Layout</h4>
    </div>
    <div class="card-body">
      Lorem ipsum dolor sit amet consectetur adipisicing elit. Magnam, commodi? Ullam quaerat similique iusto
      temporibus, vero aliquam praesentium, odit deserunt eaque nihil saepe hic deleniti? Placeat delectus
      quibusdam ratione ullam!
    </div>
  </div>
@endsection
