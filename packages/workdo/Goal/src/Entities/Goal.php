<?php

namespace Workdo\Goal\Entities;

use App\Models\Invoice;
use Workdo\Account\Entities\Revenue;
use Workdo\Account\Entities\Bill;
use Workdo\Account\Entities\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'from',
        'to',
        'amount',
        'workspace',
        'is_display',
        'created_by',
    ];

    public static $goalType = [
        'Invoice',
        'Bill',
        'Revenue',
        'Payment',
    ];



    public function target($type, $from, $to, $amount)
    {
        $total    = 0;
        $fromDate = $from . '-01';
        $toDate   = $to . '-01';
        if(\Workdo\Goal\Entities\Goal::$goalType[$type] == 'Invoice')
        {
            $fromDate = $from;
            $toDate   = $to;
            $invoices = Invoice:: select('*')->where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->where('issue_date', '>=', $fromDate)->where('issue_date', '<=', $toDate)->get();
            $total    = 0;
            foreach($invoices as $invoice)
            {
                $total += $invoice->getTotal();
            }
        }
        elseif(\Workdo\Goal\Entities\Goal::$goalType[$type] == 'Bill')
        {
            $bills = Bill:: select('*')->where('created_by', creatorId())->where('workspace' ,'=',  getActiveWorkSpace())->where('bill_date', '<=', $toDate)->get();
            $total = 0;
            foreach($bills as $bill)
            {
                $total += $bill->getTotal();
            }
        }
        elseif(\Workdo\Goal\Entities\Goal::$goalType[$type] == 'Revenue')
        {
            $revenues = Revenue:: select('*')->where('created_by', creatorId())->where('workspace' ,'=',  getActiveWorkSpace())->where('date', '<=', $toDate)->get();
            $total    = 0;

            foreach($revenues as $revenue)
            {
                $total += $revenue->amount;
            }
        }
        elseif(\Workdo\Goal\Entities\Goal::$goalType[$type] == 'Payment')
        {
            $payments = Payment:: select('*')->where('created_by', creatorId())->where('workspace' ,'=',  getActiveWorkSpace())->where('date', '<=', $toDate)->get();
            $total    = 0;

            foreach($payments as $payment)
            {
                $total += $payment->amount;
            }

        }

        $data['percentage'] = ($total * 100) / $amount;
        $data['total']      = $total;

        return $data;
    }

    public static function getProgressColor($percentage)
    {
        $color = '';
        if ($percentage <= 20) {
            $color = 'danger';
        } elseif ($percentage > 20 && $percentage <= 40) {
            $color = 'warning';
        } elseif ($percentage > 40 && $percentage <= 60) {
            $color = 'info';
        } elseif ($percentage > 60 && $percentage <= 80) {
            $color = 'primary';
        } elseif ($percentage >= 80) {
            $color = 'success';
        }
        return $color;
    }


}
