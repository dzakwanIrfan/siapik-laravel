<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LetterType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Controller;

class LetterTypeController extends Controller
{
    public function index()
    {
        return view('master.letter_types');
    }

    public function data()
    {
        $letterTypes = LetterType::query();
        return DataTables::of($letterTypes)
            ->addColumn('action', function ($letterType) {
                $editBtn = '<a href="javascript:void(0)" class="btn btn-warning btn-sm edit-btn" data-id="' . $letterType->intLetterType_ID . '">Edit</a>';
                $editTemplateBtn = '<a href="javascript:void(0)" class="btn btn-info btn-sm edit-template-btn" data-id="' . $letterType->intLetterType_ID . '">Edit Template</a>';
                $deleteBtn = '<a href="javascript:void(0)" class="btn btn-danger btn-sm delete-btn" data-id="' . $letterType->intLetterType_ID . '">Hapus</a>';
                return '<div class="d-flex gap-2">' . $editBtn . $editTemplateBtn . $deleteBtn . '</div>';
            })
            ->editColumn('bitActive', function ($letterType) {
                return $letterType->bitActive ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Tidak Aktif</span>';
            })
            ->rawColumns(['action', 'bitActive'])
            ->make(true);
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'txtNameLetterType' => 'required|string|max:255',
            'txtCode' => 'required|string|unique:letter_types,txtCode',
            'txtDescription' => 'required|string',
            'txtTemplatePath' => 'required|string',
            'bitActive' => 'required|boolean',
        ])->validate();

        LetterType::create([
            'txtNameLetterType' => $request->txtNameLetterType,
            'txtCode' => $request->txtCode,
            'txtDescription' => $request->txtDescription,
            'txtTemplatePath' => $request->txtTemplatePath,
            'bitActive' => $request->bitActive,
            'txtInsertedBy' => Auth::user()->txtFullName,
            'txtInserted' => now(),
        ]);

        return response()->json(['success' => 'Jenis Surat berhasil ditambahkan.']);
    }

    public function edit(LetterType $letter_type)
    {
        return response()->json($letter_type);
    }

    public function update(Request $request, LetterType $letter_type)
    {
        Validator::make($request->all(), [
            'txtNameLetterType' => 'required|string|max:255',
            'txtCode' => 'required|string|unique:letter_types,txtCode,' . $letter_type->intLetterType_ID . ',intLetterType_ID',
            'txtDescription' => 'required|string',
            'txtTemplatePath' => 'required|string',
            'bitActive' => 'required|boolean',
        ])->validate();

        $letter_type->update([
            'txtNameLetterType' => $request->txtNameLetterType,
            'txtCode' => $request->txtCode,
            'txtDescription' => $request->txtDescription,
            'txtTemplatePath' => $request->txtTemplatePath,
            'bitActive' => $request->bitActive,
            'txtUpdatedBy' => Auth::user()->txtFullName,
            'txtUpdated' => now(),
        ]);

        return response()->json(['success' => 'Jenis Surat berhasil diperbarui.']);
    }

    public function destroy(LetterType $letter_type)
    {
        $letter_type->delete();
        return response()->json(['success' => 'Jenis Surat berhasil dihapus.']);
    }

    // Method baru untuk template editing dengan iframe approach
    public function editTemplate(LetterType $letter_type)
    {
        try {
            $templatePath = $this->getTemplateFilePath($letter_type->txtTemplatePath);
            
            if (!File::exists($templatePath)) {
                return response()->json(['error' => 'Template file tidak ditemukan.'], 404);
            }

            $content = File::get($templatePath);
            
            return response()->json([
                'success' => true,
                'content' => $content,
                'fileName' => basename($templatePath),
                'letterType' => $letter_type
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal membaca template: ' . $e->getMessage()], 500);
        }
    }

    // Method untuk menampilkan preview template dalam iframe
    public function previewTemplateHtml(Request $request, LetterType $letter_type)
    {
        try {
            $content = $request->get('content');
            
            if ($content) {
                // Jika ada content dari editor, gunakan itu untuk preview
                return response($content)->header('Content-Type', 'text/html');
            } else {
                // Jika tidak ada content, ambil dari file
                $templatePath = $this->getTemplateFilePath($letter_type->txtTemplatePath);
                
                if (!File::exists($templatePath)) {
                    return response('<html><body><h1>Template file tidak ditemukan</h1></body></html>')
                            ->header('Content-Type', 'text/html');
                }

                $content = File::get($templatePath);
                return response($content)->header('Content-Type', 'text/html');
            }
        } catch (\Exception $e) {
            return response('<html><body><h1>Error: ' . $e->getMessage() . '</h1></body></html>')
                    ->header('Content-Type', 'text/html');
        }
    }

    public function updateTemplate(Request $request, LetterType $letter_type)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Konten template tidak boleh kosong.'], 422);
        }

        try {
            $templatePath = $this->getTemplateFilePath($letter_type->txtTemplatePath);
            
            if (!File::exists($templatePath)) {
                return response()->json(['error' => 'Template file tidak ditemukan.'], 404);
            }

            // Update file template langsung tanpa backup
            File::put($templatePath, $request->content);

            // Log perubahan untuk audit trail
            \Log::info('Template updated', [
                'letter_type_id' => $letter_type->intLetterType_ID,
                'letter_type_name' => $letter_type->txtNameLetterType,
                'file_path' => $templatePath,
                'updated_by' => Auth::user()->txtFullName,
                'updated_at' => now()
            ]);

            return response()->json(['success' => 'Template berhasil diperbarui.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memperbarui template: ' . $e->getMessage()], 500);
        }
    }

    private function getTemplateFilePath($templatePath)
    {
        // Konversi dari template path (misal: templates.surat_izin_penelitian) 
        // ke file path sebenarnya (misal: resources/letters_html/surat_izin_penelitian.html)
        
        if (str_contains($templatePath, 'templates.')) {
            $fileName = str_replace('templates.', '', $templatePath);
            return resource_path('views/templates/' . $fileName . '.blade.php');
        }
        
        // Jika sudah format file path langsung
        return resource_path($templatePath);
    }
}