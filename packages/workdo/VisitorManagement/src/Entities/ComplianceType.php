<?php

namespace Workdo\VisitorManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ComplianceType extends Model
{
    use HasFactory;
    protected $table = 'compliance_types';

    protected $fillable = ['name','workspace','created_by'];
    protected static function newFactory()
    {
        return \Workdo\VisitorManagement\Database\factories\ComplianceTypeFactory::new();
    }
}
