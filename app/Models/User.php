<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

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
}
