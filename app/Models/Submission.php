<?php

namespace App\Models;

use App\Models\User;
use App\Models\LetterType;
use App\Models\SubmissionValue;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $table = 'submissions';
    protected $primaryKey = 'intSubmission_ID';
    protected $fillable = [
        'intLetterType_ID',
        'intUser_ID',
        'txtStatus',
        'txtKaprodiNote',
        'txtAkademikNote',
        'jsonDataForm',
        'txtInsertedBy',
        'dtmInserted',
        'txtUpdatedBy',
        'dtmUpdated',
        'bitActive',
    ];

    public function fileSubmissions()
    {
        return $this->hasMany(FileSubmission::class, 'intSubmission_ID', 'intSubmission_ID');
    }
}
