<?php

namespace App\Models;

use App\Models\DosenProfile;
use App\Models\FileSubmission;
use App\Models\MahasiswaProfile;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    protected $table = 'users';
    protected $primaryKey = 'intUser_ID';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'txtFullName',
        'txtEmail',
        'txtPassword',
        'txtGender',
        'txtBirthPlace',
        'dtmBirthDate',
        'txtPhone',
        'txtInsertedBy',
        'dtmInserted',
        'txtUpdatedBy',
        'dtmUpdated',
        'bitActive',
    ];

    protected $hidden = ['txtPassword'];

    protected $casts = [
        'dtmBirthDate' => 'datetime',
        'dtmInserted'  => 'datetime',
        'dtmUpdated'   => 'datetime',
        'bitActive'    => 'boolean',
        'txtPassword'  => 'hashed',
    ];

    public function getAuthPassword()
    {
        return $this->txtPassword;
    }

    public function mahasiswaProfile()
    {
        return $this->hasOne(MahasiswaProfile::class, 'intUser_ID', 'intUser_ID');
    }

    public function fileSubmissions()
    {
        return $this->hasMany(FileSubmission::class, 'intUser_ID', 'intUser_ID');
    }

    public function dosenProfile()
    {
        return $this->hasOne(DosenProfile::class, 'intUser_ID', 'intUser_ID');
    }

    public function submissionChats()
    {
        return $this->hasMany(SubmissionChat::class, 'intUser_ID', 'intUser_ID');
    }
}
