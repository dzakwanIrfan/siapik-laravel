<?php

namespace App\Models;

use App\Models\LetterField;
use Illuminate\Database\Eloquent\Model;

class LetterType extends Model
{
    protected $table = 'letter_types';
    protected $primaryKey = 'intLetterType_ID';
    public $incrementing = true;
    public $timestamps = false;
    protected $keyType = 'int';

        protected $fillable = [
        'txtNameLetterType',
        'txtCode',
        'txtDescription',
        'txtTemplatePath',
        'bitActive',
        'txtInsertedBy',
        'txtInserted',
        'txtUpdatedBy',
        'txtUpdated',
    ];

    public function letterFields()
    {
        return $this->hasMany(LetterField::class, 'intLetterType_ID', 'intLetterType_ID')
                    ->orderBy('intFieldOrder');
    }
}
