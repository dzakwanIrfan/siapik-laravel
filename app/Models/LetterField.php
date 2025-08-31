<?php

namespace App\Models;

use App\Models\LetterType;
use Illuminate\Database\Eloquent\Model;

class LetterField extends Model
{
    protected $table = 'letter_fields';
    protected $primaryKey = 'intLetterField_ID';
    public $incrementing = true;
    protected $keyType = 'int';

        public $timestamps = false; // Nonaktifkan timestamps bawaan

    protected $fillable = [
        'intLetterType_ID',
        'txtFieldName',
        'txtFieldLabel',
        'txtFieldType',
        'jsonFieldOptions',
        'bitRequired',
        'intFieldOrder',
        'jsonFieldValidation',
        'bitAkademik',
        'bitActive',
        'txtInsertedBy',
        'txtInserted',
        'txtUpdatedBy',
        'txtUpdated',
    ];

    protected $casts = [
        'jsonFieldOptions'    => 'array',
        'jsonFieldValidation' => 'array',
    ];

    public function letterType()
    {
        return $this->belongsTo(LetterType::class, 'intLetterType_ID', 'intLetterType_ID');
    }
}
