<?php

namespace Workdo\VisitorManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Visitors extends Model
{

    use HasFactory;

    protected $fillable = [
        'first_name','last_name','email','phone','workspace','created_by'
	];

   
    public function visitLogs()
    {
        return $this->hasMany(VisitLog::class);
    }
}
