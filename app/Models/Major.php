<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    protected $table = 'majors';

    protected $primaryKey = 'intMajor_ID';

    protected $fillable = [
        'txtNameMajor',
        'txtStrata',
        'txtTitle',
        'txtShortTitle',
        'txtInsertedBy',
        'dtmInserted',
        'txtUpdatedBy',
        'dtmUpdated',
        'bitActive',
    ];

    public function concentrates()
    {
        return $this->hasMany(Concentrate::class, 'intMajor_ID', 'intMajor_ID');
    }
}
