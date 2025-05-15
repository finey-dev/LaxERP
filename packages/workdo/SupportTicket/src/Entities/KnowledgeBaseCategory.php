<?php

namespace Workdo\SupportTicket\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KnowledgeBaseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','workspace_id','created_by'
    ];

   
}
