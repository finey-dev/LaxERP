<?php

namespace Workdo\Sales\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\User;
use Workdo\Sales\Entities\CommonCase;
use Workdo\Sales\Entities\CaseType;
use Workdo\Sales\Entities\UserDefualtView;
use Workdo\Sales\Entities\SalesUtility;
use Workdo\Sales\Entities\Contact;
use Workdo\Sales\Entities\SalesAccount;
use Workdo\Sales\Entities\Stream;
use Illuminate\Support\Facades\Auth;
use Workdo\Sales\DataTables\SalesCasesDataTable;
use Workdo\Sales\Events\CreateCommonCase;
use Workdo\Sales\Events\DestroyCommonCase;
use Workdo\Sales\Events\UpdateCommonCase;

class CommonCaseController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(SalesCasesDataTable $dataTable)
    {
        if(\Auth::user()->isAbleTo('case manage'))
        {
            $defualtView         = new UserDefualtView();
            $defualtView->route  = \Request::route()->getName();
            $defualtView->module = 'commoncases';
            $defualtView->view   = 'list';
            SalesUtility::userDefualtView($defualtView);

            return $dataTable->render('sales::commoncase.index');
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create($type, $id)
    {
        if(\Auth::user()->isAbleTo('case create'))
        {
            $status       = CommonCase::$status;
            $account      = SalesAccount::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $account->prepend('--', 0);
            $contact_name = Contact::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $contact_name->prepend('--', 0);
            $case_type    = CaseType::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $priority     = CommonCase::$priority;
            $user         = User::where('workspace_id',getActiveWorkSpace())->emp()->get()->pluck('name', 'id');
            $user->prepend('--', 0);
            if(module_is_active('CustomField')){
                $customFields =  \Workdo\CustomField\Entities\CustomField::where('workspace_id',getActiveWorkSpace())->where('module', '=', 'sales')->where('sub_module','Case')->get();
            }else{
                $customFields = null;
            }
            return view('sales::commoncase.create', compact('status', 'account', 'user', 'case_type', 'priority', 'contact_name', 'type', 'id','customFields'));
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

        if(\Auth::user()->isAbleTo('case create'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                    'name'      => 'required|string|max:120',
                                    'status'    => 'required',
                                    'priority'  => 'required',
                                    'type'      => 'required',
                                    'image'     => 'image',
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            if(!empty($request->attachments))
            {
                $filenameWithExt = $request->file('attachments')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('attachments')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $uplaod = upload_file($request,'attachments',$fileNameToStore,'Cases');
                if($uplaod['flag'] == 1)
                {
                    $url = $uplaod['url'];
                }
                else
                {
                    return redirect()->back()->with('error',$uplaod['msg']);
                }
            }

            $commoncase                = new CommonCase();
            $commoncase['user_id']     = $request->user;
            $commoncase['name']        = $request->name;
            $commoncase['number']      = $this->caseNumber();
            $commoncase['status']      = $request->status;
            $commoncase['account']     = $request->account;
            $commoncase['priority']    = $request->priority;
            $commoncase['contact']     = $request->contact;
            $commoncase['type']        = $request->type;
            $commoncase['description'] = $request->description;
            $commoncase['attachments'] = !empty($request->attachments) ? $url : '';
            $commoncase['workspace']         = getActiveWorkSpace();
            $commoncase['created_by']  = creatorId();
            $commoncase->save();
            Stream::create(
                [
                    'user_id' => Auth::user()->id,'created_by' => creatorId(),
                    'log_type' => 'created',
                    'remark' => json_encode(
                        [
                            'owner_name' => Auth::user()->username,
                            'title' => 'commoncase',
                            'stream_comment' => '',
                            'user_name' => $commoncase->name,
                        ]
                    ),
                ]
            );
            if(module_is_active('CustomField'))
            {
                \Workdo\CustomField\Entities\CustomField::saveData($commoncase, $request->customField);
            }
            event(new CreateCommonCase($request,$commoncase));

            return redirect()->back()->with('success', __('The common case has been created successfully.'));
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
    public function show($id)
    {
        if(\Auth::user()->isAbleTo('case show'))
        {
            $commonCase = CommonCase::find($id);
            if(module_is_active('CustomField')){
                $commonCase->customField = \Workdo\CustomField\Entities\CustomField::getData($commonCase, 'Sales','Case');
                $customFields      = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'Sales')->where('sub_module','Case')->get();
            }else{
                $customFields = null;
            }

            return view('sales::commoncase.view', compact('commonCase','customFields'));
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
    public function edit($id)
    {
        if(\Auth::user()->isAbleTo('case edit'))
        {
            $commonCase = CommonCase::find($id);
            $status     = CommonCase::$status;
            $account    = SalesAccount::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $account->prepend('--', 0);
            $contact   = Contact::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $contact->prepend('--', 0);
            $type       = CaseType::where('created_by', creatorId())->where('workspace',getActiveWorkSpace())->get()->pluck('name', 'id');
            $priority   = CommonCase::$priority;
            $user       = User::where('workspace_id',getActiveWorkSpace())->emp()->pluck('name', 'id');
            $user->prepend('--', 0);
            // get previous user id
            $previous = CommonCase::where('id', '<', $commonCase->id)->max('id');
            // get next user id
            $next = CommonCase::where('id', '>', $commonCase->id)->min('id');
            $log_type = 'commoncases comment';
            $streams  = Stream::where('log_type', $log_type)->get();

            if(module_is_active('CustomField')){
                $commonCase->customField = \Workdo\CustomField\Entities\CustomField::getData($commonCase, 'sales','Case');
                $customFields             = \Workdo\CustomField\Entities\CustomField::where('workspace_id', '=', getActiveWorkSpace())->where('module', '=', 'sales')->where('sub_module','Case')->get();
            }else{
                $customFields = null;
            }

            return view('sales::commoncase.edit', compact('commonCase', 'status', 'user', 'priority', 'type', 'contact', 'account', 'streams','previous','next','customFields'));
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
    public function update(Request $request, $id)
    {
        if(\Auth::user()->isAbleTo('case edit'))
        {
            $validator  = \Validator::make(
                $request->all(), [
                                    'name'      => 'required|string|max:120',
                                    'status'    => 'required',
                                    'priority'  => 'required',
                                    'type'      => 'required',
                                    'image'     => 'image',
                                ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $commonCase = CommonCase::find($id);
            if(!empty($request->attachments))
            {
                $filenameWithExt = $request->file('attachments')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('attachments')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $uplaod = upload_file($request,'attachments',$fileNameToStore,'Cases');
                if($uplaod['flag'] == 1)
                {
                    $url = $uplaod['url'];
                }
                else
                {
                    return redirect()->back()->with('error',$uplaod['msg']);
                }
            }else{
                $url = '';
            }

            $commonCase['user_id']     = $request->user;
            $commonCase['name']        = $request->name;
            $commonCase['status']      = $request->status;
            $commonCase['account']     = $request->account;
            $commonCase['priority']    = $request->priority;
            $commonCase['contact']     = $request->contact;
            $commonCase['type']        = $request->type;
            $commonCase['description'] = $request->description;
            $commonCase['attachments'] = !empty($request->attachments) ? $url : $commonCase->attachments;
            $commoncase['workspace']   = getActiveWorkSpace();
            $commonCase['created_by']  = creatorId();
            $commonCase->update();

            Stream::create(
                [
                    'user_id' => Auth::user()->id,'created_by' => creatorId(),
                    'log_type' => 'updated',
                    'remark' => json_encode(
                        [
                            'owner_name' => Auth::user()->username,
                            'title' => 'commonCase',
                            'stream_comment' => '',
                            'user_name' => $commonCase->name,
                        ]
                    ),
                ]
            );
            if(module_is_active('CustomField'))
            {
                \Workdo\CustomField\Entities\CustomField::saveData($commonCase, $request->customField);
            }
            event(new UpdateCommonCase($request,$commonCase));

            return redirect()->back()->with('success', __('The common case details are updated successfully.'));
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
    public function destroy($id)
    {
        if(\Auth::user()->isAbleTo('case delete'))
        {
            $commonCase = CommonCase::find($id);
            if(module_is_active('CustomField'))
            {
                $customFields = \Workdo\CustomField\Entities\CustomField::where('module','sales')->where('sub_module','Case')->get();
                foreach($customFields as $customField)
                {
                    $value = \Workdo\CustomField\Entities\CustomFieldValue::where('record_id', '=', $commonCase->id)->where('field_id',$customField->id)->first();
                    if(!empty($value)){
                        $value->delete();
                    }
                }
            }
            if(!empty($commonCase->attachments))
            {
                delete_file($commonCase->attachments);
            }

            event(new DestroyCommonCase($commonCase));
            $commonCase->delete();

            return redirect()->back()->with('success', __('The ') . $commonCase->name . __('case has been deleted.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function grid()
    {
        if(\Auth::user()->isAbleTo('case manage'))
        {
            $commonCases = CommonCase::where('created_by', creatorId())->where('workspace',getActiveWorkSpace());
            $commonCases = $commonCases->paginate(11);

            $defualtView         = new UserDefualtView();
            $defualtView->route  = \Request::route()->getName();
            $defualtView->module = 'commoncases';
            $defualtView->view   = 'grid';
            SalesUtility::userDefualtView($defualtView);

            return view('sales::commoncase.grid', compact('commonCases'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    function caseNumber()
    {
        $latest = CommonCase::where('workspace',getActiveWorkSpace())->latest()->first();
        if(!$latest)
        {
            return 1;
        }

        return $latest->number + 1;
    }
}
