<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LetterType;
use App\Models\LetterField;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubmissionController extends Controller
{
    // Halaman daftar surat (kartu + modal kosong)
    public function index()
    {
        $letter_types = LetterType::where('bitActive', 1)
            ->orderBy('txtNameLetterType')
            ->withCount(['letterFields' => function ($q) {
                $q->where('bitActive', 1);
            }])
            ->get();

        // Tidak preload fields; akan diambil via AJAX partial agar ringan
        return view('pages.submissions.create.index', compact('letter_types'));
    }

    // HTML partial untuk field dinamis di modal
    public function form($letterTypeId)
    {
        $letterType = LetterType::with(['letterFields' => function ($q) {
                $q->where('bitActive', 1)->orderBy('intFieldOrder');
            }/*, 'requirements' */]) // aktifkan jika kamu punya tabel requirements
            ->findOrFail($letterTypeId);

        $fieldOptions = $this->prepareFieldOptions($letterType->letterFields);

        return view('pages.submissions.create.components._dynamic_fields', [
            'letterType'   => $letterType,
            'fieldOptions' => $fieldOptions,
        ]);
    }

    // Simpan pengajuan
    public function store(Request $request)
    {
        $request->merge([
            'letter_type_id' => $request->input('letter_type_id'),
        ]);

        $letterType = LetterType::with(['letterFields' => function ($q) {
            $q->where('bitActive', 1)->orderBy('intFieldOrder');
        }])->findOrFail($request->letter_type_id);

        $rules = $this->buildValidationRules($letterType->letterFields);

        // NB: field file dari letterFields akan tervalidasi dari Parsley front-end;
        // kalau ingin validasi file di backend, tambahkan mapping 'file' ke 'mimes|max' di jsonFieldValidation.
        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            // === Simpan header pengajuan (silakan ganti ke model Submission kamu) ===
            // Contoh minimal tanpa model khusus:
            $submissionId = DB::table('submissions')->insertGetId([
                'user_id'         => auth()->id(),
                'intLetterType_ID'=> $letterType->intLetterType_ID,
                'submission_number' => $this->generateSubmissionNumber(),
                'status'          => 'pending',
                'submitted_at'    => now(),
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            // === Simpan detail field dinamis ===
            $fields = $request->input('fields', []);
            foreach ($letterType->letterFields as $field) {
                $name = $field->txtFieldName;

                // nilai text/number/date/email/select
                $value = Arr::get($fields, $name);

                // kalau type file dan ada file diunggah
                if ($field->txtFieldType === 'file' && $request->hasFile("fields.$name")) {
                    $file = $request->file("fields.$name");
                    $path = $file->store("submissions/{$submissionId}", 'public');
                    $value = $path;
                }

                DB::table('submission_data')->insert([
                    'submission_id' => $submissionId,
                    'field_name'    => $name,
                    'field_value'   => is_scalar($value) ? (string)$value : json_encode($value),
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            // === (Opsional) Simpan dokumen persyaratan jika punya tabel requirements ===
            // foreach ($letterType->requirements as $req) { ... }

            DB::commit();

            return redirect()
                ->route('submission.create')
                ->with('success', 'Pengajuan berhasil dibuat!');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()->withInput()->with('error', 'Gagal menyimpan pengajuan.');
        }
    }

    // ===== Helpers =====

    private function prepareFieldOptions($letterFields)
    {
        $options = [];
        foreach ($letterFields as $field) {
            if ($field->txtFieldType === 'select' && !empty($field->jsonFieldOptions)) {
                $options[$field->txtFieldName] = $this->getSelectOptions($field->jsonFieldOptions);
            }
        }
        return $options;
    }

    private function getSelectOptions($fieldOptions)
    {
        // Format:
        // {"source":"database","table":"majors","value_field":"id","label_field":"name"}
        // atau {"source":"static","options":{"S2":"Magister","S3":"Doktor"}}

        if (!is_array($fieldOptions)) {
            // casting dari model sudah array; kalau masih string → decode
            $fieldOptions = @json_decode($fieldOptions, true) ?: [];
        }

        if (($fieldOptions['source'] ?? null) === 'database') {
            $table = $fieldOptions['table'] ?? null;
            $value = $fieldOptions['value_field'] ?? 'id';
            $label = $fieldOptions['label_field'] ?? 'name';

            switch ($table) {
                // case 'majors':
                //     return \App\Models\Major::pluck($label, $value)->toArray();

                // case 'concentrates':
                //     return \App\Models\Concentrate::pluck($label, $value)->toArray();

                case 'users':
                    return User::where('role', 'dosen')->pluck($label, $value)->toArray();

                default:
                    return [];
            }
        }

        if (($fieldOptions['source'] ?? null) === 'static') {
            // options bisa array numerik/string atau key=>label
            $opts = $fieldOptions['options'] ?? [];
            return is_array($opts) ? $opts : [];
        }

        return [];
    }

    private function buildValidationRules($letterFields)
    {
        $rules = [];

        foreach ($letterFields as $field) {
            $fieldRules = [];

            if ((int)$field->bitRequired === 1) {
                $fieldRules[] = 'required';
            }

            // Tambah rule default berdasar tipe
            switch ($field->txtFieldType) {
                case 'email':
                    $fieldRules[] = 'email';
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                case 'number':
                    $fieldRules[] = 'numeric';
                    break;
                case 'file':
                    // kalau ada config di jsonFieldValidation, baca di bawah
                    break;
            }

            // Tambah rule kustom dari jsonFieldValidation (array)
            $extra = $field->jsonFieldValidation ?? [];
            if (is_string($extra)) $extra = @json_decode($extra, true) ?: [];
            foreach ($extra as $key => $val) {
                // contoh: ["max"=>255] → "max:255", ["mimes"=>"pdf,jpg"]
                if (is_bool($val)) {
                    if ($val) $fieldRules[] = $key; // e.g. "required"
                } else {
                    $fieldRules[] = "{$key}:{$val}";
                }
            }

            if (!empty($fieldRules)) {
                // name pakai fields[txtFieldName] supaya rapi
                $rules["fields.{$field->txtFieldName}"] = $fieldRules;
            }
        }

        return $rules;
    }

    private function generateSubmissionNumber()
    {
        $year  = date('Y');
        $month = date('m');

        $count = DB::table('submissions')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count() + 1;

        return sprintf('RCP/%s/%s/%04d', $year, $month, $count);
    }
}
