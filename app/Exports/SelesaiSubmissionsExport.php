<?php

namespace App\Exports;

use App\Models\Submission;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SelesaiSubmissionsExport implements FromQuery, WithHeadings, WithMapping
{
    /**
     * Mendefinisikan judul untuk setiap kolom di file Excel.
     */
    public function headings(): array
    {
        return [
            'Nomor Tanda Terima',
            'Nomor Surat',
            'Nama Pemohon',
            'NIM',
            'Prodi',
            'Jenis Surat',
            'Tanggal Selesai',
            'Link Download Surat Final',
        ];
    }

    /**
     * Mengambil data dari database.
     * Hanya mengambil submission dengan status 'Selesai'.
     */
    public function query()
    {
        return Submission::query()
            ->where('txtStatus', 'Selesai')
            ->with(['user.mahasiswaProfile.major', 'letterType']); // Eager loading untuk performa
    }

    /**
     * Memetakan setiap baris data ke format array yang diinginkan.
     *
     * @param Submission $submission
     */
    public function map($submission): array
    {
        return [
            $submission->txtReceiptNumber,
            $submission->txtLetterNumber,
            $submission->user->txtFullName ?? 'User tidak ditemukan',
            $submission->user->mahasiswaProfile->txtNIM ?? '-',
            $submission->user->mahasiswaProfile->major->txtNameMajor ?? '-',
            $submission->letterType->txtNameLetterType ?? 'Jenis surat tidak ditemukan',
            $submission->dtmUpdated ? $submission->dtmUpdated->format('d-m-Y H:i') : '-',
            $submission->txtFinalFile ? asset('storage/' . $submission->txtFinalFile) : 'Tidak ada file',
        ];
    }
}
