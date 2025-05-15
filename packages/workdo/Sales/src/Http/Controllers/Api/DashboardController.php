<?php

namespace Workdo\Sales\Http\Controllers\Api;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Workdo\Sales\Entities\Contact;
use Workdo\Sales\Entities\Opportunities;
use Workdo\Sales\Entities\Quote;
use Workdo\Sales\Entities\SalesAccount;
use Workdo\Sales\Entities\SalesInvoice;
use Workdo\Sales\Entities\SalesOrder;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(), [
                    'workspace_id'  => 'required|exists:work_spaces,id',
                ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return response()->json(['status' => 0, 'message' => $messages->first()] , 403);
            }

            $data['totalSalesorder']    = $totalSalesOrder = SalesOrder::where('created_by', creatorId())->where('workspace', '=', $request->workspace_id)->count();
            $data['totalOpportunities'] = Opportunities::where('created_by', creatorId())->where('workspace', '=', $request->workspace_id)->count();
            $data['totalInvoice']       = $totalInvoice = SalesInvoice::where('created_by', creatorId())->where('workspace', '=', $request->workspace_id)->count();
            $data['totalQuote']         = $totalQuote = Quote::where('created_by', creatorId())->where('workspace', '=', $request->workspace_id)->count();
            $statuss  = SalesInvoice::$status;

            foreach($statuss as $id => $status)
            {
                $invoice                   = $total = SalesInvoice::where('status', $id)->where('created_by', creatorId())->where('workspace', '=', $request->workspace_id)->count();
                $percentage                = ($totalInvoice != 0) ? ($total * 100) / $totalInvoice : '0';
                $invoicedata['invoice_'.$status.'_percentage'] = number_format($percentage, 2);
                $invoicedata['invoice_'.$status.'_count']       = $invoice;
                $invoices                = $invoicedata;
            }

            $data['invoice'] = $invoices;

            $statuss = Quote::$status;

            foreach($statuss as $id => $status)
            {
                $quote = $total = Quote::where('status', $id)->where('created_by',creatorId())->where('workspace', '=', $request->workspace_id)->count();

                $percentage              = ($totalQuote != 0) ? ($total * 100) / $totalQuote : '0';
                $quotedata['quote_'.$status.'_percentage'] = number_format($percentage, 2);
                $quotedata['quote_'.$status.'_count']       = $quote;
                $quotes                = $quotedata;
            }
            $data['quote'] = $quotes;


            $statuss     = SalesOrder::$status;
            foreach($statuss as $id => $status)
            {
                $salesorder                   = $total = SalesOrder::where('status', $id)->where('created_by',creatorId())->where('workspace', '=', $request->workspace_id)->count();
                $percentage                   = ($totalSalesOrder != 0) ? ($total * 100) / $totalSalesOrder : '0';
                $salesorderdata['salesorder_'.$status.'_percentage'] = number_format($percentage, 2);
                $salesorderdata['salesorder_'.$status.'_count']       = $salesorder;
                $salesOrders                  = $salesorderdata;
            }
            $data['salesOrder'] = $salesOrders;
            $data['lineChartData'] = $this->getIncExpLineChartDate($request->workspace_id);

            return response()->json(['status'=>1,'data'=>$data]);
        }
        catch(\Exception $e){
            return response()->json(['status'=>0,'message'=>'something went wrong!!!']);
        }
    }

    public function getIncExpLineChartDate($workspace_id)
    {
        $usr           = Auth::user();
        $m             = date("m");
        $de            = date("d");
        $y             = date("Y");
        $format        = 'Y-m-d';
        $arrDate       = [];
        $arrDateFormat = [];

        for($i = 0; $i <= 15 - 1; $i++)
        {
            $date = date($format, mktime(0, 0, 0, $m, ($de - $i), $y));

            $arrDay[]        = date('D', mktime(0, 0, 0, $m, ($de - $i), $y));
            $arrDate[]       = $date;
            $arrDateFormat[] = date("d", strtotime($date)) .'-'.__(date("M", strtotime($date)));
        }

        $result = [];
        for($i = 0; $i < count($arrDate); $i++)
        {
            $daysQuotes = Quote:: select('*')->where('created_by', creatorId())->where('workspace', '=', $workspace_id)->whereRAW('date_quoted = ?', $arrDate[$i])->get();
            $quoteArray = array();
            foreach($daysQuotes as $daysQuote)
            {
                $quoteArray[] = $daysQuote->getTotal();
            }
            $quoteamount = number_format(!empty($quoteArray) ? array_sum($quoteArray) : 0, 2);
            $quateData[] = str_replace(',', '', $quoteamount);


            $daysInvoices = SalesInvoice:: select('*')->where('created_by', creatorId())->where('workspace', '=', $workspace_id)->whereRAW('date_quoted = ?', $arrDate[$i])->get();

            $invoiceArray = array();
            foreach($daysInvoices as $daysInvoice)
            {
                $invoiceArray[] = $daysInvoice->getTotal();
            }
            $invoiceamount = number_format(!empty($invoiceArray) ? array_sum($invoiceArray) : 0, 2);
            $invoiceData[] = str_replace(',', '', $invoiceamount);

            $daysSalesOrders = SalesOrder:: select('*')->where('created_by', creatorId())->where('workspace', '=', $workspace_id)->whereRAW('date_quoted = ?', $arrDate[$i])->get();

            $salesOrderArray = array();
            foreach($daysSalesOrders as $daysSalesOrder)
            {
                $salesOrderArray[] = $daysSalesOrder->getTotal();
            }
            $salesorderamount = number_format(!empty($salesOrderArray) ? array_sum($salesOrderArray) : 0, 2);
            $salesOrderData[] = str_replace(',', '', $salesorderamount);

            $result[] = [
                'day' => $arrDate[$i],
                'invoiceAmount' => $invoiceData[$i],
                'quoteAmount' => $quateData[$i],
                'salesorderAmount' => $salesOrderData[$i],
            ];
        }

        return $result;
    }
}
