<?php

namespace Workdo\Procurement\Http\Controllers;

use App\Models\User;
use App\Models\WorkSpace;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\Procurement\DataTables\RfxDataTable;
use Workdo\Procurement\Entities\ProcurementCustomQuestion;
use Workdo\Procurement\Entities\ProcurementInterviewSchedule;
use Workdo\Procurement\Entities\Rfx;
use Illuminate\Support\Facades\Auth;
use Workdo\Procurement\Entities\RfxApplication;
use Workdo\Procurement\Entities\RfxApplicationItem;
use Workdo\Procurement\Entities\RfxApplicationNote;
use Workdo\Procurement\Entities\RfxCategory;
use Workdo\Procurement\Entities\RfxItem;
use Workdo\Procurement\Entities\RfxStage;
use Workdo\Procurement\Entities\VendorOnBoard;
use Workdo\Procurement\Events\CreateRfx;
use Workdo\Procurement\Events\CreateRfxApplication;
use Workdo\Procurement\Events\UpdateRfx;
use Workdo\Procurement\Events\DestroyRfx;
use Workdo\ProductService\Entities\ProductService;

class RfxController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(RfxDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('rfx manage')) {
            $data['total'] = Rfx::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->count();
            $data['active'] = Rfx::where('status', 'active')->where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->count();
            $data['in_active'] = Rfx::where('status', 'in_active')->where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->count();
            return $dataTable->render('procurement::rfx.index', compact('data'));
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
        if (Auth::user()->isAbleTo('rfx create')) {
            $categories = RfxCategory::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');

            $status = Rfx::$status;

            $customQuestion = ProcurementCustomQuestion::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();

            $users = User::where('created_by', '=', creatorId())->where('type', '=', 'client')->where('workspace_id', getActiveWorkSpace())->get()->pluck('name', 'id');
            if (count($users) != 0) {

                $users->prepend(__('Select Client'), '');
            }

            $rfx_type = Rfx::$rfx_type;

            $item_type = [];
            $items = [];
            if (module_is_active('ProductService')) {
                $items = ProductService::where('created_by', creatorId())->where('workspace_id', getActiveWorkSpace())->get()->pluck('name', 'id');
                $items->prepend('Select Items', '');
                $item_type = ProductService::$product_type;
            }
            return view('procurement::rfx.create', compact('categories', 'status', 'customQuestion', 'users', 'rfx_type', 'item_type', 'items'));
        } else {
            return redirect()->back()->with('error', ('Permission denied.'));
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {

        if (Auth::user()->isAbleTo('rfx create')) {

            $rules = [
                'title' => 'required|max:300',
                'location' => 'required|max:50',
                'category' => 'required',
                'rfx_type' => 'required',
                'budget_from' => 'required|numeric|min:0',
                'budget_to' => 'required|numeric|min:0',
                'skill' => 'required',
                'position' => 'required|min:0',
                'start_date' => 'required|after:yesterday',
                'end_date' => 'required|after_or_equal:start_date',
                'description' => 'required',
                'requirement' => 'required',
                'custom_question.*' => 'required',
            ];

            if (is_array($request->visibility) && in_array('terms', $request->visibility)) {
                $rules['terms_and_conditions'] = 'required';
            }

            $validator = \Validator::make(
                $request->all(),
                $rules
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $rfx = new Rfx();
            $rfx->title = $request->title;
            $rfx->location = !empty($request->location) ? $request->location : '';
            $rfx->category = $request->category;
            $rfx->skill = $request->skill;
            $rfx->position = $request->position;
            $rfx->status = $request->status;
            $rfx->rfx_type = $request->rfx_type;
            $rfx->budget_from = $request->budget_from;
            $rfx->budget_to = $request->budget_to;
            $rfx->billing_type = $request->billing_type;
            $rfx->start_date = $request->start_date;
            $rfx->end_date = $request->end_date;
            $rfx->description = $request->description;
            $rfx->requirement = $request->requirement;
            $rfx->terms_and_conditions = !empty($request->terms_and_conditions) ? $request->terms_and_conditions : '';
            $rfx->code = uniqid();
            $rfx->applicant = !empty($request->applicant) ? implode(',', $request->applicant) : '';
            $rfx->visibility = !empty($request->visibility) ? implode(',', $request->visibility) : '';
            $rfx->custom_question = !empty($request->custom_question) ? implode(',', $request->custom_question) : '';
            $rfx->workspace = getActiveWorkSpace();
            $rfx->created_by = creatorId();
            $rfx->save();

            if ($request->billing_type == "items") {
                $items = $request->items;
                for ($i = 0; $i < count($items); $i++) {
                    $rfxProduct = new RfxItem();
                    $rfxProduct->rfx_id = $rfx->id;
                    $rfxProduct->product_type = $items[$i]['product_type'];
                    $rfxProduct->product_id = $items[$i]['product_id'];
                    $rfxProduct->product_quantity = $items[$i]['quantity'];
                    $rfxProduct->product_price = $items[$i]['price'];
                    $rfxProduct->product_discount = $items[$i]['discount'];
                    $rfxProduct->product_tax = $items[$i]['tax'];
                    $rfxProduct->product_description = str_replace(array('\'', '"', '`', '{', "\n"), ' ', $items[$i]['product_description']);
                    $rfxProduct->workspace = getActiveWorkSpace();
                    $rfxProduct->created_by = creatorId();
                    $rfxProduct->save();
                }
            } else {
                $items = $request->rfx;
                for ($i = 0; $i < count($items); $i++) {
                    $rfxProduct = new RfxItem();
                    $rfxProduct->rfx_id = $rfx->id;
                    $rfxProduct->rfx_task = $items[$i]['task'];
                    $rfxProduct->rfx_description = str_replace(array('\'', '"', '`', '{', "\n"), ' ', $items[$i]['rfx_description']);
                    $rfxProduct->workspace = getActiveWorkSpace();
                    $rfx->created_by = creatorId();
                    $rfxProduct->save();
                }
            }


            event(new CreateRfx($request, $rfx));

            return redirect()->route('rfx.index')->with('success', __('The rfx has been created successfully.'));
        } else {
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
        if (Auth::user()->isAbleTo('rfx show')) {
            $rfx = Rfx::find($id);
            if ($rfx) {
                $rfxItemData = RfxItem::where('rfx_id', $rfx->id)->get();
                $status = Rfx::$status;
                $rfx->applicant = !empty($rfx->applicant) ? explode(',', $rfx->applicant) : '';
                $rfx->visibility = !empty($rfx->visibility) ? explode(',', $rfx->visibility) : '';
                $rfx->skill = !empty($rfx->skill) ? explode(',', $rfx->skill) : '';

                return view('procurement::rfx.show', compact('status', 'rfx', 'rfxItemData'));
            } else {
                return redirect()->back()->with('error', __('The rfx detail is not found.'));
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
        if (Auth::user()->isAbleTo('rfx edit')) {
            $categories = RfxCategory::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');


            $status = Rfx::$status;
            $rfx = Rfx::find($id);
            if ($rfx) {
                $rfx->applicant = explode(',', $rfx->applicant);
                $rfx->visibility = explode(',', $rfx->visibility);
                $rfx->custom_question = explode(',', $rfx->custom_question);

                $customQuestion = ProcurementCustomQuestion::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();

                $users = User::where('created_by', '=', creatorId())->where('type', '=', 'client')->where('workspace_id', getActiveWorkSpace())->get()->pluck('name', 'id');
                if (count($users) != 0) {

                    $users->prepend(__('Select Client'), '');
                }

                $rfx_type = Rfx::$rfx_type;
                $item_type = [];
                $items = [];
                if (module_is_active('ProductService')) {
                    $items = ProductService::where('created_by', creatorId())->where('workspace_id', getActiveWorkSpace())->get()->pluck('name', 'id');
                    $items->prepend('Select Items', '');
                    $item_type = ProductService::$product_type;
                }
                $rfxTaskData = [];
                $rfxItemData = [];
                if ($rfx->billing_type == 'rfx') {
                    $rfxTaskData = RfxItem::where('rfx_id', $id)->get();
                } else {
                    $rfxItemData = RfxItem::where('rfx_id', $id)->get();
                }


                return view('procurement::rfx.edit', compact('categories', 'status', 'rfx', 'customQuestion', 'users', 'rfx_type', 'item_type', 'items', 'rfxTaskData', 'rfxItemData'));
            } else {
                return redirect()->back()->with('error', __('The rfx detail is not found.'));
            }

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
        if (Auth::user()->isAbleTo('rfx edit')) {

            $rules = [
                'title' => 'required|max:300',
                'location' => 'required|max:50',
                'category' => 'required',
                'rfx_type' => 'required',
                'budget_from' => 'required|numeric|min:0',
                'budget_to' => 'required|numeric|min:0',
                'skill' => 'required',
                'position' => 'required|min:0',
                'start_date' => 'required|after:yesterday',
                'end_date' => 'required|after_or_equal:start_date',
                'description' => 'required',
                'requirement' => 'required',
                'custom_question.*' => 'required',
            ];

            if (is_array($request->visibility) && in_array('terms', $request->visibility)) {
                $rules['terms_and_conditions'] = 'required';
            }

            $validator = \Validator::make(
                $request->all(),
                $rules
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $rfx = Rfx::find($id);
            if ($rfx) {
                $billingTypeChanged = $request->billing_type != $rfx->billing_type;


                $rfx->title = $request->title;
                $rfx->location = !empty($request->location) ? $request->location : '';
                $rfx->category = $request->category;
                $rfx->skill = $request->skill;
                $rfx->position = $request->position;
                $rfx->status = $request->status;
                $rfx->rfx_type = $request->rfx_type;
                $rfx->budget_from = $request->budget_from;
                $rfx->budget_to = $request->budget_to;
                $rfx->billing_type = $request->billing_type;
                $rfx->start_date = $request->start_date;
                $rfx->end_date = $request->end_date;
                $rfx->description = $request->description;
                $rfx->requirement = $request->requirement;
                $rfx->terms_and_conditions = !empty($request->terms_and_conditions) ? $request->terms_and_conditions : '';
                $rfx->code = uniqid();
                $rfx->applicant = !empty($request->applicant) ? implode(',', $request->applicant) : '';
                $rfx->visibility = !empty($request->visibility) ? implode(',', $request->visibility) : '';
                $rfx->custom_question = !empty($request->custom_question) ? implode(',', $request->custom_question) : '';
                $rfx->save();

                if ($billingTypeChanged) {
                    RfxItem::where('rfx_id', $rfx->id)->delete();
                }


                if ($request->billing_type == "items") {
                    $items = $request->items;
                    $requestItemIds = [];
                    for ($i = 0; $i < count($items); $i++) {

                        $requestItemIds[] = $this->saveRfxItems($items[$i], $rfx->id, 'items');
                    }

                    if (!$billingTypeChanged) {
                        $existingItemIds = RfxItem::where('rfx_id', $rfx->id)->pluck('id')->toArray();

                        $itemsToDelete = array_diff($existingItemIds, $requestItemIds);
                        if (!empty($itemsToDelete)) {
                            RfxItem::whereIn('id', $itemsToDelete)->delete();
                        }
                    }
                } else {
                    $items = $request->rfx;
                    $requestItemIds = [];
                    for ($i = 0; $i < count($items); $i++) {
                        $requestItemIds[] = $this->saveRfxItems($items[$i], $rfx->id, 'rfx');
                    }

                    if (!$billingTypeChanged) {
                        $existingItemIds = RfxItem::where('rfx_id', $rfx->id)->pluck('id')->toArray();

                        $itemsToDelete = array_diff($existingItemIds, $requestItemIds);
                        if (!empty($itemsToDelete)) {
                            RfxItem::whereIn('id', $itemsToDelete)->delete();
                        }
                    }
                }
                event(new UpdateRfx($request, $rfx));

                return redirect()->route('rfx.index')->with('success', __('The rfx details are updated successfully.'));
            } else {
                return redirect()->back()->with('error', __('The rfx detail is not found.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    private function saveRfxItems($item, $rfxId, $billingType)
    {
        $rfxProduct = isset($item['id']) ? RfxItem::find($item['id']) : null;

        if (!$rfxProduct) {
            $rfxProduct = new RfxItem();
        }
        $rfxProduct->rfx_id = $rfxId;

        if ($billingType == "items") {
            $rfxProduct->product_type = $item['product_type'];
            $rfxProduct->product_id = $item['product_id'];
            $rfxProduct->product_quantity = $item['quantity'];
            $rfxProduct->product_price = $item['price'];
            $rfxProduct->product_discount = $item['discount'];
            $rfxProduct->product_tax = $item['tax'];
            $rfxProduct->product_description = str_replace(array('\'', '"', '', '{', "\n"), ' ', $item['product_description']);

        } else {
            $rfxProduct->rfx_task = $item['rfx_task'];
            $rfxProduct->rfx_description = str_replace(array('\'', '"', '', '{', "\n"), ' ', $item['rfx_description']);
        }

        $rfxProduct->workspace = getActiveWorkSpace();
        $rfxProduct->created_by = creatorId();
        $rfxProduct->save();

        return $rfxProduct->id;
    }

    public function items(Request $request)
    {

        $items = RfxItem::where('rfx_id', $request->rfx_id)->where('product_id', $request->product_id)->first();
        return json_encode($items);
    }
    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('rfx delete')) {
            $rfx = Rfx::find($id);
            if ($rfx) {
                $application = RfxApplication::where('rfx', $rfx->id)->get()->pluck('id');
                event(new DestroyRfx($rfx));
                VendorOnBoard::whereIn('application', $application)->delete();
                ProcurementInterviewSchedule::whereIn('applicant', $application)->delete();
                RfxApplicationNote::whereIn('application_id', $application)->delete();
                RfxApplication::where('rfx', $rfx->id)->delete();
                RfxItem::where('rfx_id', $rfx->id)->delete();

                $rfx->delete();

                return redirect()->route('rfx.index')->with('success', __('The rfx has been deleted'));
            } else {
                return redirect()->route('rfx.index')->with('error', __('The rfx detail is not found.'));
            }

        } else {
            return redirect()->route('rfx.index')->with('error', __('Permission denied.'));
        }
    }

    public function grid()
    {
        if (Auth::user()->isAbleTo('rfx manage')) {
            $rfxs = Rfx::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->paginate(11);
            $data['total'] = Rfx::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->count();
            $data['active'] = Rfx::where('status', 'active')->where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->count();
            $data['in_active'] = Rfx::where('status', 'in_active')->where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->count();

            return view('procurement::rfx.grid', compact('rfxs', 'data'));
        } else {
            return redirect()->back()->with('error', 'Permission denied');
        }
    }

    public function rfxListing($slug = null, $lang = null)
    {
        if (!empty($slug)) {
            try {
                $workspace = WorkSpace::where('slug', $slug)->first();
                $workspace_id = $workspace->id;
            } catch (\Throwable $th) {
                return redirect()->back();
            }
        } else {
            try {
                $workspace = getActiveWorkSpace();
                $workspace_id = $workspace;
                $workspace = WorkSpace::where('id', $workspace)->first();
                $slug = $workspace->slug;
            } catch (\Throwable $th) {
                return redirect()->back();
            }
        }
        $company_id = $workspace->created_by;

        try {
            $slug = $slug;
        } catch (\Throwable $th) {
            return redirect('login');
        }
        if ($lang == null) {
            $lang = 'en';
        }

        $rfxs = Rfx::where('created_by', $company_id)->where('status', 'active')->where('workspace', $workspace_id)->get();
        if ($rfxs) {
            \Session::put('lang', $lang);

            \App::setLocale($lang);

            $languages = languages();

            $currantLang = \Session::get('lang');
            if (empty($currantLang)) {
                $user = User::find($company_id);
                $currantLang = !empty($user) && !empty($user->lang) ? $user->lang : 'en';
            }
            return view('procurement::rfx.rfxlist', compact('rfxs', 'languages', 'currantLang', 'company_id', 'workspace_id','workspace','slug'));
        } else {
            return redirect()->route('rfx.index')->with('error', __('The rfx`s is not found.'));
        }

    }

    public function rfxRequirement($code, $lang)
    {
        $rfx = Rfx::where('code', $code)->first();
        if ($rfx) {
            if ($rfx->status == 'in_active') {
                return redirect()->back()->with('error', __('This rfx is not Active.'));
            }

            \Session::put('lang', $lang);

            \App::setLocale($lang);


            $languages = languages();

            $currantLang = \Session::get('lang');
            if (empty($currantLang)) {
                $currantLang = !empty($rfx->createdBy) ? $rfx->createdBy->lang : 'en';
            }

            $company_id = $rfx->created_by;
            $workspace_id = $rfx->workspace;
            return view('procurement::rfx.requirement', compact('rfx', 'languages', 'currantLang', 'company_id', 'workspace_id'));
        } else {
            return redirect()->back()->with('error', __('This rfx is not found.'));
        }
    }

    public function rfxApply($code, $lang)
    {
        \Session::put('lang', $lang);

        \App::setLocale($lang);

        $rfx = RFx::where('code', $code)->first();
        if ($rfx) {
            $que = !empty($rfx->custom_question) ? explode(",", $rfx->custom_question) : [];

            $questions = ProcurementCustomQuestion::wherein('id', $que)->get();

            $languages = languages();

            $currantLang = \Session::get('lang');
            if (empty($currantLang)) {
                $currantLang = !empty($rfx->createdBy) ? $rfx->createdBy->lang : 'en';
            }

            $company_id = $rfx->created_by;
            $workspace_id = $rfx->workspace;
            $rfxItemData = RfxItem::where('rfx_id', $rfx->id)->get();
            return view('procurement::rfx.apply', compact('rfx', 'questions', 'languages', 'currantLang', 'company_id', 'workspace_id', 'rfxItemData'));
        } else {
            return redirect()->back()->with('error', __('This rfx is not found.'));
        }
    }

    public function TermsAndCondition($code, $lang)
    {
        $rfx = RFx::where('code', $code)->first();
        if ($rfx) {
            if ($rfx->status == 'in_active') {
                return redirect()->back()->with('error', __('This rfx is not Active.'));
            }

            \Session::put('lang', $lang);

            \App::setLocale($lang);

            $languages = languages();

            $currantLang = \Session::get('lang');
            if (empty($currantLang)) {
                $currantLang = !empty($rfx->createdBy) ? $rfx->createdBy->lang : 'en';
            }

            $company_id = $rfx->created_by;
            $workspace_id = $rfx->workspace;
            return view('procurement::rfx.terms', compact('rfx', 'languages', 'currantLang', 'company_id', 'workspace_id'));
        } else {
            return redirect()->back()->with('error', __('This rfx is not Found.'));
        }
    }

    public function rfxApplyData(Request $request, $code)
    {
        $rules = [
            'name' => 'required|max:120',
            'email' => 'required|max:100',
            'phone' => 'required|max:15',
        ];
        if (isset($request->terms_condition_check) && empty($request->terms_condition_check)) {
            $rules['terms_condition_check'] = [
                'required',
            ];
        }

        $validator = \Validator::make(
            $request->all(),
            $rules
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $rfx = Rfx::where('code', $code)->first();
        if ($rfx) {
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
            $stage = RfxStage::where('created_by', $rfx->created_by)->where('workspace', $rfx->workspace)->where('order', \DB::raw("(select min(`order`) from rfx_stages)"))->first();

            $rfxApplication = new RfxApplication();
            $rfxApplication->rfx = $rfx->id;
            $rfxApplication->name = $request->name;
            $rfxApplication->email = $request->email;
            $rfxApplication->phone = $request->phone;
            $rfxApplication->profile = !empty($request->profile) ? $url : '';
            $rfxApplication->proposal = !empty($request->proposal) ? $url1 : '';
            $rfxApplication->cover_letter = $request->cover_letter;
            $rfxApplication->dob = $request->dob;
            $rfxApplication->gender = $request->gender;
            $rfxApplication->country = $request->country;
            $rfxApplication->state = $request->state;
            $rfxApplication->city = $request->city;
            $rfxApplication->stage = !empty($stage) ? $stage->id : 1;
            $rfxApplication->custom_question = json_encode($request->question);
            $rfxApplication->workspace = getActiveWorkSpace($rfx->created_by);
            $rfxApplication->bid_total = $request->bid_amount;
            $rfxApplication->bid_total_amount = $request->bid_total;
            $rfxApplication->billing_type = $request->billing_type;
            $rfxApplication->created_by = $rfx->created_by;
            $rfxApplication->save();

            if ($rfx->billing_type == 'items') {
                foreach ($request->items as $item) {
                    $rfxAppItem = new RfxApplicationItem();
                    $rfxAppItem->application_id = $rfxApplication->id;
                    $rfxAppItem->rfx_id = $rfx->id;
                    $rfxAppItem->product_type = $item['product_type'];
                    $rfxAppItem->product_id = $item['product_id'];
                    $rfxAppItem->product_price = $item['price'];
                    $rfxAppItem->product_discount = $item['discount'];
                    $rfxAppItem->product_tax = $item['tax'];
                    $rfxAppItem->product_total_amount = $item['amount'];
                    $rfxAppItem->product_description = $item['product_description'];
                    $rfxAppItem->workspace = $rfxApplication->workspace;
                    $rfxAppItem->created_by = $rfxApplication->created_by;
                    $rfxAppItem->save();
                }
            } elseif ($request->billing_type == 'rfx') {
                foreach ($request->rfx as $task) {
                    $rfxAppItem = new RfxApplicationItem();
                    $rfxAppItem->application_id = $rfxApplication->id;
                    $rfxAppItem->rfx_id = $rfx->id;
                    $rfxAppItem->rfx_task = $task['task'];
                    $rfxAppItem->rfx_price = $task['price'];
                    $rfxAppItem->rfx_discount = $task['discount'];
                    $rfxAppItem->rfx_tax = $task['tax'];
                    $rfxAppItem->rfx_total_amount = $task['amount'];
                    $rfxAppItem->rfx_description = $task['description'];
                    $rfxAppItem->workspace = $rfxApplication->workspace;
                    $rfxAppItem->created_by = $rfxApplication->created_by;
                    $rfxAppItem->save();
                }
            }

            event(new CreateRfxApplication($request, $rfxApplication));

            return redirect()->back()->with('success', __('The rfx application has been sent successfully.'));
        } else {
            return redirect()->back()->with('error', __('This rfx is not Found.'));
        }
    }

    public function product(Request $request)
    {
        $data['product'] = $product = ProductService::find($request->product_id);
        $data['unit'] = !empty($product) ? ((!empty($product->unit())) ? $product->unit()->name : '') : '';
        $data['taxRate'] = $taxRate = !empty($product) ? (!empty($product->tax_id) ? $product->taxRate($product->tax_id) : 0) : 0;
        $data['taxes'] = !empty($product) ? (!empty($product->tax_id) ? $product->tax($product->tax_id) : 0) : 0;
        $salePrice = !empty($product) ? $product->purchase_price : 0;
        $quantity = 1;
        $taxPrice = !empty($product) ? (($taxRate / 100) * ($salePrice * $quantity)) : 0;
        $data['totalAmount'] = !empty($product) ? ($salePrice * $quantity) : 0;
        return json_encode($data);
    }
}
