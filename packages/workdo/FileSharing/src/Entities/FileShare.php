<?php

namespace Workdo\FileSharing\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class FileShare extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'related_id',
        'user_id',
        'type',
        'file_path',
        'file_name',
        'file_size',
        'file_status',
        'auto_destroy',
        'filesharing_type',
        'email',
        'is_pass_enable',
        'password',
        'total_downloads',
        'description',
        'workspace',
        'created_by',
    ];

    public static $statues = [
        'Available'     => 'Available',
        'Not Available' => 'Not Available',
    ];

    public static $share_mode = [
        'email' => 'Email',
        'link'  => 'Link',
    ];

    protected static function newFactory()
    {
        return \Workdo\FileSharing\Database\factories\FileShareFactory::new();
    }
}
