<?php

namespace App\Models;

use App\Models\LetterType;
use Illuminate\Database\Eloquent\Model;

class LetterField extends Model
{
    protected $table = 'letter_fields';

    protected $fillable = [
        'intLetterType_ID',
        'txtFieldName',
        'txtFieldLabel',
        'txtFieldType',
        'jsonFieldOptions',
        'bitRequired',
        'intFieldOrder',
        'jsonFieldValidation',
        'bitActive',
        'txtInsertedBy',
        'txtInserted',
        'txtUpdatedBy',
        'txtUpdated',
    ];

    protected $casts = [
        'jsonFieldOptions' => 'array',
        'jsonFieldValidation' => 'array',
        'txtInserted' => 'datetime',
        'txtUpdated' => 'datetime',
    ];

    public function letterType()
    {
        return $this->belongsTo(LetterType::class, 'intLetterType_ID');
    }
}
