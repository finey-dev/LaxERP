<?php

namespace Workdo\RecurringInvoiceBill\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Facades\Log;
use Workdo\RecurringInvoiceBill\Entities\RecurringInvoiceBill;
use App\Models\Invoice;
use App\Models\Setting;
use App\Models\InvoiceProduct;
use Workdo\Account\Entities\Bill;
use Workdo\Account\Entities\BillProduct;
use Workdo\Account\Entities\StockReport;

use DateTime;
use DateInterval;

class RecurringData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recurring:invoice-bill';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $superadmin = getAdminAllSetting();
        if(!empty($superadmin['recurring_invoice_bill']) && $superadmin['recurring_invoice_bill'] == 'on')
        {
            $recurring_data = RecurringInvoiceBill::where('modify_date','<=',date('Y-m-d'))->where('pending_cycle','!=',0)->get();

            foreach($recurring_data as $data){
                $setting = getCompanyAllSetting($data->created_by ,$data->workspace);
                if(!empty($setting['recurring_invoice_bill']) && $setting['recurring_invoice_bill'] == 'on'){
                    if($data->recurring_type == 'invoice'){
                        $invoice = invoice::where('id',$data->invoice_id)->where('created_by' ,$data->created_by)->where('workspace',$data->workspace)->first();

                        if(!empty($invoice)){
                            try{
                                $latest = getCompanyAllSetting($data->created_by ,$data->workspace)['invoice_starting_number'];
                                if($latest == null)
                                {
                                    $latest =  1;
                                }
                                $RecurringInvoice                   = new Invoice();
                                $RecurringInvoice->invoice_id       = $latest;
                                $RecurringInvoice->account_type     = $invoice['account_type'];
                                $RecurringInvoice->customer_id      = $invoice['customer_id'];
                                $RecurringInvoice->user_id          = $invoice['user_id'];
                                $RecurringInvoice->issue_date       = $data->modify_date;
                                $RecurringInvoice->due_date         = $data->modify_due_date;
                                $RecurringInvoice->send_date        = null;
                                $RecurringInvoice->category_id      = $invoice['category_id'];
                                $RecurringInvoice->status           = 0;
                                $RecurringInvoice->shipping_display = $invoice['shipping_display'];
                                $RecurringInvoice->invoice_module   = $invoice['invoice_module'];
                                $RecurringInvoice->invoice_template = $invoice['invoice_template'];
                                $RecurringInvoice->workspace        = $invoice['workspace'];
                                $RecurringInvoice->created_by       = $invoice['created_by'];
                                $RecurringInvoice->save();
                                $key = 'invoice_starting_number';
                                $id = $RecurringInvoice->invoice_id + 1;


                                if(!empty($key) && $id){
                                    $datas = [
                                        'key' => $key,
                                        'workspace' => $data->workspace,
                                        'created_by' => $data->created_by,
                                    ];
                                    Setting::updateOrInsert($datas, ['value' => $id]);
                                    comapnySettingCacheForget($data->created_by , $data->workspace);
                                }

                                if($RecurringInvoice)
                                {
                                    $invoiceProduct = InvoiceProduct::where('invoice_id',$data->invoice_id)->get();

                                    foreach($invoiceProduct as $product)
                                    {
                                        $duplicateProduct                 = new InvoiceProduct();
                                        $duplicateProduct->invoice_id     = $RecurringInvoice->id;
                                        $duplicateProduct->product_type   = $product->product_type;
                                        $duplicateProduct->product_id     = $product->product_id;
                                        $duplicateProduct->quantity       = $product->quantity;
                                        $duplicateProduct->tax            = $product->tax;
                                        $duplicateProduct->discount       = $product->discount;
                                        $duplicateProduct->price          = $product->price;
                                        $duplicateProduct->save();
                                    }
                                    $count =$data->count;
                                    $day   =$data->day_type;
                                    $date = new DateTime($data->modify_date);
                                    $due_date = new DateTime($data->modify_due_date);
                                    switch ($day) {
                                        case 'day':
                                            $date->add(new DateInterval('P'.$count.'D'));
                                            $modify_date = $date->format('Y-m-d');

                                            $due_date->add(new DateInterval('P'.$count.'D'));
                                            $modify_due_date = $due_date->format('Y-m-d');
                                            break;
                                        case 'month':
                                            $date->add(new DateInterval('P'.$count.'M'));
                                            $modify_date = $date->format('Y-m-d');

                                            $due_date->add(new DateInterval('P'.$count.'M'));
                                            $modify_due_date = $due_date->format('Y-m-d');
                                            break;
                                        case 'year':
                                            $date->add(new DateInterval('P'.$count.'Y'));
                                            $modify_date = $date->format('Y-m-d');

                                            $due_date->add(new DateInterval('P'.$count.'Y'));
                                            $modify_due_date = $due_date->format('Y-m-d');
                                            break;
                                        case 'week':
                                            $date->add(new DateInterval('P'.$count.'W'));
                                            $modify_date = $date->format('Y-m-d');

                                            $due_date->add(new DateInterval('P'.$count.'W'));
                                            $modify_due_date = $due_date->format('Y-m-d');
                                            break;
                                    }

                                    $duplicate_invoice_ids = [];
                                    if (!empty($data->dublicate_invoice)) {
                                        $duplicate_invoice_ids = explode(',', $data->dublicate_invoice);
                                        $duplicate_invoice_ids = array_map(function($item) {
                                            return trim($item, '[]');
                                        }, $duplicate_invoice_ids);
                                    }
                                    $duplicate_invoice_ids[] = $RecurringInvoice->id;
                                    $data->dublicate_invoice = implode(',', $duplicate_invoice_ids);
                                    $updatecycle = RecurringInvoiceBill::where('id', $data->id)
                                        ->where('recurring_type', 'invoice')
                                        ->where('created_by', $data->created_by)
                                        ->where('workspace', $data->workspace)
                                        ->first();
                                    $updatecycle->pending_cycle = $data->pending_cycle - 1;
                                    $updatecycle->modify_date = $modify_date;
                                    $updatecycle->modify_due_date = $modify_due_date;
                                    $updatecycle->dublicate_invoice = $data->dublicate_invoice;
                                    $updatecycle->save();
                                }

                            }catch (Exception $e) {
                            }
                        }
                    }
                    else{
                        $bill = Bill::where('id',$data->invoice_id)->where('created_by' ,$data->created_by)->where('workspace',$data->workspace)->first();
                            if(!empty($bill)){
                                $setting = getCompanyAllSetting($data->created_by ,$data->workspace);
                                $latest = $setting['bill_starting_number'];
                                try{

                                    if($latest == null)
                                    {
                                        $latest =  1;
                                    }
                                    $duplicateBill = new Bill();
                                    $duplicateBill->bill_id = $latest;
                                    $duplicateBill->vendor_id = $bill['vendor_id'];
                                    $duplicateBill->user_id = $bill['user_id'];
                                    $duplicateBill->bill_date = $data->modify_date;
                                    $duplicateBill->due_date = $data->modify_due_date;
                                    $duplicateBill->send_date = null;
                                    $duplicateBill->category_id = $bill['category_id'];
                                    $duplicateBill->order_number = $bill['order_number'];
                                    $duplicateBill->status = 0;
                                    $duplicateBill->bill_shipping_display = $bill['bill_shipping_display'];
                                    $duplicateBill->bill_module = $bill['bill_module'];
                                    $duplicateBill->created_by = $bill['created_by'];
                                    $duplicateBill->workspace = $bill['workspace'];
                                    $duplicateBill->save();

                                    $key = 'bill_starting_number';
                                    $id = $duplicateBill->bill_id + 1;


                                    if(!empty($key) && $id){
                                        $datas = [
                                            'key' => $key,
                                            'workspace' => $data->workspace,
                                            'created_by' => $data->created_by,
                                        ];
                                        Setting::updateOrInsert($datas, ['value' => $id]);
                                        comapnySettingCacheForget($data->created_by , $data->workspace);
                                    }


                                    if ($duplicateBill) {
                                        $billProduct = BillProduct::where('bill_id', $data->invoice_id)->get();
                                        foreach ($billProduct as $product) {
                                            $duplicateProduct = new BillProduct();
                                            $duplicateProduct->bill_id = $duplicateBill->id;
                                            $duplicateProduct->product_type = $product->product_type;
                                            $duplicateProduct->product_id = $product->product_id;
                                            $duplicateProduct->quantity = $product->quantity;
                                            $duplicateProduct->tax = $product->tax;
                                            $duplicateProduct->discount = $product->discount;
                                            $duplicateProduct->price = $product->price;
                                            $duplicateProduct->save();
                                            Invoice::total_quantity('plus', $duplicateProduct->quantity, $duplicateProduct->product_id ,$data->created_by);

                                            //Product Stock Report
                                            $type = 'bill';
                                            $type_id = $bill->id;
                                            $description = $duplicateProduct->quantity . '  ' . __(' quantity purchase in bill') . ' ' . Bill::billNumberFormat($bill->bill_id ,$data->created_by , $data->workspace);
                                            $stocks                = new StockReport();
                                            $stocks->product_id    = $duplicateProduct->product_id;
                                            $stocks->quantity	   = $duplicateProduct->quantity;
                                            $stocks->type          = $type;
                                            $stocks->type_id       = $type_id;
                                            $stocks->description   = $description;
                                            $stocks->workspace     = $data->workspace;
                                            $stocks->created_by    = $data->created_by;
                                            $stocks->save();

                                        }
                                        $count =$data->count;
                                        $day   =$data->day_type;
                                        $date = new DateTime($data->modify_date);
                                        $due_date = new DateTime($data->modify_due_date);
                                        switch ($day) {
                                            case 'day':
                                                $date->add(new DateInterval('P'.$count.'D'));
                                                $modify_date = $date->format('Y-m-d');

                                                $due_date->add(new DateInterval('P'.$count.'D'));
                                                $modify_due_date = $due_date->format('Y-m-d');
                                                break;
                                            case 'month':
                                                $date->add(new DateInterval('P'.$count.'M'));
                                                $modify_date = $date->format('Y-m-d');

                                                $due_date->add(new DateInterval('P'.$count.'M'));
                                                $modify_due_date = $due_date->format('Y-m-d');
                                                break;
                                            case 'year':
                                                $date->add(new DateInterval('P'.$count.'Y'));
                                                $modify_date = $date->format('Y-m-d');

                                                $due_date->add(new DateInterval('P'.$count.'Y'));
                                                $modify_due_date = $due_date->format('Y-m-d');
                                                break;
                                            case 'week':
                                                $date->add(new DateInterval('P'.$count.'W'));
                                                $modify_date = $date->format('Y-m-d');

                                                $due_date->add(new DateInterval('P'.$count.'W'));
                                                $modify_due_date = $due_date->format('Y-m-d');
                                                break;
                                        }

                                    // Update dublicate_invoice field
                                    $duplicate_invoice_ids = !empty($data->dublicate_invoice) ? explode(',', $data->dublicate_invoice) : [];
                                    $duplicate_invoice_ids[] = $duplicateBill->id; // Assuming $duplicateBill is available
                                    $data->dublicate_invoice = implode(',', $duplicate_invoice_ids);

                                    // Update RecurringInvoiceBill record
                                    $updatecycle = RecurringInvoiceBill::where('id', $data->id)
                                        ->where('recurring_type', 'bill')
                                        ->where('created_by', $data->created_by)
                                        ->where('workspace', $data->workspace)
                                        ->first();
                                    $updatecycle->pending_cycle = $data->pending_cycle - 1;
                                    $updatecycle->modify_date = $modify_date;
                                    $updatecycle->modify_due_date = $modify_due_date;
                                    $updatecycle->dublicate_invoice = $data->dublicate_invoice;
                                    $updatecycle->save();

                                }
                            }catch (Exception $e) {
                            }
                        }
                    }

                }

            }
        }



    }


    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
