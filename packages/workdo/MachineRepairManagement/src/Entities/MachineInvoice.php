<?php

namespace Workdo\MachineRepairManagement\Entities;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MachineInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'request_id',
        'customer_name',
        'customer_email',
        'issue_date',
        'due_date',
        'send_date',
        'status',
        'estimated_time',
        'service_charge',
        'workspace',
        'created_by',
    ];
    public static $statues = [
        'Draft',
        // 'Sent',
        'Unpaid',
        'Partialy Paid',
        'Paid',
    ];

    public static function countInvoices()
    {
        return MachineInvoice::where('workspace', '=', getActiveWorkSpace())->count();
    }
    public static function machineInvoiceNumberFormat($number,$company_id = null,$workspace = null)
    {
        if(!empty($company_id) && empty($workspace))
        {
            $company_settings = getCompanyAllSetting($company_id);
        }
        elseif(!empty($company_id) && !empty($workspace))
        {
            $company_settings = getCompanyAllSetting($company_id,$workspace);
        }
        else
        {
            $company_settings = getCompanyAllSetting();
        }
        $data = !empty($company_settings['machine_invoice_prefix']) ? $company_settings['machine_invoice_prefix'] : '#MRM';

        return $data. sprintf("%05d", $number);
    }

    public function items()
    {
        return $this->hasMany(MachineInvoiceDiagnosis::class, 'invoice_id', 'id');
    }
    public function payments()
    {
        return $this->hasMany(MachineInvoicePayment::class, 'invoice_id', 'id');
    }
    public function getSubTotal()
    {
        $subTotal = 0;
        foreach ($this->items as $product) {
            $subTotal += ($product->price * $product->quantity);
        }
        return $subTotal;
    }
    public function getTotalDiscount()
    {
        $totalDiscount = 0;
        foreach($this->items as $product)
        {
            $totalDiscount += $product->discount;
        }
        return $totalDiscount;
    }
    public static function taxRate($taxRate, $price, $quantity,$discount = 0)
    {
        return ($taxRate / 100) * (($price * $quantity) - $discount);
    }
    public static function tax($taxes)
    {
        if(module_is_active('ProductService'))
        {
            $taxArr = explode(',', $taxes);
            $taxes  = [];
            foreach($taxArr as $tax)
            {
                $taxes[] = \Workdo\ProductService\Entities\Tax::find($tax);
            }

            return $taxes;
        }
        else
        {
            return [];
        }
    }

    public static function totalTaxRate($taxes)
    {
        if(module_is_active('ProductService'))
        {
            $taxArr  = explode(',', $taxes);
            $taxRate = 0;
            foreach($taxArr as $tax)
            {
                $tax     = \Workdo\ProductService\Entities\Tax::find($tax);
                $taxRate += !empty($tax->rate) ? $tax->rate : 0;
            }
            return $taxRate;
        }
        else
        {
            return 0;
        }
    }
    public function getTotalTax()
    {
        $totalTax = 0;
        foreach ($this->items as $product)
        {
            if(module_is_active('ProductService'))
            {
                $taxes = $this->totalTaxRate($product->tax);
            }
            else
            {
                $taxes = 0;
            }
            $totalTax += ($taxes / 100) * (($product->price * $product->quantity) - $product->discount);
        }

        return $totalTax;
    }
    public function getServiceCharge()
    {
        return ($this->service_charge);
    }
    public function getTotal()
    {
        return ($this->getSubTotal() - $this->getTotalDiscount() + $this->getTotalTax() + $this->getServiceCharge());
    }
    public function getDue()
    {
        $due = 0;
        foreach ($this->payments as $payment)
        {
            $due += $payment->amount;
        }

        return ($this->getTotal() - $due);
    }

    public static function total_quantity($type, $quantity, $product_id)
    {
        if(module_is_active('ProductService'))
        {
            $product      = \Workdo\ProductService\Entities\ProductService::find($product_id);
            if(!empty($product)){
                if(($product->type == 'product'))
                {
                    $pro_quantity = $product->quantity;
                    if($type == 'minus')
                    {
                        $product->quantity = $pro_quantity - $quantity;
                    }
                    else
                    {
                        $product->quantity = $pro_quantity + $quantity;
                    }
                    $product->save();
                }
            }
        }
    }

    public static function addProductStock($product_id, $quantity, $type, $description,$type_id)
    {
        $stocks                = new \Workdo\Account\Entities\StockReport();
        $stocks->product_id    = $product_id;
        $stocks->quantity	   = $quantity;
        $stocks->type          = $type;
        $stocks->type_id       = $type_id;
        $stocks->description   = $description;
        $stocks->workspace     = getActiveWorkSpace();
        $stocks->created_by    = creatorId();
        $stocks->save();
    }

    public static function getDiagnosisReportChart()
    {
        $purchases = Invoice::with('items')
            ->whereDate('created_at', '>',  \Carbon\Carbon::now()->subDays(10))
            ->where('account_type','MachineRepairManagement')->where('created_by', creatorId())
            ->where('workspace', getActiveWorkSpace())
            ->orderBy('created_at')
            ->get()
            ->groupBy(function ($val) {
                return \Carbon\Carbon::parse($val->created_at)->format('dm');
            });

        $total = [];

        if (!empty($purchases) && count($purchases) > 0) {
            foreach ($purchases as $day => $onepurchase) {
                $totals = 0;

                foreach ($onepurchase as $purchase) {
                    $items = $purchase->items;

                    $totals += $purchase->getTotal($items);
                }

                $total[$day] = $totals;
            }
        }
        $d = date("d");
        $m = date("m");
        $y = date("Y");

        for ($i = 0; $i <= 9; $i++) {
            $date                      = date('Y-m-d', mktime(0, 0, 0, $m, ($d - $i), $y));
            $purchasesArray['label'][] = date('d-M', strtotime($date));
            $date                      = date('dm', strtotime($date));
            $purchasesArray['value'][] = array_key_exists($date, $total) ? $total[$date] : 0;;
        }

        return $purchasesArray;
    }
}
