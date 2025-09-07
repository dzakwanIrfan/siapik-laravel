<?php

namespace App\Models;

use App\Models\User;
use App\Models\LetterType;
use App\Models\SubmissionChat;
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
        'txtLetterNumber',
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

    protected $casts = [
        'dtmInserted' => 'datetime',
        'dtmUpdated' => 'datetime',
        'bitActive' => 'boolean',
        'jsonDataForm' => 'array'
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

    public function chats()
    {
        return $this->hasMany(SubmissionChat::class, 'intSubmission_ID', 'intSubmission_ID');
    }
}