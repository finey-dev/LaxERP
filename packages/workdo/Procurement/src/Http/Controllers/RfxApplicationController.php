<?php

namespace Workdo\Procurement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\Procurement\DataTables\ApplicationPurchaseDataTable;
use Workdo\Procurement\DataTables\RfxApplicationDataTable;
use Workdo\Procurement\DataTables\RfxArchiveDataTable;
use Workdo\Procurement\DataTables\RfxVendorDataTable;
use Workdo\Procurement\DataTables\VendorOnBoardDataTable;
use Workdo\Procurement\Entities\BudgetType;
use Workdo\Procurement\Entities\ProcurementCustomQuestion;
use Workdo\Procurement\Entities\ProcurementInterviewSchedule;
use Workdo\Procurement\Entities\Rfx;
use Workdo\Procurement\Entities\RfxApplication;
use Workdo\Procurement\Entities\RfxApplicationItem;
use Workdo\Procurement\Entities\RfxApplicationNote;
use Workdo\Procurement\Entities\RfxStage;
use Illuminate\Support\Facades\Auth;
use Workdo\Procurement\Entities\VendorOnBoard;
use Workdo\Procurement\Events\ConvertToVendor;
use Workdo\Procurement\Events\CreateRfxApplication;
use Workdo\Procurement\Events\CreateRfxApplicationNote;
use Workdo\Procurement\Events\CreateRfxApplicationRating;
use Workdo\Procurement\Events\CreateRfxApplicationSkill;
use Workdo\Procurement\Events\CreateRfxApplicationStageChange;
use Workdo\Procurement\Events\CreateVendorOnBoard;
use Workdo\Procurement\Events\DestroyRfxApplication;
use Workdo\Procurement\Events\DestroyRfxApplicationNote;
use Workdo\Procurement\Events\DestroyVendorOnBoard;
use Workdo\Procurement\Events\RfxApplicationArchive;
use Workdo\Procurement\Events\RfxApplicationChangeOrder;
use Illuminate\Support\Facades\Crypt;
use Workdo\Procurement\Events\UpdateVendorOnBoard;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Workdo\Account\Entities\Vender;
use App\Models\EmailTemplate;

class RfxApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        if (Auth::user()->isAbleTo('rfxapplication manage')) {
            $stages = RfxStage::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->orderBy('order', 'asc')->get();

            $rfxs = Rfx::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('title', 'id');
            $rfxs->prepend('All', '');

            if (isset($request->start_date) && !empty($request->start_date)) {

                $filter['start_date'] = $request->start_date;
            } else {

                $filter['start_date'] ='';
            }

            if (isset($request->end_date) && !empty($request->end_date)) {

                $filter['end_date'] = $request->end_date;
            } else {

                $filter['end_date'] = '';
            }

            if (isset($request->rfx) && !empty($request->rfx)) {

                $filter['rfx'] = $request->rfx;
            } else {
                $filter['rfx'] = '';
            }

            return view('procurement::rfxApplication.index', compact('stages', 'rfxs', 'filter'));
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
        if (Auth::user()->isAbleTo('rfxapplication create')) {
            $rfxs = Rfx::where('created_by', creatorId())->where('status', 'active')->where('workspace', getActiveWorkSpace())->get()->pluck('title', 'id');
            $rfxs->prepend('--', '');
            $questions = ProcurementCustomQuestion::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            $application_type = RfxApplication::$application_type;
            $bid_type = RfxApplication::$bid_type;
            return view('procurement::rfxApplication.create', compact('rfxs', 'questions', 'application_type', 'bid_type'));
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
        if (Auth::user()->isAbleTo('rfxapplication create')) {

            $validator = \Validator::make(
                $request->all(),
                [
                    'rfx' => 'required',
                    'application_type' => 'required',
                    'name' => 'required|max:120',
                    'email' => 'required|max:100',
                    'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:15',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            if (!empty($request->profile)) {

                $filenameWithExt = $request->file('profile')->getClientOriginalName();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension = $request->file('profile')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $upload = upload_file($request, 'profile', $fileNameToStore, 'RfxApplication');
                if ($upload['flag'] == 1) {
                    $url = $upload['url'];
                } else {
                    return redirect()->back()->with('error', $upload['msg']);
                }
            }

            if (!empty($request->proposal)) {

                $filenameWithExt1 = $request->file('proposal')->getClientOriginalName();
                $filename1 = pathinfo($filenameWithExt1, PATHINFO_FILENAME);
                $extension1 = $request->file('proposal')->getClientOriginalExtension();
                $fileNameToStore1 = $filename1 . '_' . time() . '.' . $extension1;

                $upload = upload_file($request, 'proposal', $fileNameToStore1, 'RfxApplication');
                if ($upload['flag'] == 1) {
                    $url1 = $upload['url'];
                } else {
                    return redirect()->back()->with('error', $upload['msg']);
                }
            }

            $stage = RfxStage::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->first();
            if (!empty($stage)) {
                $rfx = new RfxApplication();
                $rfx->rfx = $request->rfx;
                $rfx->application_type = $request->application_type;
                $rfx->name = $request->name;
                $rfx->email = $request->email;
                $rfx->phone = $request->phone;
                $rfx->profile = !empty($request->profile) ? $url : '';
                $rfx->proposal = !empty($request->proposal) ? $url1 : '';
                $rfx->cover_letter = $request->cover_letter;
                $rfx->dob = $request->dob;
                $rfx->gender = $request->gender;
                $rfx->country = $request->country;
                $rfx->state = $request->state;
                $rfx->stage = $stage->id;
                $rfx->city = $request->city;
                $rfx->custom_question = json_encode($request->question);
                $rfx->bid_type = $request->bid_type;
                $rfx->bid_total = $request->bid_total;
                $rfx->workspace = getActiveWorkSpace();
                $rfx->created_by = creatorId();
                $rfx->save();

                event(new CreateRfxApplication($request, $rfx));

                return redirect()->back()->with('success', __('The rfx application has been created successfully.'));
            } else {
                return redirect()->back()->with('error', __('Please create rfx stage'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show(ApplicationPurchaseDataTable $dataTable, $ids)
    {
        if (Auth::user()->isAbleTo('rfxapplication show')) {
            $id = Crypt::decrypt($ids);
            $rfxApplication = RfxApplication::find($id);
            if ($rfxApplication) {
                $vendorOnBoards = VendorOnBoard::where('application', $id)->first();
                $interview = ProcurementInterviewSchedule::where('applicant', $id)->first();
                $notes = RfxApplicationNote::where('application_id', $id)->get();

                $stages = RfxStage::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
                return $dataTable->with('applicationId',$id)->render('procurement::rfxApplication.show', compact('rfxApplication', 'notes', 'stages', 'vendorOnBoards', 'interview'));
            } else {
                return redirect()->back()->with('error', __('The rfx application detail is not found.'));
            }

        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return redirect()->route('rfx-application.index')->with('error', __('Permission denied.'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('rfxapplication delete')) {
            $rfxApplication = RfxApplication::find($id);
            if ($rfxApplication) {
                if ($rfxApplication->profile != null) {
                    delete_file($rfxApplication->profile);
                }
                if ($rfxApplication->proposal != null) {
                    delete_file($rfxApplication->proposal);
                }
                RfxApplicationItem::where('application_id', $id)->delete();
                event(new DestroyRfxApplication($rfxApplication));

                $rfxApplication->delete();

                return redirect()->route('rfx-application.index')->with('success', __('The rfx application has been deleted.'));
            } else {
                return redirect()->back()->with('error', __('The rfx application is not found.'));
            }

        } else {
            return redirect()->route('rfx-application.index')->with('error', __('Permission denied.'));
        }
    }

    public function getRfx(Request $request)
    {
        try {
            $rfx = Rfx::find($request->id);
            if ($rfx) {
                $rfx->applicant = !empty($rfx->applicant) ? explode(',', $rfx->applicant) : '';
                $rfx->visibility = !empty($rfx->visibility) ? explode(',', $rfx->visibility) : '';
                $rfx->custom_question = !empty($rfx->custom_question) ? explode(',', $rfx->custom_question) : '';
                return json_encode($rfx);
            } else {
                return redirect()->back()->with('error', __('The rfx is not found.'));
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function order(Request $request)
    {
        if (Auth::user()->isAbleTo('rfxapplication move')) {
            $post = $request->all();
            foreach ($post['order'] as $key => $item) {
                $application = RfxApplication::where('id', '=', $item)->first();
                $application->order = $key;
                $application->stage = $post['stage_id'];
                $application->save();
            }
            event(new RfxApplicationChangeOrder($request, $application));
            return response()->json(['status'=>1,'message'=>'Order Change Successfully!']);
        } else {
            return redirect()->route('rfx-application.index')->with('error', __('Permission denied.'));
        }
    }
    public function list(RfxApplicationDataTable $dataTable, Request $request)
    {
        if (Auth::user()->isAbleTo('rfxapplication manage')) {
            $stages = RfxStage::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->orderBy('order', 'asc')->get();
            $rfxs = Rfx::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('title', 'id');
            $rfxs->prepend('All', '');

            if (isset($request->start_date) && !empty($request->start_date)) {

                $filter['start_date'] = $request->start_date;
            } else {

                $filter['start_date'] = date("Y-m-d", strtotime("-1 month"));
            }

            if (isset($request->end_date) && !empty($request->end_date)) {

                $filter['end_date'] = $request->end_date;
            } else {

                $filter['end_date'] = date("Y-m-d H:i:s", strtotime("+1 hours"));
            }

            if (isset($request->rfx) && !empty($request->rfx)) {

                $filter['rfx'] = $request->rfx;
            } else {
                $filter['rfx'] = '';
            }
            return $dataTable->render('procurement::rfxApplication.list', compact('filter', 'rfxs', 'stages'));
        } else {
            return redirect()->back()->with('error', 'Permission denied');
        }
    }

    public function rating(Request $request, $id)
    {
        $rfxApplication = RfxApplication::find($id);
        if ($rfxApplication) {
            $rfxApplication->rating = $request->rating;
            $rfxApplication->save();
            event(new CreateRfxApplicationRating($request, $rfxApplication));
            return response()->json(['status'=>1,'message'=>__('Rating Add Successfully!')]);
        } else {
            return redirect()->back()->with('error', __('The rfx application detail is not found.'));
        }

    }

    public function archive($id)
    {
        $rfxApplication = RfxApplication::find($id);
        if ($rfxApplication) {
            if ($rfxApplication->is_archive == 0) {
                $rfxApplication->is_archive = 1;
                $rfxApplication->save();

                event(new RfxApplicationArchive($rfxApplication));

                return redirect()->route('rfx.application.archived')->with('success', __('The rfx application has been successfully added to the archive'));
            } else {
                $rfxApplication->is_archive = 0;
                $rfxApplication->save();

                event(new RfxApplicationArchive($rfxApplication));

                return redirect()->route('rfx-application.index')->with('success', __('The rfx application has been successfully removed to the archive.'));
            }
        } else {
            return redirect()->back()->with('error', __('The rfx application detail is not found.'));
        }
    }

    public function archived(RfxArchiveDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('vendoronboard manage')) {
            return $dataTable->render('procurement::rfxApplication.archived');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function stageChange(Request $request)
    {
        $application = RfxApplication::where('id', '=', $request->schedule_id)->first();
        if ($application) {
            $application->stage = $request->stage;
            $application->save();

            event(new CreateRfxApplicationStageChange($request, $application));

            return response()->json(['success' => __('This applicant`s stage has been changed successfully.')], 200);
        } else {
            return response()->json(['error' => __('The rfx application detail is not found')], 401);
        }
    }
    public function addSkill(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('rfxapplication add skill')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'skill' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $rfx = RfxApplication::find($id);
            if ($rfx) {
                $rfx->skill = $request->skill;
                $rfx->save();

                event(new CreateRfxApplicationSkill($request, $rfx));

                return redirect()->back()->with('success', __('The rfx application skill has been added successfully.'));
            } else {
                return redirect()->back()->with('error', __('The rfx is not found.'));
            }

        } else {
            return redirect()->route('rfx-application.index')->with('error', __('Permission denied.'));
        }
    }

    public function addNote(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('rfxapplication add note')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'note' => 'required|max:500',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $note = new RfxApplicationNote();
            $note->application_id = $id;
            $note->note = $request->note;
            $note->note_created = Auth::user()->id;
            $note->created_by = Auth::user()->id;
            $note->save();

            event(new CreateRfxApplicationNote($request, $note));

            return redirect()->back()->with('success', __('The rfx application note has been added successfully.'));
        } else {
            return redirect()->route('rfx-application.index')->with('error', __('Permission denied.'));
        }
    }

    public function destroyNote($id)
    {
        if (Auth::user()->isAbleTo('rfxapplication delete note')) {
            $note = RfxApplicationNote::find($id);
            if ($note) {
                event(new DestroyRfxApplicationNote($note));

                $note->delete();

                return redirect()->back()->with('success', __('The rfx application note has been deleted.'));
            } else {
                return redirect()->back()->with('error', __('The rfx application note is not found.'));
            }


        } else {
            return redirect()->route('rfx-application.index')->with('error', __('Permission denied.'));
        }
    }

    //    -----------------------Vendor OnBoard-----------------------------_

    public function vendorOnBoard(VendorOnBoardDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('vendoronboard manage')) {
            return $dataTable->render('procurement::vendorOnboard.onboard');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function grid()
    {
        if (Auth::user()->isAbleTo('vendoronboard manage')) {
            $vendorOnBoards = VendorOnBoard::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->paginate(11);

            return view('procurement::vendorOnboard.grid', compact('vendorOnBoards'));
        } else {
            return redirect()->back()->with('error', 'Permission denied');
        }
    }
    public function vendorBoardCreate($id)
    {
        if (Auth::user()->isAbleTo('vendoronboard create')) {
            $status = VendorOnBoard::$status;
            $rfx_type = VendorOnBoard::$rfx_type;
            $budget_duration = VendorOnBoard::$budget_duration;
            $budget_type = BudgetType::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $applications = ProcurementInterviewSchedule::select('procurement_interview_schedules.*', 'rfx_applications.name')
                ->join('rfx_applications', 'procurement_interview_schedules.applicant', '=', 'rfx_applications.id')->where('procurement_interview_schedules.workspace', getActiveWorkSpace())->get()->pluck('name', 'applicant');
            $applications->prepend('-', '');

            return view('procurement::vendorOnboard.onboardCreate', compact('id', 'status', 'applications', 'budget_type', 'rfx_type', 'budget_duration'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function vendorBoardStore(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('vendoronboard create')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'joining_date' => 'required',
                    'status' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $id = ($id == 0) ? $request->application : $id;

            $rfApplicationData = RfxApplication::find($id);
            if ($rfApplicationData) {

            }

            $vendorOnBoard = new VendorOnBoard();
            $vendorOnBoard->application = $id;
            $vendorOnBoard->joining_date = $request->joining_date;
            $vendorOnBoard->rfx_type = $request->rfx_type;
            $vendorOnBoard->days_of_week = $request->days_of_week;
            $vendorOnBoard->budget = $request->budget;
            $vendorOnBoard->budget_type = $request->budget_type;
            $vendorOnBoard->budget_duration = $request->budget_duration;
            $vendorOnBoard->status = $request->status;
            $vendorOnBoard->workspace = getActiveWorkSpace();
            $vendorOnBoard->created_by = creatorId();
            $vendorOnBoard->save();

            event(new CreateVendorOnBoard($request, $vendorOnBoard));

            $interview = ProcurementInterviewSchedule::where('applicant', $id)->first();
            if (!empty($interview)) {
                $interview->delete();
            }

            return redirect()->back()->with('success', __('The applicant has been successfully added to the vendor board.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function vendorBoardEdit($id)
    {
        if (Auth::user()->isAbleTo('vendoronboard edit')) {
            $vendorOnBoard = VendorOnBoard::find($id);
            if ($vendorOnBoard) {
                $status = VendorOnBoard::$status;
                $rfx_type = VendorOnBoard::$rfx_type;
                $budget_duration = VendorOnBoard::$budget_duration;
                $budget_type = BudgetType::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');

                return view('procurement::vendorOnboard.onboardEdit', compact('vendorOnBoard', 'status', 'rfx_type', 'budget_duration', 'budget_type'));
            } else {
                return response()->json(['error' => __('The vendor on board detail is not found.')], 401);
            }

        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }
    public function vendorBoardUpdate(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('vendoronboard edit')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'joining_date' => 'required',
                    'status' => 'required',
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $vendorOnBoard = VendorOnBoard::find($id);
            if ($vendorOnBoard) {
                $vendorOnBoard->joining_date = $request->joining_date;
                $vendorOnBoard->rfx_type = $request->rfx_type;
                $vendorOnBoard->days_of_week = $request->days_of_week;
                $vendorOnBoard->budget = $request->budget;
                $vendorOnBoard->budget_type = $request->budget_type;
                $vendorOnBoard->budget_duration = $request->budget_duration;
                $vendorOnBoard->status = $request->status;
                $vendorOnBoard->save();
                event(new UpdateVendorOnBoard($request, $vendorOnBoard));
                return redirect()->back()->with('success', __('The vendor board applicant are updated successfully.'));
            } else {
                return redirect()->back()->with('error', __('The vendor on board detail is not found.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function vendorBoardDelete($id)
    {
        if (Auth::user()->isAbleTo('vendoronboard delete')) {
            $vendorBoard = VendorOnBoard::find($id);
            if ($vendorBoard) {
                event(new DestroyVendorOnBoard($vendorBoard));
                $vendorBoard->delete();

                return redirect()->route('vendor.on.board')->with('success', __('The vendor on board has been deleted.'));
            } else {
                return redirect()->back()->with('error', __('The vendor on board detail is not found.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function vendorBoardConvert($id)
    {
        if (Auth::user()->isAbleTo('vendoronboard convert')) {
            $vendorOnBoard = VendorOnBoard::find($id);
            if ($vendorOnBoard) {
                $user = User::where('id', $vendorOnBoard->convert_to_employee)->first();
                $roles = Role::where('created_by', creatorId())->whereNotIn('name', \Auth::user()->not_emp_type)->get()->pluck('name', 'id');
                return view('procurement::rfxApplication.convert_vendor', compact('vendorOnBoard', 'roles', 'user'));
            } else {
                return response()->json(['error' => __('The vendor on board detail is not found.')], 401);
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function vendorBoardConvertData(Request $request, $id)
    {
        if (Auth::user()->type != 'super admin') {
            $canUse = PlanCheck('User', \Auth::user()->id);
            if ($canUse == false) {
                return redirect()->back()->with('error', 'You have maxed out the total number of User allowed on your current plan');
            }
        }
        $roles = Role::where('name', 'vendor')->where('guard_name', 'web')->where('created_by', creatorId())->first();
        $vendorOnBoard = VendorOnBoard::where('id', $id)->first();
        if ($vendorOnBoard) {
            if (Auth::user()->isAbleTo('vendoronboard convert')) {
                $rules = [
                    'name' => 'required|max:120',
                    'contact' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|max:15',
                    'billing_name' => 'required|max:120',
                    'billing_phone' => 'required|max:15',
                    'billing_address' => 'required|max:120',
                    'billing_city' => 'required|max:50',
                    'billing_state' => 'required|max:50',
                    'billing_country' => 'required|max:50',
                    'billing_zip' => 'required|max:10',
                ];

                $validator = \Validator::make($request->all(), $rules);
                if (empty($request->user_id)) {
                    $rules = [
                        'email' => 'required|email|unique:users|max:100',
                        'password' => 'required|max:10',
                        'contact' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:9|max:15',

                    ];
                    $validator = \Validator::make($request->all(), $rules);
                }
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->route('vendors.index')->with('error', $messages->first());
                }

                $roles = Role::where('name', 'vendor')->where('guard_name', 'web')->where('created_by', creatorId())->first();
                if (empty($roles)) {
                    return redirect()->back()->with('error', __('Vendor Role Not found !'));
                }

                if (isset($request->user_id)) {
                    $user = User::where('id', $request->user_id)->first();
                } else {
                    $user = User::create(
                        [
                            'name' => $request->name,
                            'email' => $request->email,
                            'mobile_no' => $request->phone,
                            'password' => $request->password,
                            'email_verified_at' => date('Y-m-d h:i:s'),
                            'type' => $roles->name,
                            'lang' => 'en',
                            'workspace_id' => getActiveWorkSpace(),
                            'active_workspace' => getActiveWorkSpace(),
                            'created_by' => creatorId(),
                        ]
                    );
                    $user->save();
                    $user->addRole($roles);
                }


                $vendor = new Vender();
                $vendor->vendor_id = $vendorOnBoard->vendorNumber();
                $vendor->user_id = $user->id;
                $vendor->name = $request->name;
                $vendor->contact = $request->contact;
                $vendor->email = $user->email;
                $vendor->tax_number = $request->tax_number;
                $vendor->billing_name = $request->billing_name;
                $vendor->billing_country = $request->billing_country;
                $vendor->billing_state = $request->billing_state;
                $vendor->billing_city = $request->billing_city;
                $vendor->billing_phone = $request->billing_phone;
                $vendor->billing_zip = $request->billing_zip;
                $vendor->billing_address = $request->billing_address;
                if (company_setting('bill_shipping_display') == 'on') {
                    $vendor->shipping_name = $request->shipping_name;
                    $vendor->shipping_country = $request->shipping_country;
                    $vendor->shipping_state = $request->shipping_state;
                    $vendor->shipping_city = $request->shipping_city;
                    $vendor->shipping_phone = $request->shipping_phone;
                    $vendor->shipping_zip = $request->shipping_zip;
                    $vendor->shipping_address = $request->shipping_address;
                }
                $vendor->lang = $user->lang;
                $vendor->created_by = creatorId();
                $vendor->workspace = getActiveWorkSpace();
                $vendor->save();
                if (!empty($user)) {
                    $vendorOnBoard->convert_to_vendor = $user->id;
                    $vendorOnBoard->save();
                }

                $rfx_application = RfxApplication::find($vendorOnBoard->application);
                $rfx_application->is_vendor = 1;
                $rfx_application->save();

                $company_settings = getCompanyAllSetting();
                if (!empty($company_settings['Create User']) && $company_settings['Create User'] == true) {
                    $User = User::where('id', $user->id)->where('workspace_id', '=', getActiveWorkSpace())->first();
                    $uArr = [
                        'email' => $User->email,
                        'password' => $request['password'],
                    ];
                    $resp = EmailTemplate::sendEmailTemplate('New User', [$User->email], $uArr);
                    return redirect()->back()->with('success', __('The application has been successfully converted into a vendor.') . ((!empty($resp) && $resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
                }
                event(new ConvertToVendor($request, $vendor));

                return redirect()->route('vendor.on.board')->with('success', __('The application has been successfully converted into a vendor.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('The vendor on board detail is not found.'));
        }
    }

    public function getRFxVendor(RfxVendorDataTable $dataTable, Request $request)
    {
        if (Auth::user()->isAbleTo('rfx vendor view')) {
            if (module_is_active('Account')) {
                $country = Vender::distinct()->pluck('billing_country', 'billing_country');
                $country->prepend('All', '');

                $state = Vender::distinct()->pluck('billing_state', 'billing_state');
                $state->prepend('All', '');
                $city = Vender::distinct()->pluck('billing_city', 'billing_city');
                $city->prepend('All', '');
                $filter = [
                    'name' => isset($request->name) ? $request->name : '',
                    'country' => isset($request->country) ? $request->country : '',
                    'state' => isset($request->state) ? $request->state : '',
                    'city' => isset($request->city) ? $request->city : '',
                ];
                return $dataTable->render('procurement::vendorOnboard.vendor_list', compact('country', 'state', 'filter', 'city'));
            } else {
                return redirect()->back()->with('error', __('Please Enable Accounting Module'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
