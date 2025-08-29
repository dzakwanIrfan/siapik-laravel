<?php

namespace App\Models;

use App\Models\Submission;
use Illuminate\Database\Eloquent\Model;

class FileSubmission extends Model
{
    protected $table = 'file_submissions';
    protected $primaryKey = 'intFileSubmission_ID';
    protected $fillable = [
        'intFile_ID',
        'intSubmission_ID',
        'txtInsertedBy',
        'dtmInserted',
        'txtUpdatedBy',
        'dtmUpdated',
        'bitActive',
    ];

    public function file()
    {
        return $this->belongsTo(File::class, 'intFile_ID', 'intFile_ID');
    }

    public function submission()
    {
        return $this->belongsTo(Submission::class, 'intSubmission_ID', 'intSubmission_ID');
    }
}
