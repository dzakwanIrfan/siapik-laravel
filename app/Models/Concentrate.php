<?php

namespace App\Models;

use App\Models\Major;
use Illuminate\Database\Eloquent\Model;

class Concentrate extends Model
{
    protected $table = 'concentrates';

    protected $primaryKey = 'intConcentrate_ID';

    protected $fillable = [
        'intMajor_ID',
        'txtNameConcentrate',
        'txtInsertedBy',
        'dtmInserted',
        'txtUpdatedBy',
        'dtmUpdated',
        'bitActive',
    ];

    public function major()
    {
        return $this->belongsTo(Major::class, 'intMajor_ID', 'intMajor_ID');
    }
}
