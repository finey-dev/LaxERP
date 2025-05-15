<?php

namespace Workdo\Sales\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\User;
use Workdo\Sales\Entities\SalesAccount;
use Workdo\Sales\Entities\Opportunities;
use Workdo\Sales\Entities\OpportunitiesStage;
use Workdo\Sales\Entities\Contact;
use Workdo\Sales\Entities\Stream;
use Workdo\Sales\Entities\UserDefualtView;
use Workdo\Sales\Entities\SalesUtility;
use Workdo\Sales\Entities\SalesDocument;
use Illuminate\Support\Facades\Auth;
use Workdo\Sales\DataTables\SalesOpportunitiesDataTable;
use Workdo\Sales\Entities\Quote;
use Workdo\Sales\Entities\SalesInvoice;
use Workdo\Sales\Entities\SalesOrder;
use Workdo\Sales\Events\CreateChangeOrder;
use Workdo\Sales\Events\CreateOpportunities;
use Workdo\Sales\Events\DestroyOpportunities;
use Workdo\Sales\Events\UpdateOpportunities;

class OpportunitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(SalesOpportunitiesDataTable $dataTable)
    {
        if (\Auth::user()->isAbleTo('opportunities manage')) {
            $defualtView         = new UserDefualtView();
            $defualtView->route  = \Request::route()->getName();
            $defualtView->module = 'opportunities';
            $defualtView->view   = 'list';
            SalesUtility::userDefualtView($defualtView);
            return $dataTable->render('sales::opportunities.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create($type, $id)
    {
        if (\Auth::user()->isAbleTo('opportunities create')) {
            $account_name        = SalesAccount::where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $user                = User::where('workspace_id', getActiveWorkSpace())->emp()->get()->pluck('name', 'id');
            $user->prepend('--', 0);
            $opportunities_stage = OpportunitiesStage::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $leadsource = "";
            if (module_is_active('Lead')) {
                $leadsource          = \Workdo\Lead\Entities\Source::where('created_by', creatorId())->where('workspace_id', getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            $contact             = Contact::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $contact->prepend('--', 0);
            if (module_is_active('CustomField')) {
                $customFields =  \Workdo\CustomField\Entities\CustomField::where('workspace_id', getActiveWorkSpace())->where('module', '=', 'sales')->where('sub_module', 'Opportunities')->get();
            } else {
                $customFields = null;
            }

            return view('sales::opportunities.create', compact('user', 'opportunities_stage', 'account_name', 'leadsource', 'contact', 'type', 'id', 'customFields'));
        } else {
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
        if (\Auth::user()->isAbleTo('opportunities create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'name'          => 'required|string|max:120',
                    'account'       => 'required',
                    'stage'         => 'required',
                    'amount'        => 'required|numeric',
                    'probability'   => 'required|numeric',
                    'close_date'    => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $opportunities                = new Opportunities();
            $opportunities['user_id']     = $request->user;
            $opportunities['name']        = $request->name;
            $opportunities['account']     = $request->account;
            $opportunities['stage']       = $request->stage;
            $opportunities['amount']      = $request->amount;
            $opportunities['probability'] = $request->probability;
            $opportunities['close_date']  = $request->close_date;
            $opportunities['contact']     = $request->contact;
            $opportunities['lead_source'] = $request->lead_source;
            $opportunities['description'] = $request->description;
            $opportunities['workspace']         = getActiveWorkSpace();
            $opportunities['created_by']  = creatorId();
            $opportunities->save();

            Stream::create(
                [
                    'user_id' => Auth::user()->id, 'created_by' => creatorId(),
                    'log_type' => 'created',
                    'remark' => json_encode(
                        [
                            'owner_name' => Auth::user()->username,
                            'title' => 'opportunities',
                            'stream_comment' => '',
                            'user_name' => $opportunities->name,
                        ]
                    ),
                ]
            );
            if (module_is_active('CustomField')) {
                \Workdo\CustomField\Entities\CustomField::saveData($opportunities, $request->customField);
            }
            event(new CreateOpportunities($request, $opportunities));


            return redirect()->back()->with('success', __('The opportunities has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show(Opportunities $opportunities, $id)
    {
        if (\Auth::user()->isAbleTo('opportunities show')) {
            $opportunities = Opportunities::find($id);
            $satge         = OpportunitiesStage::find($id);
            $account_name  = SalesAccount::find($id);

            if(module_is_active('CustomField')){
                $opportunities->customField = \Workdo\CustomField\Entities\CustomField::getData($opportunities, 'Sales','Opportunities');
                $customFields      = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'Sales')->where('sub_module','Opportunities')->get();
            }else{
                $customFields = null;
            }

            return view('sales::opportunities.view', compact('opportunities', 'satge', 'account_name', 'customFields'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if (\Auth::user()->isAbleTo('opportunities edit')) {
            $opportunities = Opportunities::find($id);
            $stages        = OpportunitiesStage::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $lead_source = "";
            if (module_is_active('Lead')) {
                $lead_source   = \Workdo\Lead\Entities\Source::where('created_by', creatorId())->where('workspace_id', getActiveWorkSpace())->get()->pluck('name', 'id');
            }
            $user          = User::where('workspace_id', getActiveWorkSpace())->emp()->pluck('name', 'id');
            $user->prepend('--', 0);
            $account_name  = SalesAccount::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $contact       = Contact::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $contact->prepend('--', '');
            $documents     = SalesDocument::where('opportunities', $opportunities->id)->where('workspace', getActiveWorkSpace())->get();
            $parent        = 'opportunities';
            $log_type      = 'opportunities comment';
            $streams       = Stream::where('log_type', $log_type)->get();
            $salesorders   = SalesOrder::where('opportunity', $opportunities->id)->where('workspace', getActiveWorkSpace())->get();
            $quotes        = Quote::where('opportunity', $opportunities->id)->where('workspace', getActiveWorkSpace())->get();
            $salesinvoices = SalesInvoice::where('opportunity', $opportunities->id)->where('workspace', getActiveWorkSpace())->get();

            // get previous user id
            $previous = Opportunities::where('id', '<', $opportunities->id)->max('id');
            // get next user id
            $next = Opportunities::where('id', '>', $opportunities->id)->min('id');

            if(module_is_active('CustomField')){
                $opportunities->customField = \Workdo\CustomField\Entities\CustomField::getData($opportunities, 'sales','Opportunities');
                $customFields               = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'sales')->where('sub_module','Opportunities')->get();
            }else{
                $customFields = null;
            }

            return view('sales::opportunities.edit', compact('opportunities', 'salesorders', 'quotes', 'salesinvoices', 'user', 'stages', 'lead_source', 'account_name', 'contact', 'documents', 'streams', 'previous', 'next','customFields'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
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
        if (\Auth::user()->isAbleTo('opportunities edit')) {
            $opportunities = Opportunities::find($id);
            $validator     = \Validator::make(
                $request->all(),
                [
                    'name'          => 'required|string|max:120',
                    'account'       => 'required',
                    'stage'         => 'required',
                    'amount'        => 'required|numeric',
                    'probability'   => 'required|numeric',
                    'close_date'    => 'required',

                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $opportunities['user_id']     = $request->user;
            $opportunities['name']        = $request->name;
            $opportunities['account']     = $request->account;
            $opportunities['contact']    = $request->contact;
            $opportunities['stage']       = $request->stage;
            $opportunities['amount']      = $request->amount;
            $opportunities['probability'] = $request->probability;
            $opportunities['close_date']  = $request->close_date;
            $opportunities['lead_source'] = $request->lead_source;
            $opportunities['description'] = $request->description;
            $opportunities['workspace']   = getActiveWorkSpace();
            $opportunities['created_by']  = creatorId();
            $opportunities->update();

            Stream::create(
                [
                    'user_id' => Auth::user()->id, 'created_by' => creatorId(),
                    'log_type' => 'updated',
                    'remark' => json_encode(
                        [
                            'owner_name' => Auth::user()->username,
                            'title' => 'opportunities',
                            'stream_comment' => '',
                            'user_name' => $opportunities->name,
                        ]
                    ),
                ]
            );
            if(module_is_active('CustomField'))
            {
                \Workdo\CustomField\Entities\CustomField::saveData($opportunities, $request->customField);
            }
            event(new UpdateOpportunities($request, $opportunities));

            return redirect()->back()->with('success', __('The opportunities details are updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        if (\Auth::user()->isAbleTo('opportunities delete')) {
            $opportunities = Opportunities::find($id);
            if(module_is_active('CustomField'))
            {
                $customFields = \Workdo\CustomField\Entities\CustomField::where('module','sales')->where('sub_module','Opportunities')->get();
                foreach($customFields as $customField)
                {
                    $value = \Workdo\CustomField\Entities\CustomFieldValue::where('record_id', '=', $opportunities->id)->where('field_id',$customField->id)->first();
                    if(!empty($value)){
                        $value->delete();
                    }
                }
            }
            event(new DestroyOpportunities($opportunities));

            $opportunities->delete();

            return redirect()->back()->with('success', __('The ') . $opportunities->name . __(' opportunities has been deleted.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function grid()
    {
        if (\Auth::user()->isAbleTo('opportunities manage')) {
            $stages         = OpportunitiesStage::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            $opportunities = Opportunities::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();

            $defualtView         = new UserDefualtView();
            $defualtView->route  = \Request::route()->getName();
            $defualtView->module = 'opportunities';
            $defualtView->view   = 'kanban';
            SalesUtility::userDefualtView($defualtView);

            return view('sales::opportunities.grid', compact('opportunities', 'stages'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function changeorder(Request $request)
    {
        try {
            $post          = $request->all();
            $opportunities = Opportunities::find($post['opo_id']);
            $stage         = OpportunitiesStage::find($post['stage_id']);
    
    
            if (!empty($stage)) {
                $opportunities->stage = $post['stage_id'];
                $opportunities->save();
            }
            event(new CreateChangeOrder($request, $opportunities));
    
            foreach ($post['order'] as $key => $item) {
                $order        = Opportunities::find($item);
                $order->stage = $post['stage_id'];
                $order->save();
            }
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
