<?php

namespace App\Models;

use App\Models\LetterField;
use Illuminate\Database\Eloquent\Model;

class LetterType extends Model
{
    protected $table = 'letter_types';
    protected $primaryKey = 'intLetterType_ID';
    public $incrementing = true;
    protected $keyType = 'int';

    public function letterFields()
    {
        return $this->hasMany(LetterField::class, 'intLetterType_ID', 'intLetterType_ID')
                    ->orderBy('intFieldOrder');
    }
}
