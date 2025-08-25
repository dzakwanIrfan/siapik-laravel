<?php

namespace App\Http\Controllers;

use App\Models\LetterType;

class SubmissionController extends Controller
{
    public function create()
    {
        // Ambil semua jenis surat aktif + field aktifnya (urut sesuai intFieldOrder)
        $letter_types = LetterType::where('bitActive', 1)
            ->with(['letterFields' => function ($q) {
                $q->where('bitActive', 1)->orderBy('intFieldOrder');
            }])
            ->get();

        // Susun preload untuk dipakai JS di Blade (modal dinamis)
        $preload = [];
        foreach ($letter_types as $type) {
            $preload[$type->intLetterType_ID] = $type->letterFields->map(function ($f) {
                return [
                    'id'         => $f->intLetterField_ID,
                    'name'       => $f->txtFieldName,
                    'label'      => $f->txtFieldLabel,
                    'type'       => $f->txtFieldType,        // text|textarea|select|date|file|number
                    'options'    => $f->jsonFieldOptions,    // array|null (otomatis dari $casts)
                    'required'   => (bool) $f->bitRequired,
                    'validation' => $f->jsonFieldValidation, // associative array untuk Parsley
                ];
            })->values()->toArray();
        }

        // NB: View canvas sekarang expect $preload & $letter_types
        return view('pages.submissions.create.index', compact('letter_types', 'preload'));
    }
}
