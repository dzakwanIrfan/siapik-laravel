<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionStatus extends Model
{
    protected $table = 'submission_statuses';
    protected $primaryKey = 'intSubmissionStatus_ID';

    protected $fillable = [
        'intSubmission_ID',
        'txtStatus',
        'txtInReview',
        'bitActive',
        'txtInsertedBy',
        'dtmInserted',
        'txtUpdatedBy',
        'dtmUpdated',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class, 'intSubmission_ID', 'intSubmission_ID');
    }
}
