<?php

namespace App\Exports;

use App\Models\Submission;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FilteredSubmissionsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    // Terima filter dari controller
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    /**
     * Judul kolom di file Excel.
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
        ];
    }

    /**
     * Query data dari database berdasarkan filter.
     */
    public function query()
    {
        $query = Submission::with(['user.mahasiswaProfile.major', 'letterType']);

        if (!empty($this->filters['status'])) {
            $query->where('txtStatus', $this->filters['status']);
        }

        // Terapkan filter tanggal jika ada
        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $query->whereBetween('dtmUpdated', [$this->filters['start_date'], $this->filters['end_date'] . ' 23:59:59']);
        }

        // Terapkan filter prodi jika ada
        if (!empty($this->filters['major_id'])) {
            $query->whereHas('user.mahasiswaProfile', function ($q) {
                $q->where('intMajor_ID', $this->filters['major_id']);
            });
        }

        // Terapkan filter jenis surat jika ada
        if (!empty($this->filters['letter_type_id'])) {
            $query->where('intLetterType_ID', $this->filters['letter_type_id']);
        }

        return $query->orderBy('dtmUpdated', 'desc');
    }

    /**
     * Format setiap baris data.
     * @param Submission $submission
     */
    public function map($submission): array
    {
        return [
            $submission->txtReceiptNumber,
            $submission->txtLetterNumber ?? '-',
            $submission->user->txtFullName ?? 'N/A',
            $submission->user->mahasiswaProfile->txtNIM ?? '-',
            $submission->user->mahasiswaProfile->major->txtNameMajor ?? '-',
            $submission->letterType->txtNameLetterType ?? 'N/A',
            $submission->dtmUpdated ? $submission->dtmUpdated->format('d-m-Y H:i') : '-',
        ];
    }
}
