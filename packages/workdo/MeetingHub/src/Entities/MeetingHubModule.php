<?php

namespace Workdo\MeetingHub\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MeetingHubModule extends Model
{
    use HasFactory;

    protected $table = "meetinghub_modules";
    protected $fillable = [
        'module',
        'submodule',
    ];
    
}
