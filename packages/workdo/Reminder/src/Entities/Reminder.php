<?php

namespace Workdo\Reminder\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Invoice;
use App\Models\User;
use Workdo\Account\Entities\Bill;
use Workdo\Lead\Entities\Deal;
use Workdo\Lead\Entities\Lead;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_select',
        'date',
        'day',
        'action',
        'to',
        'module',
        'module_value',
        'message',
        'workspace',
        'created_by',
    ];
    protected $casts = [
        'to' => 'array'
    ];
    protected static function module_value($reminder){
        if ($reminder->module == 'Invoice'){
            $data =Invoice::where('id',$reminder->module_value)->first();
            $module_value = Invoice::invoiceNumberFormat($data->invoice_id);
        }elseif ($reminder->module == 'Bill') {
            $data = Bill::where('id',$reminder->module_value)->first();
            $module_value = Bill::billNumberFormat($data->bill_id);
        }elseif ($reminder->module == 'User') {
            $module_value = User::where('id',$reminder->module_value)->first();
            if(!empty($module_value)){
                $module_value = $module_value->name;
            }
        }elseif ($reminder->module == 'Lead') {
            $module_value = Lead::where('id',$reminder->module_value)->first();
            if(!empty($module_value)){
                $module_value = $module_value->name;
            }
        }else {
            $module_value = Deal::where('id',$reminder->module_value)->first();
            if(!empty($module_value)){
                $module_value = $module_value->name;
            }
        }
        return $module_value;

    }

}
