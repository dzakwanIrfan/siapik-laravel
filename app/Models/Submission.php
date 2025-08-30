<?php

namespace App\Models;

use App\Models\User;
use App\Models\LetterType;
use App\Models\SubmissionValue;
use App\Models\SubmissionStatus;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $table = 'submissions';
    protected $primaryKey = 'intSubmission_ID';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'intLetterType_ID',
        'intUser_ID',
        'txtReceiptNumber',
        'txtStatus',
        'txtKaprodiNote',
        'txtAkademikNote',
        'jsonDataForm',
        'txtInsertedBy',
        'dtmInserted',
        'txtUpdatedBy',
        'dtmUpdated',
        'bitActive',
        'txtInsertedBy', 'dtmInserted',
        'txtUpdatedBy', 'dtmUpdated',
    ];

    public function letterType()
    {
        return $this->belongsTo(LetterType::class, 'intLetterType_ID', 'intLetterType_ID');
    }

    public function values()
    {
        return $this->hasMany(SubmissionValue::class, 'intSubmission_ID', 'intSubmission_ID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'intUser_ID', 'intUser_ID');
    }

    public function statuses()
    {
        return $this->hasMany(SubmissionStatus::class, 'intSubmission_ID', 'intSubmission_ID')
            ->orderByDesc('dtmInserted');
    }
}
