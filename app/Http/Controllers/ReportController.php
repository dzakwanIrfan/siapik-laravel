<?php

namespace App\Http\Controllers;

use App\Models\Major;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\FilteredSubmissionsExport;

class ReportController extends Controller
{
    /**
     * Menampilkan halaman utama laporan.
     */
    public function index()
    {
        // Ambil data untuk filter dropdown
        $majors = Major::orderBy('txtNameMajor')->get();
        $letterTypes = DB::table('letter_types')->orderBy('txtNameLetterType')->get();

        // Status yang relevan untuk laporan
        $statuses = [
            'Sedang ditinjau Kaprodi', 'Disetujui Kaprodi', 'Ditolak Kaprodi',
            'Disetujui Akademik', 'Ditolak Akademik', 'Sudah dicetak', 'Selesai'
        ];

        return view('reports.index', compact('majors', 'letterTypes', 'statuses'));
    }

    /**
     * Mengambil data untuk tabel laporan berdasarkan filter (AJAX).
     */
    public function data(Request $request)
    {
        // Validasi input filter
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ]);

        $query = Submission::with(['user.mahasiswaProfile.major', 'letterType'])
            ->where('bitActive', 1); // Hanya ambil data aktif

        // Terapkan filter tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('dtmInserted', [$request->start_date, $request->end_date . ' 23:59:59']);
        }

        // Terapkan filter prodi
        if ($request->filled('major_id')) {
            $query->whereHas('user.mahasiswaProfile', function ($q) use ($request) {
                $q->where('intMajor_ID', $request->major_id);
            });
        }

        // Terapkan filter jenis surat
        if ($request->filled('letter_type_id')) {
            $query->where('intLetterType_ID', $request->letter_type_id);
        }

        // Terapkan filter status
        if ($request->filled('status')) {
            $query->where('txtStatus', $request->status);
        }

        return \DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('user_name', fn($row) => $row->user->txtFullName ?? '-')
            ->editColumn('major_name', fn($row) => $row->user->mahasiswaProfile->major->txtNameMajor ?? '-')
            ->editColumn('letter_type_name', fn($row) => $row->letterType->txtNameLetterType ?? '-')
            ->editColumn('date', fn($row) => $row->dtmInserted->format('d M Y H:i'))
            ->editColumn('status', function($row) {
                // Logika badge status bisa ditambahkan di sini
                return $row->txtStatus;
            })
            ->make(true);
    }

    public function export(Request $request)
    {
        // Ambil semua parameter filter dari request
        $filters = $request->only(['start_date', 'end_date', 'major_id', 'letter_type_id', 'status']);

        $fileName = 'laporan_surat_selesai_' . date('Y-m-d') . '.xlsx';

        // Panggil class export dan kirimkan filter
        return Excel::download(new FilteredSubmissionsExport($filters), $fileName);
    }
}
