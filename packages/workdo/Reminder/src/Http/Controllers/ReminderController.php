<?php

namespace Workdo\Reminder\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Invoice;
use App\Models\User;
use Workdo\Account\Entities\Bill;
use Workdo\Lead\Entities\Lead;
use Workdo\Lead\Entities\Deal;
use Workdo\Account\Entities\Customer;
use Workdo\LMS\Entities\Store;
use Workdo\LMS\Entities\Student;
use Workdo\ChildcareManagement\Entities\Child;
use Workdo\MobileServiceManagement\Entities\MobileServiceRequest;
use Workdo\VehicleInspectionManagement\Entities\InspectionRequest;
use Workdo\MachineRepairManagement\Entities\MachineRepairRequest;
use Workdo\Account\Entities\Vender;
use Workdo\Lead\Entities\ClientDeal;
use Workdo\Reminder\Entities\Reminder;
use Workdo\Reminder\Events\CreateReminder;
use Workdo\Reminder\Events\UpdateReminder;
use Workdo\Reminder\Events\DestroyReminder;
use Illuminate\Support\Facades\Auth;
use DateTime;
use DateInterval;
use Workdo\Reminder\DataTables\ReminderDataTable;


class ReminderController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(ReminderDataTable  $dataTable)
    {
        if(Auth::user()->isAbleTo('reminder manage'))
        {
            return $dataTable->render('reminder::reminder.index');
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if(Auth::user()->isAbleTo('reminder create'))
        {
           return view('reminder::reminder.create');
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if(Auth::user()->isAbleTo('reminder create'))
        {
            $requestData = $request->all();
            $jsonData = [
                'email_address' => $requestData['email_address'] ?? '',
                'sms_mobile_no' => $requestData['sms_mobile_no'] ?? '',
                'slack_url' => $requestData['slack_url'] ?? '',
                'twillo_mobile_no' => $requestData['twillo_mobile_no'] ?? '',
                'telegram_access' => $requestData['telegram_access'] ?? '',
                'telegram_chat' => $requestData['telegram_chat'] ?? '',
                'whatsapp_mobile_no' => $requestData['whatsapp_mobile_no'] ?? '',
                'whatsappapi_mobile_no' => $requestData['whatsappapi_mobile_no'] ?? '',
                'deal_client_id' => $requestData['deal_client_id'] ?? '',
            ];
            $jsonData = json_encode($jsonData);
            if($request->date_select  == 'default'){
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'day_number' => 'required|integer|min:1',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }
                if($request->module == 'Invoice' ){
                    $invoice  =  Invoice::where('id',$request->module_value)->first();
                    $dateString = $invoice->due_date;
                    $date = new DateTime($dateString);
                    $date->sub(new DateInterval('P'.$request->day_number.'D'));
                    $date = $date->format('Y-m-d');
                }elseif($request->module == 'Bill'){
                    $bill  =  Bill::where('id',$request->module_value)->first();
                    $dateString = $bill->due_date;
                    $date = new DateTime($dateString);
                    $date->sub(new DateInterval('P'.$request->day_number.'D'));
                    $date = $date->format('Y-m-d');

                }else{
                    $lead  =  Lead::where('id',$request->module_value)->first();
                    $dateString = $lead->follow_up_date;
                    $date = new DateTime($dateString);
                    $date->sub(new DateInterval('P'.$request->day_number.'D'));
                    $date = $date->format('Y-m-d');
                }

            }else{
                $date = $request->date;
            }

            $reminder = new Reminder();
            $reminder->date_select            = $request->input('date_select');
            $reminder->day                    = $request->day_number ?? 0;
            $reminder->date                   = $date;
            $reminder->module                 = $request->module;
            $reminder->module_value           = $request->module_value ;
            $reminder->to                     = $jsonData;
            $reminder->action                 = implode(',' ,$request->actions) ;
            $reminder->message                = $request->course_description ;
            $reminder->workspace              = getActiveWorkSpace() ;
            $reminder->created_by             = creatorId() ;
            $reminder->save();

            event(new CreateReminder($request,$reminder));

            return redirect()->route('reminder.index')->with('success', __('The reminder has been created successfully'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }

    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('reminder::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if(Auth::user()->isAbleTo('reminder edit'))
        {
            $reminder = Reminder::find($id);
            $reminder_to = json_decode($reminder->to);
            $client_id = !empty($reminder_to->deal_client_id) ? $reminder_to->deal_client_id : 0;
            $client = User::where('id' , $client_id)->first();
            return view('reminder::reminder.edit' ,compact('reminder','client'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        if(Auth::user()->isAbleTo('reminder edit'))
        {

            $requestData = $request->all();
            $jsonData = [
                'email_address' => $requestData['email_address'] ?? '',
                'sms_mobile_no' => $requestData['sms_mobile_no'] ?? '',
                'slack_url' => $requestData['slack_url'] ?? '',
                'twillo_mobile_no' => $requestData['twillo_mobile_no'] ?? '',
                'telegram_access' => $requestData['telegram_access'] ?? '',
                'telegram_chat' => $requestData['telegram_chat'] ?? '',
                'whatsapp_mobile_no' => $requestData['whatsapp_mobile_no'] ?? '',
                'whatsappapi_mobile_no' => $requestData['whatsappapi_mobile_no'] ?? '',
                'deal_client_id' => $requestData['deal_client_id'] ?? '',
            ];
            $jsonData = json_encode($jsonData);
            if($request->date_select  == 'default'){
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'day_number' => 'required|integer|min:1',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->back()->with('error', $messages->first());
                }
                if($request->module == 'Invoice' ){
                    $invoice  =  Invoice::where('id',$request->module_value)->first();
                    $dateString = $invoice->due_date;
                    $date = new DateTime($dateString);
                    $date->sub(new DateInterval('P'.$request->day_number.'D'));
                    $date = $date->format('Y-m-d');
                }elseif($request->module == 'Bill'){
                    $bill  =  Bill::where('id',$request->module_value)->first();
                    $dateString = $bill->due_date;
                    $date = new DateTime($dateString);
                    $date->sub(new DateInterval('P'.$request->day_number.'D'));
                    $date = $date->format('Y-m-d');

                }else{
                    $lead  =  Lead::where('id',$request->module_value)->first();
                    $dateString = $lead->follow_up_date;
                    $date = new DateTime($dateString);
                    $date->sub(new DateInterval('P'.$request->day_number.'D'));
                    $date = $date->format('Y-m-d');
                }

            }else{
                $date = $request->date;
            }
            $reminder                         = Reminder::find($id);
            $reminder->date_select            = $request->input('date_select');
            $reminder->day                    = $request->day_number ?? 0;
            $reminder->date                   = $date;
            $reminder->module                 = $request->module;
            $reminder->module_value           = $request->module_value ;
            $reminder->to                     = $jsonData;
            $reminder->action                 = implode(',' ,$request->actions) ;
            $reminder->message                = $request->course_description ;
            $reminder->workspace              = getActiveWorkSpace() ;
            $reminder->created_by             = creatorId() ;
            $reminder->save();
            event(new UpdateReminder($request,$reminder));

            return redirect()->route('reminder.index')->with('success', __('The reminder details are updated successfully'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }

    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        if(Auth::user()->isAbleTo('reminder delete'))
        {
            $reminder = Reminder::find($id);
            event(new DestroyReminder($reminder));
            $reminder->delete();

            return redirect()->back()->with('success', __('The reminder has been deleted'));
        }
        else
        {
            return redirect()->back()->with('error', 'permission Denied');
        }

    }
    public function module_data(Request $request){
        if($request->module != '0'){
            if($request->module == "Invoice"){
                $data = Invoice::where('created_by', creatorId())->where('workspace' ,getActiveWorkSpace())->whereIn('account_type' ,ActivatedModule())->get()->pluck('invoice_id','id')->map(function ($invoiceId) {
                    return \App\Models\Invoice::invoiceNumberFormat($invoiceId);
                });
            }elseif($request->module == "User"){
                $data = User::where('created_by', creatorId())->where('workspace_id' ,getActiveWorkSpace())->get()->pluck('name','id');

            }elseif($request->module == "Bill"){
                $data = Bill::where('created_by', creatorId())->where('workspace' ,getActiveWorkSpace())->get()->pluck('bill_id','id')->map(function ($billId) {
                    return Bill::billNumberFormat($billId);
                });

            }elseif($request->module == "Lead"){
                $data = Lead::where('created_by', creatorId())->where('workspace_id' ,getActiveWorkSpace())->get()->pluck('name','id');

            }else{
                $data = Deal::where('created_by', creatorId())->where('workspace_id' ,getActiveWorkSpace())->get()->pluck('name','id');
            }
            return $data;
        }else{
            $data = 0;
            return $data;

        }


    }

    public function reminder_attribute(Request $request) {
            if(!empty($request->module) && !empty($request->module_data) && !empty($request->action) ){
                $notification = $request->action;
                if ($request->module == 'Invoice') {
                    $data = Invoice::where('id', $request->module_data)
                        ->where('created_by', creatorId())
                        ->where('workspace', getActiveWorkSpace())
                        ->first();
                    if ($data) {
                        if ($data->account_type == 'ChildcareManagement') {

                            $child = Child::find($data->user_id);
                            $user = $child->parent;
                            $useremail = $user['email'];
                            $user_contact_number = $user['contact_number'];
                        }elseif ($data->account_type == 'LMS') {
                            $store = Store::where('workspace_id', getActiveWorkSpace())
                                ->where('created_by', creatorId())
                                ->first();
                            if ($store) {
                                $user = Student::where('id', $data->customer_id)
                                    ->where('store_id', $store->id)
                                    ->first();
                            }

                            $useremail = $user['email'] ?? '';
                            $user_contact_number = $user['phone_number'] ?? '';
                        }elseif($data->account_type  == 'MobileServiceManagement'){

                            $user = MobileServiceRequest::find($data->customer_id);
                            $useremail = $user['email'] ?? '';
                            $user_contact_number = $user['mobile_no'] ?? '';
                        } elseif($data->account_type  == 'VehicleInspectionManagement'){

                            $user = InspectionRequest::where('id', $data->customer_id)->where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->first();
                            $useremail = $user['inspector_email'] ?? '';
                            $user_contact_number = [];
                        }elseif($data->account_type  == 'MachineRepairManagement'){
                            $user = MachineRepairRequest::where('id', $data->customer_id)->where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->first();
                            $useremail = $user['customer_email'] ?? '';
                            $user_contact_number = [];

                        }
                            else {

                            if (module_is_active('Account')) {
                                $user = User::where('id', $data->user_id)
                                    ->where('created_by', creatorId())
                                    ->where('workspace_id', getActiveWorkSpace())
                                    ->first();
                                    $useremail = $user['email'] ?? '';
                                    $user_contact_number = $user['mobile_no'] ?? '';
                                if (!empty($data->customer_id)) {
                                    $user = Customer::where('id', $data->customer_id)->first();
                                    $useremail = $user['email'] ?? '';
                                    $user_contact_number = $user['contact'] ?? '';
                                }

                            }
                            else {
                                $user = User::where('id', $data->user_id)
                                    ->where('workspace_id', getActiveWorkSpace())
                                    ->where('created_by', creatorId())
                                    ->first();
                                    $useremail = $user['email'] ?? '';
                                    $user_contact_number = $user['mobile_no'] ?? '';
                            }
                        }
                    }
                }elseif($request->module == 'Bill'){
                    $data = Bill::where('id', $request->module_data)
                        ->where('created_by', creatorId())
                        ->where('workspace', getActiveWorkSpace())
                        ->first();
                    $user = Vender::where('id',$data->vendor_id)->where('created_by', creatorId())->where('workspace', '=', getActiveWorkSpace())->first();
                    $useremail = $user['email'] ?? '';
                    $user_contact_number = $user['contact'] ?? '';
                }elseif($request->module == 'User'){
                    $user = User::where('id',$request->module_data)
                    ->where('created_by', creatorId())
                    ->where('workspace_id', getActiveWorkSpace())
                    ->first();
                    $useremail = $user['email'] ?? '';
                    $user_contact_number = $user['mobile_no'] ?? '';
                }elseif($request->module == 'Lead'){
                    $data = Lead::where('id',$request->module_data)
                    ->where('created_by', creatorId())
                    ->where('workspace_id', getActiveWorkSpace())
                    ->first();
                    $user = User::where('id', $data->user_id)->first();
                    $useremail = $user['email'] ?? '';
                    $user_contact_number = $user['mobile_no'] ?? '';
                }else{
                    $user = User::where('id' , $request->deal_data)->first();
                    $useremail = $user['email'] ?? '';
                    $user_contact_number = $user['mobile_no'] ?? '';

                }
                $returnHTML = view('reminder::reminder.notification',compact('notification','useremail','user_contact_number'))->render();
                $response = [
                    'is_success' => true,
                    'message' => '',
                    'html' => $returnHTML,
                ];
                return $response;
            }else{
                $response = [
                    'is_success' => false,
                    'message' => '',
                ];
                return $response;
            }


    }

    public function reminder_attribute_edit(Request $request ,$id) {
            if(!empty($request->module) && !empty($request->module_data) && !empty($request->action) ){
                $reminder = Reminder::find($id);
                $reminder_data = json_decode($reminder->to);

                $notification = $request->action;
                if ($request->module == 'Invoice') {
                    $data = Invoice::where('id', $request->module_data)
                        ->where('created_by', creatorId())
                        ->where('workspace', getActiveWorkSpace())
                        ->first();
                    if ($data) {
                        if ($data->account_type == 'ChildcareManagement') {

                            $child = Child::find($data->user_id);
                            $user = $child->parent;
                            $useremail = $user['email'];
                            $user_contact_number = $user['contact_number'];
                        }elseif ($data->account_type == 'LMS') {
                            $store = Store::where('workspace_id', getActiveWorkSpace())
                                ->where('created_by', creatorId())
                                ->first();
                            if ($store) {
                                $user = Student::where('id', $data->customer_id)
                                    ->where('store_id', $store->id)
                                    ->first();
                            }

                            $useremail = $user['email'];
                            $user_contact_number = $user['phone_number'];
                        }elseif($data->account_type  == 'MobileServiceManagement'){

                            $user = MobileServiceRequest::find($data->customer_id);
                            $useremail = $user['email'] ??  '';
                            $user_contact_number = $user['mobile_no']  ??  '';
                        } elseif($data->account_type  == 'VehicleInspectionManagement'){

                            $user = InspectionRequest::where('id', $data->customer_id)->where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->first();
                            $useremail = $user['inspector_email'] ?? '';
                            $user_contact_number = [];
                        }elseif($data->account_type  == 'MachineRepairManagement'){
                            $user = MachineRepairRequest::where('id', $data->customer_id)->where('workspace', getActiveWorkSpace())->where('created_by', creatorId())->first();
                            $useremail = $user['customer_email'] ?? '';
                            $user_contact_number = [];

                        }
                            else {

                            if (module_is_active('Account')) {
                                $user = User::where('id', $data->user_id)
                                    ->where('created_by', creatorId())
                                    ->where('workspace_id', getActiveWorkSpace())
                                    ->first();
                                    $useremail = $user['email'] ?? '';
                                    $user_contact_number = $user['mobile_no'] ?? '';
                                if (!empty($data->customer_id)) {
                                    $user = Customer::where('id', $data->customer_id)->first();
                                    $useremail = $user['email'] ?? '';
                                    $user_contact_number = $user['contact'] ?? '';
                                }

                            }
                            else {
                                $user = User::where('id', $data->user_id)
                                    ->where('workspace_id', getActiveWorkSpace())
                                    ->where('created_by', creatorId())
                                    ->first();
                                    $useremail = $user['email'] ?? '';
                                    $user_contact_number = $user['mobile_no'] ?? '';
                            }
                        }
                    }
                }elseif($request->module == 'Bill'){
                    $data = Bill::where('id', $request->module_data)
                        ->where('created_by', creatorId())
                        ->where('workspace', getActiveWorkSpace())
                        ->first();
                    $user = Vender::where('id',$data->vendor_id)->where('created_by', creatorId())->where('workspace', '=', getActiveWorkSpace())->first();
                    $useremail = $user['email'] ?? '';
                    $user_contact_number = $user['contact'] ?? '';
                }elseif($request->module == 'User'){
                    $user = User::where('id',$request->module_data)
                    ->where('created_by', creatorId())
                    ->where('workspace_id', getActiveWorkSpace())
                    ->first();
                    $useremail = $user['email'] ?? '';
                    $user_contact_number = $user['mobile_no'] ?? '';
                }elseif($request->module == 'Lead'){
                    $data = Lead::where('id',$request->module_data)
                    ->where('created_by', creatorId())
                    ->where('workspace_id', getActiveWorkSpace())
                    ->first();
                    $user = User::where('id', $data->user_id)->first();
                    $useremail = $user['email'] ?? '';
                    $user_contact_number = $user['mobile_no'] ?? '';
                }else{
                    $user = User::where('id' , $request->deal_data)->first();
                    $useremail = $user['email'] ?? '';
                    $user_contact_number = $user['mobile_no'] ?? '';

                }
                $returnHTML = view('reminder::reminder.edit_notification',compact('reminder_data','reminder','notification','useremail','user_contact_number'))->render();
                $response = [
                    'is_success' => true,
                    'message' => '',
                    'html' => $returnHTML,
                ];
                return $response;
            }else{
                $response = [
                    'is_success' => false,
                    'message' => '',
                ];
                return $response;
            }
    }

    public function deal_client(Request $request){
        $data = ClientDeal::where('deal_id',$request->module_data)
        ->get();
        $clients = [];
        foreach($data as $client){
            $clients[] = User::where('id' , $client->client_id)->first();
        }
        return $clients;
    }

}
