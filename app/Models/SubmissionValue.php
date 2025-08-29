<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionValue extends Model
{
    protected $table = 'submission_values';
    protected $primaryKey = 'intSubmissionValue_ID';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'intSubmission_ID',
        'intLetterField_ID',
        'txtFieldName', 'txtFieldLabel', 'txtFieldType',
        'txtFieldValue',
        'jsonFieldMeta',
        'bitActive',
        'txtInsertedBy', 'txtInserted',
        'txtUpdatedBy', 'txtUpdated',
    ];

    protected $casts = [
        'jsonFieldMeta' => 'array',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class, 'intSubmission_ID', 'intSubmission_ID');
    }

    public function letterField()
    {
        return $this->belongsTo(LetterField::class, 'intLetterField_ID', 'intLetterField_ID');
    }
}
