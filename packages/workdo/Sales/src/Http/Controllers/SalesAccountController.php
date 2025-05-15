<?php

namespace Workdo\Sales\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\User;
use Workdo\Sales\Entities\SalesAccount;
use Workdo\Sales\Entities\UserDefualtView;
use Workdo\Sales\Entities\SalesUtility;
use Workdo\Sales\Entities\SalesAccountType;
use Workdo\Sales\Entities\AccountIndustry;
use Workdo\Sales\Entities\Opportunities;
use Workdo\Sales\Entities\SalesDocument;
use Workdo\Sales\Entities\Contact;
use Workdo\Sales\Entities\Stream;
use Workdo\Sales\Entities\CommonCase;
use Illuminate\Support\Facades\Auth;
use Workdo\Sales\DataTables\SalesAccountDataTable;
use Workdo\Sales\Entities\Call;
use Workdo\Sales\Entities\Meeting;
use Workdo\Sales\Entities\Quote;
use Workdo\Sales\Entities\SalesInvoice;
use Workdo\Sales\Entities\SalesOrder;
use Workdo\Sales\Events\CreateSalesAccount;
use Workdo\Sales\Events\DestroySalesAccount;
use Workdo\Sales\Events\UpdateSalesAccount;

class SalesAccountController extends Controller
{
    public function index(SalesAccountDataTable $dataTable)
    {
        if(\Auth::user()->isAbleTo('salesaccount manage'))
        {
            $defualtView         = new UserDefualtView();
            $defualtView->route  = \Request::route()->getName();
            $defualtView->module = 'account';
            $defualtView->view   = 'list';

            SalesUtility::userDefualtView($defualtView);
            return $dataTable->render('sales::salesaccount.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if(\Auth::user()->isAbleTo('salesaccount create'))
        {
            $accountype  = SalesAccountType::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $industry    = AccountIndustry::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $document_id = SalesDocument::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $document_id->prepend('--', 0);

            $user        = User::where('workspace_id',getActiveWorkSpace())->emp()->get()->pluck('name', 'id');
            $user->prepend('--', 0);
            if(module_is_active('CustomField')){
                $customFields =  \Workdo\CustomField\Entities\CustomField::where('workspace_id',getActiveWorkSpace())->where('module', '=', 'sales')->where('sub_module','account')->get();
            }else{
                $customFields = null;
            }
            return view('sales::salesaccount.create', compact('accountype', 'industry', 'user', 'document_id','customFields'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {

        if(\Auth::user()->isAbleTo('salesaccount create'))
        {
            $validator = \Validator::make(
                $request->all(),[
                                   'name'                   =>  'required|string|max:120',
                                   'email'                  =>  'required|email|unique:sales_accounts',
                                   'phone'                  =>  'required|regex:/^\+\d{1,3}\d{9,13}$/',
                                   'website'                =>  'required',
                                   'billing_address'        =>  'required',
                                   'shipping_address'       =>  'required',
                                   'billing_city'           =>  'required',
                                   'billing_state'          =>  'required',
                                   'shipping_city'          =>  'required',
                                   'shipping_state'         =>  'required',
                                   'billing_country'        =>  'required',
                                   'shipping_country'       =>  'required',
                                   'type'                   =>  'required',
                                   'industry'               =>  'required',
                                   'shipping_postalcode'    =>  'required',
                                   'billing_postalcode'     =>  'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $salesaccount                        = new SalesAccount();
            $salesaccount['user_id']             = $request->user;
            $salesaccount['document_id']         = $request->document_id;
            $salesaccount['name']                = $request->name;
            $salesaccount['email']               = $request->email;
            $salesaccount['phone']               = $request->phone;
            $salesaccount['website']             = $request->website;
            $salesaccount['billing_address']     = $request->billing_address;
            $salesaccount['billing_city']        = $request->billing_city;
            $salesaccount['billing_state']       = $request->billing_state;
            $salesaccount['billing_country']     = $request->billing_country;
            $salesaccount['billing_postalcode']  = $request->billing_postalcode;
            $salesaccount['shipping_address']    = $request->shipping_address;
            $salesaccount['shipping_city']       = $request->shipping_city;
            $salesaccount['shipping_state']      = $request->shipping_state;
            $salesaccount['shipping_country']    = $request->shipping_country;
            $salesaccount['shipping_postalcode'] = $request->shipping_postalcode;
            $salesaccount['type']                = $request->type;
            $salesaccount['industry']            = $request->industry;
            $salesaccount['description']         = $request->description;
            $salesaccount['workspace']           = getActiveWorkSpace();
            $salesaccount['created_by']          = creatorId();
            $salesaccount->save();
            Stream::create(
                [
                    'user_id' => Auth::user()->id,'created_by' => creatorId(),
                    'log_type' => 'created',
                    'remark' => json_encode(
                        [
                            'owner_name' => Auth::user()->username,
                            'title' => 'account',
                            'stream_comment' => '',
                            'user_name' => $salesaccount->name,
                        ]
                    ),
                ]
            );
            if(module_is_active('CustomField'))
            {
                \Workdo\CustomField\Entities\CustomField::saveData($salesaccount, $request->customField);
            }
            event(new CreateSalesAccount($request,$salesaccount));

            return redirect()->back()->with('success', __('The sales account has been created successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show(SalesAccount $salesaccount)
    {
        if(\Auth::user()->isAbleTo('salesaccount show'))
        {
            if(module_is_active('CustomField')){
                $salesaccount->customField = \Workdo\CustomField\Entities\CustomField::getData($salesaccount, 'Sales','Account');
                $customFields      = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'Sales')->where('sub_module','Account')->get();
            }else{
                $customFields = null;
            }
            return view('sales::salesaccount.view', compact('salesaccount','customFields'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(SalesAccount $salesaccount)
    {

        if(\Auth::user()->isAbleTo('salesaccount edit'))
        {
            $accountype     = SalesAccountType::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $industry       = AccountIndustry::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $user           = User::where('workspace_id',getActiveWorkSpace())->emp()->get()->pluck('name', 'id');
            $user->prepend('--', 0);
            $contacts       = Contact::where('account', $salesaccount->id)->where('workspace',getActiveWorkSpace())->get();
            $opportunitiess = Opportunities::with('stages','assign_user')->where('account', $salesaccount->id)->where('workspace',getActiveWorkSpace())->get();
            $cases          = CommonCase::where('account', $salesaccount->id)->where('workspace',getActiveWorkSpace())->get();
            $documents      = SalesDocument::where('account', $salesaccount->id)->where('workspace',getActiveWorkSpace())->get();
            $salesorders    = SalesOrder::where('account', $salesaccount->id)->where('workspace',getActiveWorkSpace())->get();
            $salesinvoices  = SalesInvoice::with('assign_user')->where('account', $salesaccount->id)->where('workspace',getActiveWorkSpace())->get();
            $calls          = Call::with('assign_user')->where('account', $salesaccount->id)->where('workspace',getActiveWorkSpace())->get();
            $meetings       = Meeting::where('account', $salesaccount->id)->where('workspace',getActiveWorkSpace())->get();
            $quotes         = Quote::where('account', $salesaccount->id)->where('workspace',getActiveWorkSpace())->get();
            $document_id    = SalesDocument::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $document_id->prepend('--', 0);

            // get previous user id
            $previous = SalesAccount::where('id', '<', $salesaccount->id)->max('id');
            // get next user id
            $next = SalesAccount::where('id', '>', $salesaccount->id)->min('id');

            $parent   = 'account';
            $log_type = 'account comment';
            $streams  = Stream::where('log_type', $log_type)->get();

            if(module_is_active('CustomField')){
                $salesaccount->customField = \Workdo\CustomField\Entities\CustomField::getData($salesaccount, 'sales','Account');
                $customFields             = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'sales')->where('sub_module','Account')->get();
            }else{
                $customFields = null;
            }

            return view('sales::salesaccount.edit', compact('meetings','salesorders','calls','salesinvoices','quotes','salesaccount', 'accountype', 'industry', 'user','previous', 'next', 'contacts', 'opportunitiess','cases','documents', 'streams', 'document_id','customFields'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request,SalesAccount $salesaccount)
    {

        if(\Auth::user()->isAbleTo('salesaccount edit'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'name'                   =>  'required|String|max:120',
                                   'email'                  =>  'required|email|unique:users',
                                   'phone'                  =>  'required|regex:/^\+\d{1,3}\d{9,13}$/',
                                   'website'                =>  'required',
                                   'billing_address'        =>  'required',
                                   'shipping_address'       =>  'required',
                                   'billing_city'           =>  'required',
                                   'billing_state'          =>  'required',
                                   'shipping_city'          =>  'required',
                                   'shipping_state'         =>  'required',
                                   'billing_country'        =>  'required',
                                   'shipping_country'       =>  'required',
                                   'type'                   =>  'required',
                                   'industry'               =>  'required',
                                   'shipping_postalcode'    =>  'required',
                                   'billing_postalcode'     =>  'required',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $salesaccount['user_id']             = $request->user;
            $salesaccount['document_id']         = $request->document_id;
            $salesaccount['name']                = $request->name;
            $salesaccount['email']               = $request->email;
            $salesaccount['phone']               = $request->phone;
            $salesaccount['website']             = $request->website;
            $salesaccount['billing_address']     = $request->billing_address;
            $salesaccount['billing_city']        = $request->billing_city;
            $salesaccount['billing_state']       = $request->billing_state;
            $salesaccount['billing_country']     = $request->billing_country;
            $salesaccount['billing_postalcode']  = $request->billing_postalcode;
            $salesaccount['shipping_address']    = $request->shipping_address;
            $salesaccount['shipping_city']       = $request->shipping_city;
            $salesaccount['shipping_state']      = $request->shipping_state;
            $salesaccount['shipping_country']    = $request->shipping_country;
            $salesaccount['shipping_postalcode'] = $request->shipping_postalcode;
            $salesaccount['type']                = $request->type;
            $salesaccount['industry']            = $request->industry;
            $salesaccount['description']         = $request->description;
            $salesaccount['workspace']           = getActiveWorkSpace();
            $salesaccount['created_by']          = creatorId();
            $salesaccount->update();

            Stream::create(
                [
                    'user_id' => Auth::user()->id,
                    'created_by' => creatorId(),
                    'log_type' => 'updated',
                    'remark' => json_encode(
                        [
                            'owner_name' => Auth::user()->username,
                            'title' => 'account',
                            'stream_comment' => '',
                            'user_name' => $salesaccount->name,
                        ]
                    ),
                ]
            );

            if(module_is_active('CustomField'))
            {
                \Workdo\CustomField\Entities\CustomField::saveData($salesaccount, $request->customField);
            }
            event(new UpdateSalesAccount($request,$salesaccount));


            return redirect()->back()->with('success', __('The sales account details are updated successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(SalesAccount $salesaccount)
    {
        if(\Auth::user()->isAbleTo('salesaccount delete'))
        {
            if(module_is_active('CustomField'))
            {
                $customFields = \Workdo\CustomField\Entities\CustomField::where('module','sales')->where('sub_module','Account')->get();
                foreach($customFields as $customField)
                {
                    $value = \Workdo\CustomField\Entities\CustomFieldValue::where('record_id', '=', $salesaccount->id)->where('field_id',$customField->id)->first();
                    if(!empty($value)){
                        $value->delete();
                    }
                }
            }
            event(new DestroySalesAccount($salesaccount));

            $salesaccount->delete();
            return redirect()->back()->with('success', __('The sales account has been deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function grid()
    {
        if(\Auth::user()->isAbleTo('salesaccount manage'))
        {
            $accounts = SalesAccount::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->paginate(11);

            $defualtView         = new UserDefualtView();
            $defualtView->route  = \Request::route()->getName();
            $defualtView->module = 'account';
            $defualtView->view   = 'grid';

            SalesUtility::userDefualtView($defualtView);
            return view('sales::salesaccount.grid', compact('accounts'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function fileImportExport()
    {
        if(Auth::user()->isAbleTo('salesaccount import'))
        {
            return view('sales::salesaccount.import');
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function fileImport(Request $request)
    {
        if(Auth::user()->isAbleTo('salesaccount import'))
        {
            session_start();

            $error = '';

            $html = '';

            if ($request->file->getClientOriginalName() != '') {
                $file_array = explode(".", $request->file->getClientOriginalName());

                $extension = end($file_array);
                if ($extension == 'csv') {
                    $file_data = fopen($request->file->getRealPath(), 'r');

                    $file_header = fgetcsv($file_data);
                    $html .= '<table class="table table-bordered"><tr>';

                    for ($count = 0; $count < count($file_header); $count++) {
                        $html .= '
                                <th>
                                    <select name="set_column_data" class="form-control set_column_data" data-column_number="' . $count . '">
                                    <option value="">Set Count Data</option>
                                    <option value="name">Name</option>
                                    <option value="email">Email</option>
                                    <option value="phone">Phone No</option>
                                    <option value="website">Website</option>
                                    <option value="billing_address">Billing Address</option>
                                    <option value="billing_city">Billing City</option>
                                    <option value="billing_state">Billing State</option>
                                    <option value="billing_country">Billing Country</option>
                                    <option value="billing_postalcode">Billing Postal Code</option>
                                    <option value="shipping_address">Shipping Address</option>
                                    <option value="shipping_city">Shipping City</option>
                                    <option value="shipping_state">Shipping State</option>
                                    <option value="shipping_country">Shipping Country</option>
                                    <option value="shipping_postalcode">Shipping Postal Code</option>
                                    <option value="description">Description</option>
                                    </select>
                                </th>
                                ';
                    }

                    $html .= '
                                <th>
                                        <select name="set_column_data" class="form-control set_column_data type" data-column_number="' . $count+1 . '">
                                            <option value="type">Type</option>
                                        </select>
                                </th>
                                ';

                    $html .= '</tr>';
                    $limit = 0;
                    while (($row = fgetcsv($file_data)) !== false) {
                        $limit++;

                        $html .= '<tr>';

                        for ($count = 0; $count < count($row); $count++) {
                            $html .= '<td>' . $row[$count] . '</td>';
                        }

                        $html .= '<td>
                                    <select name="type" class="form-control type-value">;';
                                    $account_types = SalesAccountType::where('created_by',creatorId())->where('workspace',getActiveWorkSpace())->pluck('name','id');
                                        foreach ($account_types as $key => $account_type)
                                        {
                                            $html .=' <option value="'.$key.'">'.$account_type.'</option>';
                                        }
                                    $html .='  </select>
                                </td>';

                        $html .= '</tr>';

                        $temp_data[] = $row;

                    }
                    $_SESSION['file_data'] = $temp_data;
                } else {
                    $error = 'Only <b>.csv</b> file allowed';
                }
            } else {

                $error = __('Please select CSV file.');
            }
            $output = array(
                'error' => $error,
                'output' => $html,
            );

            return json_encode($output);
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function fileImportModal()
    {
        if(Auth::user()->isAbleTo('salesaccount import'))
        {
            return view('sales::salesaccount.import_modal');
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function salesaccountImportdata(Request $request)
    {
        if(Auth::user()->isAbleTo('salesaccount import'))
        {
            session_start();
            $html = '<h3 class="text-danger text-center">Below data is not inserted</h3></br>';
            $flag = 0;
            $html .= '<table class="table table-bordered"><tr>';
            $file_data = $_SESSION['file_data'];

            unset($_SESSION['file_data']);

            $user = Auth::user();

            foreach ($file_data as $key=>$row) {
                    $salesaccount = SalesAccount::where('created_by',creatorId())->where('workspace',getActiveWorkSpace())->Where('email', 'like',$row[$request->email])->get();

                    if($salesaccount->isEmpty()){
                    try {
                            $type = SalesAccountType::find($request->type[$key]);
                        SalesAccount::create([
                            'name' => $row[$request->name],
                            'email' => $row[$request->email],
                            'phone' => $row[$request->phone],
                            'website' => $row[$request->website],
                            'billing_address' => $row[$request->billing_address],
                            'billing_city' => $row[$request->billing_city],
                            'billing_state' => $row[$request->billing_state],
                            'billing_country' => $row[$request->billing_country],
                            'billing_postalcode' => $row[$request->billing_postalcode],
                            'shipping_address' => $row[$request->shipping_address],
                            'shipping_city' => $row[$request->shipping_city],
                            'shipping_state' => $row[$request->shipping_state],
                            'shipping_country' => $row[$request->shipping_country],
                            'shipping_postalcode' => $row[$request->shipping_postalcode],
                            'description' => $row[$request->description],
                            'type' => $type->id,
                            'created_by' => creatorId(),
                            'workspace' => getActiveWorkSpace(),
                        ]);
                    }
                    catch (\Exception $e)
                    {
                        $flag = 1;
                        $html .= '<tr>';

                        $html .= '<td>' . $row[$request->name] . '</td>';
                        $html .= '<td>' . $row[$request->email] . '</td>';
                        $html .= '<td>' . $row[$request->phone] . '</td>';
                        $html .= '<td>' . $row[$request->website] . '</td>';
                        $html .= '<td>' . $row[$request->billing_address] . '</td>';
                        $html .= '<td>' . $row[$request->billing_city] . '</td>';
                        $html .= '<td>' . $row[$request->billing_state] . '</td>';
                        $html .= '<td>' . $row[$request->billing_country] . '</td>';
                        $html .= '<td>' . $row[$request->billing_postalcode] . '</td>';
                        $html .= '<td>' . $row[$request->shipping_address] . '</td>';
                        $html .= '<td>' . $row[$request->shipping_city] . '</td>';
                        $html .= '<td>' . $row[$request->shipping_state] . '</td>';
                        $html .= '<td>' . $row[$request->shipping_country] . '</td>';
                        $html .= '<td>' . $row[$request->shipping_postalcode] . '</td>';
                        $html .= '<td>' . $row[$request->description] . '</td>';

                        $html .= '</tr>';
                    }
                }
                else
                {
                    $flag = 1;
                    $html .= '<tr>';

                    $html .= '<td>' . $row[$request->name] . '</td>';
                    $html .= '<td>' . $row[$request->email] . '</td>';
                    $html .= '<td>' . $row[$request->phone] . '</td>';
                    $html .= '<td>' . $row[$request->website] . '</td>';
                    $html .= '<td>' . $row[$request->billing_address] . '</td>';
                    $html .= '<td>' . $row[$request->billing_city] . '</td>';
                    $html .= '<td>' . $row[$request->billing_state] . '</td>';
                    $html .= '<td>' . $row[$request->billing_country] . '</td>';
                    $html .= '<td>' . $row[$request->billing_postalcode] . '</td>';
                    $html .= '<td>' . $row[$request->shipping_address] . '</td>';
                    $html .= '<td>' . $row[$request->shipping_city] . '</td>';
                    $html .= '<td>' . $row[$request->shipping_state] . '</td>';
                    $html .= '<td>' . $row[$request->shipping_country] . '</td>';
                    $html .= '<td>' . $row[$request->shipping_postalcode] . '</td>';
                    $html .= '<td>' . $row[$request->description] . '</td>';

                    $html .= '</tr>';
                }
            }

            $html .= '
                            </table>
                            <br />
                            ';
            if ($flag == 1)
            {

                return response()->json([
                            'html' => true,
                    'response' => $html,
                ]);
            } else {
                return response()->json([
                    'html' => false,
                    'response' => __('The data has been imported successfully.'),
                ]);
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
