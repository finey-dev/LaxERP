<?php

namespace Workdo\Quotation\Entities;

use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\ProductService\Entities\ProductService;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'account_type',
        'customer_id',
        'quotation_type',
        'warehouse_id',
        'quotation_date',
        'category_id',
        'quotation_module',
        'converted_pos_id',
        'is_converted',
        'workspace',
        'created_by',
    ];


    public function customer()
    {
        return  $this->hasOne(User::class, 'id', 'customer_id');
    }



    public function warehouse()
    {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }


    public function items()
    {
        return $this->hasMany(QuotationProduct::class, 'quotation_id', 'id');
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
        foreach ($this->items as $product) {
            $totalDiscount += $product->discount;
        }
        return $totalDiscount;
    }
    public function getTotalTax()
    {
        $totalTax = 0;
        foreach ($this->items as $product) {
            if (module_is_active('ProductService')) {
                $taxes = Quotation::totalTaxRate($product->tax);
            } else {
                $taxes = 0;
            }
            $totalTax += ($taxes / 100) * (($product->price * $product->quantity) - $product->discount);
        }

        return $totalTax;
    }

    public static function taxRate($taxRate, $price, $quantity, $discount = 0)
    {
        return ($taxRate / 100) * (($price * $quantity) - $discount);
    }

    public static function tax($taxes)
    {
        $taxArr = explode(',', $taxes);
        $taxes  = [];
        if (module_is_active('ProductService')) {
            foreach ($taxArr as $tax) {
                $taxes[] = \Workdo\ProductService\Entities\Tax::find($tax);
            }
        }
        return $taxes;
    }

    public static function totalTaxRate($taxes)
    {
        $taxArr  = explode(',', $taxes);
        $taxRate = 0;
        if (module_is_active('ProductService')) {
            foreach ($taxArr as $tax) {
                $tax     =  \Workdo\ProductService\Entities\Tax::find($tax);
                $taxRate += !empty($tax->rate) ? $tax->rate : 0;
            }
        }
        return $taxRate;
    }

    public function getTotal()
    {
        return ($this->getSubTotal() - $this->getTotalDiscount() + $this->getTotalTax());
    }

    public static function quotationNumberFormat($number, $company_id = null, $workspace = null)
    {
        if (!empty($company_id) && empty($workspace)) {
            $company_settings = getCompanyAllSetting($company_id);
        } elseif (!empty($company_id) && !empty($workspace)) {
            $company_settings = getCompanyAllSetting($company_id, $workspace);
        } else {
            $company_settings = getCompanyAllSetting();
        }
        $data = !empty($company_settings['quotation_prefix']) ? $company_settings['quotation_prefix'] : '#QUOO0';
        return $data . sprintf("%05d", $number);
    }


    public static function quotationProduct($value, $session_key = 'pos')
    {
        $id = $value->product_id;
        $product = ProductService::find($id);
        $productquantity = 0;

        if (!$product) {
            return response()->json([
                'code' => 404,
                'status' => 'Error',
                'error' => __('Product not found!'),
            ], 404);
        }

        $productquantity = $product->getProductQuantity();

        if ($session_key == 'pos' && $productquantity == 0) {
            return response()->json([
                'code' => 404,
                'status' => 'Error',
                'error' => __('This product is out of stock!'),
            ], 404);
        }

        $productname = $product->name;
        $productprice = $product->sale_price != 0 ? $product->sale_price : 0;
        $originalquantity = (int) $productquantity;
        $quantity = $value->quantity;
        $taxes = Quotation::tax($product->tax_id);
        $totalTaxRate = Quotation::totalTaxRate($product->tax_id);

        // Calculate tax
        $tax = ($productprice * $totalTaxRate * $quantity) / 100;

        // Calculate subtotal
        $subtotal = $productprice * $quantity + $tax;

        // Retrieve discounts for all products
        $quotationProducts = QuotationProduct::where('product_id', $id)->get();
        $discount = $quotationProducts->sum('discount');

        // Calculate net price
        $netPrice = $subtotal - $discount;

        // Generate product tax details
        $product_tax = '';
        $product_tax_id = [];
        foreach ($taxes as $tax) {
            $product_tax .= !empty($tax) ? "<span class='badge badge-primary'>" . $tax->name . ' (' . $tax->rate . '%)' . "</span><br>" : '';
            $product_tax_id[] = !empty($tax) ? $tax->id : 0;
        }

        if (empty($product_tax)) {
            $product_tax = "-";
        }

        // Construct product data
        $productData = [
            'name' => $productname,
            'quantity' => $quantity,
            'price' => $productprice,
            'id' => $id,
            'tax' => $totalTaxRate,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'netPrice' => $netPrice,
            'originalquantity' => $originalquantity,
            'product_tax' => $product_tax,
            'product_tax_id' => !empty($product_tax_id) ? implode(',', $product_tax_id) : 0,
        ];

        // Update session cart
        $cart = session()->get($session_key);
        if (!$cart) {
            $cart = [];
        }

        if ($originalquantity < $quantity && $session_key == 'pos') {
            return response()->json([
                'code' => 404,
                'status' => 'Error',
                'error' => __('This product is out of stock!'),
            ], 404);
        }

        $cart[$id] = $productData;
        session()->put($session_key, $cart);

        return response()->json([
            'code' => 200,
            'status' => 'Success',
            'success' => $productname . __(' added to cart successfully!'),
            'product' => $productData,
            'carttotal' => $cart,
        ]);
    }


    public static function templateData()
    {
        $arr = [];
        $arr['colors'] = [
            '003580',
            '666666',
            '6676ef',
            'f50102',
            'f9b034',
            'fbdd03',
            'c1d82f',
            '37a4e4',
            '8a7966',
            '6a737b',
            '050f2c',
            '0e3666',
            '3baeff',
            '3368e6',
            'b84592',
            'f64f81',
            'f66c5f',
            'fac168',
            '46de98',
            '40c7d0',
            'be0028',
            '2f9f45',
            '371676',
            '52325d',
            '511378',
            '0f3866',
            '48c0b6',
            '297cc0',
            'ffffff',
            '000',
        ];
        $arr['templates'] = [
            "template1" => "New York",
            "template2" => "Toronto",
            "template3" => "Rio",
            "template4" => "London",
            "template5" => "Istanbul",
            "template6" => "Mumbai",
            "template7" => "Hong Kong",
            "template8" => "Tokyo",
            "template9" => "Sydney",
            "template10" => "Paris",
        ];
        return $arr;
    }


    public static function hex2rgb($hex)
    {
        $hex = str_replace("#", "", $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = array(
            $r,
            $g,
            $b,
        );

        //return implode(",", $rgb); // returns the rgb values separated by commas
        return $rgb; // returns an array with the rgb values
    }


    public static function getFontColor($color_code)
    {
        $rgb = self::hex2rgb($color_code);
        $R = $G = $B = $C = $L = $color = '';

        $R = (floor($rgb[0]));
        $G = (floor($rgb[1]));
        $B = (floor($rgb[2]));

        $C = [
            $R / 255,
            $G / 255,
            $B / 255,
        ];

        for ($i = 0; $i < count($C); ++$i) {
            if ($C[$i] <= 0.03928) {
                $C[$i] = $C[$i] / 12.92;
            } else {
                $C[$i] = pow(($C[$i] + 0.055) / 1.055, 2.4);
            }
        }

        $L = 0.2126 * $C[0] + 0.7152 * $C[1] + 0.0722 * $C[2];

        if ($L > 0.179) {
            $color = 'black';
        } else {
            $color = 'white';
        }

        return $color;
    }

    public function getDue()
    {
        $due = 0;

        foreach ($this->payments as $payment)
        {
            $due += $payment->amount;
        }

        return ($this->getTotal() - $due) - $this->invoiceTotalCreditNote();
    }
}
