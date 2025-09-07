@php($layoutNavbar = true)

@extends('layouts.default_layout')
@section('title', 'Dashboard - Sistem Persuratan')

@section('page_title')
  <nav aria-label="breadcrumb" class="breadcrumb-header float-end float-lg-end">
      <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
      </ol>
  </nav>

  <div>
    <h3>Dashboard</h3>
    <p class="text-subtitle text-muted">Welcome back, {{ Auth::user()->txtFullName }} 👋</p>
  </div>
@endsection

@section('content')
  <!-- Stats Cards -->
  <div class="row" id="stats-cards">
    <div class="col-6 col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body px-4 py-4-5">
                <div class="row">
                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                        <div class="stats-icon blue mb-2">
                            <span style="font-size: 24px;">📄</span>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                        <h6 class="text-muted font-semibold">Total Pengajuan</h6>
                        <h6 class="font-extrabold mb-0" id="total-submissions">{{ $dashboardData['total_submissions'] }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body px-4 py-4-5">
                <div class="row">
                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                        <div class="stats-icon yellow mb-2">
                            <span style="font-size: 24px;">⏳</span>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                        <h6 class="text-muted font-semibold">Pending</h6>
                        <h6 class="font-extrabold mb-0" id="pending-submissions">{{ $dashboardData['pending'] }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body px-4 py-4-5">
                <div class="row">
                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                        <div class="stats-icon green mb-2">
                            <span style="font-size: 24px;">🔄</span>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                        <h6 class="text-muted font-semibold">On Progress</h6>
                        <h6 class="font-extrabold mb-0" id="progress-submissions">{{ $dashboardData['on_progress'] }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body px-4 py-4-5">
                <div class="row">
                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                        <div class="stats-icon red mb-2">
                            <span style="font-size: 24px;">✅</span>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                        <h6 class="text-muted font-semibold">Closed</h6>
                        <h6 class="font-extrabold mb-0" id="closed-submissions">{{ $dashboardData['closed'] }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>

  @if(Auth::user()->hasRole('akademik'))
  <!-- Additional Stats for Akademik -->
  <div class="row">
    <div class="col-6 col-lg-4 col-md-6">
        <div class="card">
            <div class="card-body px-4 py-4-5">
                <div class="row">
                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                        <div class="stats-icon purple mb-2">
                            <span style="font-size: 24px;">👥</span>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                        <h6 class="text-muted font-semibold">Total Pengguna</h6>
                        <h6 class="font-extrabold mb-0">{{ $dashboardData['total_users'] ?? 0 }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-4 col-md-6">
        <div class="card">
            <div class="card-body px-4 py-4-5">
                <div class="row">
                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                        <div class="stats-icon info mb-2">
                            <span style="font-size: 24px;">🎓</span>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                        <h6 class="text-muted font-semibold">Mahasiswa</h6>
                        <h6 class="font-extrabold mb-0">{{ $dashboardData['total_mahasiswa'] ?? 0 }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-4 col-md-6">
        <div class="card">
            <div class="card-body px-4 py-4-5">
                <div class="row">
                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                        <div class="stats-icon warning mb-2">
                            <span style="font-size: 24px;">👨‍🏫</span>
                        </div>
                    </div>
                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                        <h6 class="text-muted font-semibold">Dosen</h6>
                        <h6 class="font-extrabold mb-0">{{ $dashboardData['total_dosen'] ?? 0 }}</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
  @endif

  <!-- Charts Section -->
  <div class="row">
      <div class="col-lg-8">
          <div class="card">
              <div class="card-header d-flex justify-content-between align-items-center">
                  <h4>Statistik Pengajuan</h4>
                  <select class="form-select form-select-sm" id="chart-type-selector" style="width: auto;">
                      <option value="monthly">Per Bulan</option>
                      <option value="letter_type">Per Jenis Surat</option>
                  </select>
              </div>
              <div class="card-body">
                  <div id="chart-submissions"></div>
              </div>
          </div>
      </div>
      <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4>Status Pengajuan</h4>
            </div>
            <div class="card-body">
                <div id="chart-status"></div>
            </div>
        </div>
      </div>
  </div>

  <!-- Recent Submissions Table -->
  <div class="row">
      <div class="col-12">
          <div class="card">
              <div class="card-header">
                  <h4>Pengajuan Terbaru</h4>
              </div>
              <div class="card-body">
                  <div class="table-responsive">
                      <table class="table table-striped" id="recent-submissions-table">
                          <thead>
                              <tr>
                                  <th>No. Resi</th>
                                  <th>Jenis Surat</th>
                                  @if(!Auth::user()->hasRole('mahasiswa') && !Auth::user()->hasRole('dosen'))
                                  <th>Nama</th>
                                  <th>Program Studi</th>
                                  @endif
                                  <th>Status</th>
                                  <th>Tanggal</th>
                                  <th>Aksi</th>
                              </tr>
                          </thead>
                          <tbody></tbody>
                      </table>
                  </div>
              </div>
          </div>
      </div>
  </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize charts
    initializeCharts();
    
    // Initialize DataTable
    initializeDataTable();
    
    // Chart type selector
    $('#chart-type-selector').on('change', function() {
        loadSubmissionsChart($(this).val());
    });
    
    // Refresh stats every 5 minutes
    setInterval(refreshStats, 300000);
});

function initializeCharts() {
    // Load status chart
    loadStatusChart();
    
    // Load submissions chart (default: monthly)
    loadSubmissionsChart('monthly');
}

function loadStatusChart() {
    fetch('/api/dashboard/chart?type=status')
        .then(response => response.json())
        .then(data => {
            const options = {
                series: data.series,
                chart: {
                    type: 'donut',
                    height: 350
                },
                labels: data.labels,
                colors: ['#ffc107', '#28a745', '#dc3545'],
                legend: {
                    position: 'bottom'
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            height: 250
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };
            
            const chart = new ApexCharts(document.querySelector("#chart-status"), options);
            chart.render();
        })
        .catch(error => {
            console.error('Error loading status chart:', error);
        });
}

function loadSubmissionsChart(type) {
    fetch(`/api/dashboard/chart?type=${type}`)
        .then(response => response.json())
        .then(data => {
            // Clear existing chart
            const chartElement = document.querySelector("#chart-submissions");
            chartElement.innerHTML = '';
            
            let options = {
                series: data.series,
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 4,
                        horizontal: false,
                        columnWidth: '50%'
                    }
                },
                dataLabels: {
                    enabled: false
                },
                xaxis: {
                    categories: data.labels,
                    labels: {
                        rotate: -45,
                        maxHeight: 120
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function (val) {
                            return Math.floor(val); // Pastikan tidak ada desimal
                        }
                    },
                    min: 0,
                    forceNiceScale: true,
                    decimalsInFloat: 0 // Tidak ada desimal
                },
                colors: ['#435ebe'],
                grid: {
                    borderColor: '#e0e6ed'
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + (type === 'monthly' ? ' pengajuan' : ' pengajuan')
                        }
                    }
                }
            };
            
            const chart = new ApexCharts(chartElement, options);
            chart.render();
        })
        .catch(error => {
            console.error('Error loading submissions chart:', error);
        });
}

function initializeDataTable() {
    $('#recent-submissions-table').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '/api/dashboard/recent-submissions',
            type: 'GET'
        },
        columns: [
            { data: 'receipt_number', name: 'receipt_number' },
            { data: 'letter_type', name: 'letter_type' },
            @if(!Auth::user()->hasRole('mahasiswa') && !Auth::user()->hasRole('dosen'))
            { data: 'user_name', name: 'user_name' },
            { data: 'major', name: 'major' },
            @endif
            { 
                data: 'status', 
                name: 'status',
                render: function(data) {
                    let badgeClass = 'secondary';
                    if (['Sedang ditinjau Kaprodi'].includes(data)) {
                        badgeClass = 'warning';
                    } else if (['Disetujui Kaprodi', 'Disetujui Akademik', 'Sudah dicetak'].includes(data)) {
                        badgeClass = 'primary';
                    } else if (['Selesai'].includes(data)) {
                        badgeClass = 'success';
                    } else if (['Ditolak Kaprodi', 'Ditolak Akademik'].includes(data)) {
                        badgeClass = 'danger';
                    }
                    return `<span class="badge bg-${badgeClass}">${data}</span>`;
                }
            },
            { data: 'created_at', name: 'created_at' },
            {
                data: 'url',
                name: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `<a href="${data}" class="btn btn-sm btn-outline-primary">Detail</a>`;
                }
            }
        ],
        order: [[{{ Auth::user()->hasRole('mahasiswa') || Auth::user()->hasRole('dosen') ? '4' : '5' }}, 'desc']],
        pageLength: 10,
        responsive: true,
    });
}

function refreshStats() {
    fetch('/api/dashboard/stats')
        .then(response => response.json())
        .then(data => {
            $('#total-submissions').text(data.total_submissions);
            $('#pending-submissions').text(data.pending);
            $('#progress-submissions').text(data.on_progress);
            $('#closed-submissions').text(data.closed);
        })
        .catch(error => {
            console.error('Error refreshing stats:', error);
        });
}
</script>
@endpush