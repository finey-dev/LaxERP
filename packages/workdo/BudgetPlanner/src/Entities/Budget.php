<?php

namespace Workdo\BudgetPlanner\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'period',
        'from',
        'income_data',
        'expense_data',
        'workspace',
        'created_by'
    ];

    public static $period = [
        'monthly' => 'Monthly',
        'quarterly' => 'Quarterly',
        'half-yearly' =>'Half Yearly',
        'yearly' => 'Yearly',

    ];

    protected static function newFactory()
    {
        return \Workdo\BudgetPlanner\Database\factories\BudgetFactory::new();
    }

    public static function percentage($actual,$budget)
    {
        $percentage = $budget*100/$actual;
        return  number_format($percentage,2);

    }
}
