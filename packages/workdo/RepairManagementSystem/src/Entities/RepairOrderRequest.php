<?php

namespace Workdo\RepairManagementSystem\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RepairOrderRequest extends Model
{
    use HasFactory;

    protected $guarded = [];


    protected static function newFactory()
    {
        return \Workdo\RepairManagementSystem\Database\factories\RepairOrderRequestFactory::new();
    }
    public function RepairTechnician()
    {
        return $this->hasOne(RepairTechnician::class, 'id', 'repair_technician');
    }
    public function repairParts()
    {
        return $this->hasMany(RepairPart::class, 'repair_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(RepairInvoicePayment::class, 'repair_id', 'id');
    }

    public function repairInvoice()
    {
        return $this->hasOne(RepairInvoice::class, 'repair_id', 'id')->first();
    }

    public function getRepairCharge()
    {
        $repairCharge = 0;
        $repairCharge = $this->repairInvoice()->repair_charge;
        return $repairCharge;
    }
    public function getSubTotal()
    {
        $subTotal = 0;
        foreach ($this->repairParts as $product) {
            $subTotal += ($product->price * $product->quantity);
        }
        return $subTotal;
    }
    public function getTotalDiscount()
    {
        $totalDiscount = 0;
        foreach($this->repairParts as $product)
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

    public function getTotal()
    {
        return ($this->getSubTotal() - $this->getTotalDiscount() + $this->getTotalTax() + $this->getRepairCharge());
    }

    public function getDue()
    {
        $due = 0;
        foreach ($this->payments as $payment)
        {
            $due += $payment->amount;
        }
        return ($this->getTotal() - $due);
        // $difference = $this->getTotal() - $due;
        // $rounded_due = round($difference, 2); // Round to two decimal places
        // return $rounded_due;
    }

    public function getTotalTax()
    {
        $totalTax = 0;
        foreach ($this->repairParts as $product)
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

    public function invoiceTotalCreditNote()
    {
        if(module_is_active('Account'))
        {
            return $this->hasMany(\Workdo\Account\Entities\CreditNote::class, 'invoice', 'id')->sum('amount');
        }
        else
        {
            return 0;
        }
    }

    public function invoiceTotalCustomerCreditNote()
    {
        if(module_is_active('Account'))
        {
            return $this->hasMany(\Workdo\Account\Entities\CustomerCreditNotes::class, 'invoice', 'id')->sum('amount');
        }
        else
        {
            return 0;
        }
    }

    public static function invoiceNumberFormat($number,$company_id = null,$workspace = null)
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
        $data = !empty($company_settings['repair_invoice_prefix']) ? $company_settings['repair_invoice_prefix'] : '#INVO0';

        return $data. sprintf("%05d", $number);
    }
}
