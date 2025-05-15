<?php

namespace Workdo\RecurringInvoiceBill\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecurringInvoiceBill extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'recurring_type',
        'cycles',
        'recurring_duration',
        'day_type',
        'count',
        'pending_cycle',
        'modify_date',
        'modify_due_date',
        'dublicate_invoice',
        'workspace',
        'created_by',
    ];

    public static $recuuring_type = [
        'no' => 'No',
        '1 day' => 'Every 1 Day',
        '2 day' => 'Every 2 Day',
        '3 day' => 'Every 3 Day',
        '4 day' => 'Every 4 Day',
        '1 week' =>'Every 1 week',
        '2 week' =>'Every 2 week',
        '3 week' =>'Every 3 week',
        '4 week' =>'Every 4 week',
        '1 month' =>'Every 1 month',
        '2 month' =>'Every 2 month',
        '3 month' =>'Every 3 month',
        '4 month' =>'Every 4 month',
        'custom' => 'Custom',



    ];
    public static $day_type = [
        'day' => 'Day(s)',
        'week' => 'Week(s)',
        'month' => 'Month(s)',
        'year' => 'Year(s)',
    ];
}
