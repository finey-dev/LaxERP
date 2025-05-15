<?php

namespace Workdo\BusinessProcessMapping\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Related extends Model
{
    use HasFactory;
    
    protected $table = 'business_process_mapping_related';

    protected $fillable = [
        'related',
        'model_name',
    ];
    
    protected static function newFactory()
    {
        return \Workdo\BusinessProcessMapping\Database\factories\RelatedFactory::new();
    }
}