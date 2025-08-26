<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DosenProfile extends Model
{
    protected $table = 'dosen_profiles';

    protected $primaryKey = 'intDosenProfile_ID';

    protected $fillable = [
        'intUser_ID',
        'txtNIP',
        'txtNIDN',
        'txtFieldOfKnowledge',
        'intMajor_ID',
        'txtInsertedBy',
        'dtmInserted',
        'txtUpdatedBy',
        'dtmUpdated',
        'bitActive',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'intUser_ID', 'intUser_ID');
    }

    public function major()
    {
        return $this->belongsTo(Major::class, 'intMajor_ID', 'intMajor_ID');
    }
}
