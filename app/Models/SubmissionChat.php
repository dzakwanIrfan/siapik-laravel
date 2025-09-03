<?php

namespace App\Models;

use App\Models\User;
use App\Models\Submission;
use Illuminate\Database\Eloquent\Model;

class SubmissionChat extends Model
{
    protected $table = 'submission_chats';
    protected $primaryKey = 'intSubmissionChat_ID';
    public $timestamps = true;

    protected $fillable = [
        'intSubmission_ID',
        'intUser_ID',
        'txtMessage',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class, 'intSubmission_ID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'intUser_ID');
    }
}
