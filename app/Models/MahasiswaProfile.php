<?php

namespace App\Models;

use App\Models\User;
use App\Models\Major;
use App\Models\Concentrate;
use Illuminate\Database\Eloquent\Model;

class MahasiswaProfile extends Model
{
    protected $table = 'mahasiswa_profiles';

    protected $primaryKey = 'intMahasiswaProfile_ID';

    protected $fillable = [
        'intUser_ID',
        'txtNIM',
        'intMajor_ID',
        'intConcentrate_ID',
        'txtYear',
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

    public function concentrate()
    {
        return $this->belongsTo(Concentrate::class, 'intConcentrate_ID', 'intConcentrate_ID');
    }
}
