<?php

namespace App\Models;

use App\Models\FileSubmission;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table = 'files';
    protected $primaryKey = 'intFile_ID';
    protected $fillable = [
        'txtFileName',
        'txtOriginalFileName',
        'txtFilePath',
        'txtInsertedBy',
        'dtmInserted',
        'txtUpdatedBy',
        'dtmUpdated',
        'bitActive',
    ];

    public function fileSubmissions()
    {
        return $this->hasMany(FileSubmission::class, 'intFile_ID', 'intFile_ID');
    }
}
