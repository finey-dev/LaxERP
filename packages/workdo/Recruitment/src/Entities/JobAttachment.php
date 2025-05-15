<?php

namespace Workdo\Recruitment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'file_name',
        'file_path',
        'file_size',
        'workspace',
        'created_by',
    ];
    
    protected static function newFactory()
    {
        return \Workdo\Recruitment\Database\factories\JobAttachmentFactory::new();
    }
}
