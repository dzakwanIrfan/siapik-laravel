<?php

namespace App\Models;

use App\Models\LetterField;
use App\Models\LetterRequirement;
use Illuminate\Database\Eloquent\Model;

class LetterType extends Model
{
    protected $table = 'letter_types';

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

    protected $casts = [
        'txtInserted' => 'datetime',
        'txtUpdated' => 'datetime',
    ];

    public function letterFields()
    {
        return $this->hasMany(LetterField::class)->orderBy('field_order');
    }
}
