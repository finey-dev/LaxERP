<?php

namespace Workdo\VisitorManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VisitorDocument extends Model
{
    use HasFactory;
    protected $table = 'visitor_documents';

    protected $fillable = ['visitor_id','document_type','document_number','status','date','workspace','created_by'];

    public function visitor(){
        return $this->belongsTo(Visitors::class);
    }
    public function document(){
        return $this->belongsTo(VisitorDocumentType::class,'document_type');
    }
}
