<?php

namespace Workdo\MarketingPlan\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Workdo\MarketingPlan\Entities\MarketingPlan;
use Workdo\ProductService\Entities\ProductService;
use Workdo\Planning\Entities\PlanningChallenge;
use Workdo\Planning\Entities\PlanningCharters;
use Workdo\Planning\Entities\PlanningComment;
use Workdo\Planning\Entities\PlanningStage;
use Workdo\Planning\Entities\PlanningStatus;
use Workdo\MarketingPlan\Events\CreateMarketingPlan;
use Workdo\MarketingPlan\Events\UpdateMarketingPlan;
use Workdo\MarketingPlan\Events\DestroyMarketingPlan;
use Workdo\MarketingPlan\Entities\MarketingPlanItem;
use Workdo\MarketingPlan\Entities\MarketingPlanComment;
use Workdo\MarketingPlan\DataTables\MarketingPlanDataTable;

class MarketingPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(MarketingPlanDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('marketing plan manage')) {

            return $dataTable->render('marketing-plan::marketingplan.index');
        } else {

            return redirect()->back()->with('error', __('Permission denied'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if (Auth::user()->isAbleTo('marketing plan create')) {
            $Planningstatus = PlanningStatus::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $Challenge = PlanningChallenge::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $Planningstage = PlanningStage::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $users  = User::where('created_by', creatorId())->where('type', '!=', 'company')->where('active_workspace', getActiveWorkSpace())->orWhere('id', Auth::user()->id)->get();
            $role   = Role::where('created_by', creatorId())->whereNotIn('name', Auth::user()->not_emp_type)->get()->pluck('name', 'id');
            $item_types = MarketingPlanItem::$item_type;
            $item = ProductService::where('created_by', '=', creatorId())->where('workspace_id', '=', getActiveWorkSpace())->get()->pluck('name', 'id');
            $marketingplans = MarketingPlanItem::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->with('Items')->get();
            return view('marketing-plan::marketingplan.create', compact('Planningstatus', 'Planningstage', 'users', 'role', 'Challenge', 'item_types', 'item', 'marketingplans'));
        } else {

            return redirect()->back()->with('error', __('Permission denied'));
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('marketing plan create')) {
            if (!empty($request->video)) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'name' => 'required|max:50',
                        // 'type' => 'required',
                        'status' => 'required',
                        'stage' => 'required',
                        'challenge' => 'required',
                        'visibility_type' => 'required',
                        'description' => 'required',
                        'thumbnail_image' => 'required',
                        'video' => 'required | mimes:mp4,ogx,oga,ogv,ogg,webm',

                    ]
                );
            } elseif ($request->visibility_type == 'role') {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'role_list' => 'required',
                    ]
                );
            } else {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'name' => 'required|max:50',
                        // 'type' => 'required',
                        'status' => 'required',
                        'stage' => 'required',
                        'challenge' => 'required',
                        'description' => 'required',
                        'visibility_type' => 'required',
                        'thumbnail_image' => 'required',
                        'users_list' => 'required',
                    ]
                );
            }
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $users = [];
            if ($request->has('users_list') && !empty($request['users_list'])) {
                $users = User::whereIn('email', $request['users_list'])
                    ->where('active_workspace', getActiveWorkSpace())
                    ->get()
                    ->pluck('id')
                    ->toArray();

                if (!empty($users)) {
                    $userIds = $users;
                }
            }

            $Challenge = PlanningChallenge::where('created_by', '=', creatorId())->where('id', $request->challenge)->where('workspace', getActiveWorkSpace())->first();

            $currentDate = Carbon::now();
            $formattedDate = company_date_formate($currentDate);
            $ExpireDate =   company_date_formate($Challenge->end_date);

            if (!empty($ExpireDate) && $ExpireDate > $formattedDate) {

                $MarketingPlan                           = new MarketingPlan();
                $MarketingPlan->name                     = $request->name;
                $MarketingPlan->status                   = $request->status;
                $MarketingPlan->stage                    = $request->stage;
                $MarketingPlan->challenge                = $request->challenge;
                $MarketingPlan->visibility_type          = $request->visibility_type;
                $MarketingPlan->description              = $request->description;
                $MarketingPlan->business_summary         = $request->business_summary;
                $MarketingPlan->company_description      = $request->company_description;
                $MarketingPlan->team                     = $request->team;
                $MarketingPlan->business_initiative      = $request->business_initiative;
                $MarketingPlan->target_market            = $request->target_market;
                $MarketingPlan->marketing_channels       = $request->marketing_channels;
                $MarketingPlan->budget                   = $request->budget;
                $MarketingPlan->notes                    = $request->notes;
                $MarketingPlan->role_id                  = !empty($request->role_list) ? $request->role_list : 0;
                $MarketingPlan->user_id                  = implode(',', $users);


                if ($request->hasFile('thumbnail_image')) {
                    $filenameWithExt = $request->file('thumbnail_image')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('thumbnail_image')->getClientOriginalExtension();
                    $fileName = 'thumbnail_' . $filename . time() . rand() . '.' . $extension;

                    $upload_thumbnail = upload_file($request, 'thumbnail_image', $fileName, 'MarketingPlanAttachment');
                    if ($upload_thumbnail['flag'] == 1) {
                        $url = $upload_thumbnail['url'];
                    } else {
                        return redirect()->back()->with('error', $upload_thumbnail['msg']);
                    }
                    $MarketingPlan->thumbnail_image = $url;
                }

                if ($request->hasFile('video')) {
                    $filenameWithExt = $request->file('video')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $uploadedVideo = $request->file('video');
                    $extension = $request->file('video')->getClientOriginalExtension();
                    $fileName = 'video_' . $filename . time() . rand() . '.' . $extension;
                    $upload_video = upload_file($request, 'video', $fileName, 'MarketingPlanAttachment');
                    if ($upload_video['flag'] == 1) {
                        $url = $upload_video['url'];
                    } else {
                        return redirect()->back()->with('error', $upload_video['msg']);
                    }
                    $MarketingPlan->video_file = $url;
                }

                if ($request->hasFile('attachments')) {
                    $attachments = [];

                    foreach ($request->file('attachments') as $file) {
                        $name = $file->getClientOriginalName();

                        multi_upload_file($file, 'attachments', $name, 'MarketingPlanAttachment/');

                        $attachments[] = [
                            'name' => $name,
                            'path' => 'uploads/MarketingPlanAttachment' . '/' . $name,
                        ];
                    }

                    $MarketingPlan->marketing_attachments = json_encode($attachments);
                }

                $MarketingPlan->workspace           = getActiveWorkSpace();
                $MarketingPlan->created_by          = creatorId();
                $MarketingPlan->save();

                event(new CreateMarketingPlan($request, $MarketingPlan));
            } else {
                return redirect()->back()->with('error', __('The challenge deadline has expired!'));
            }
            return redirect()->route('marketing-plan.index')->with('success', __('The marketing plan has been created successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        if (Auth::user()->isAbleTo('marketing plan show')) {

            $MarketingPlans = MarketingPlan::find($id);
            if (!empty($MarketingPlans)) {

                $comments   = MarketingPlanComment::where('marketing_plan_id', $id)->where('parent', 0)->get();
                $allComments   = MarketingPlanComment::where('marketing_plan_id', $id)->get();

                return view('marketing-plan::marketingplan.show', compact('MarketingPlans', 'comments', 'allComments'));
            } else {
                return redirect()->back()->with('error', __('Marketing Plan Not Found.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if (Auth::user()->isAbleTo('marketing plan edit')) {

            $MarketingPlan = MarketingPlan::find($id);
            $Planningstatus = PlanningStatus::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $challengesArray = PlanningChallenge::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');

            $Planningstage = PlanningStage::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id');
            $users  = User::where('created_by', creatorId())->where('type', '!=', 'company')->where('active_workspace', getActiveWorkSpace())->orWhere('id', Auth::user()->id)->pluck('name', 'id');

            $MarketingPlan->user_id      = explode(',', $MarketingPlan->user_id);
            $role   = Role::where('created_by', creatorId())->whereNotIn('name', Auth::user()->not_emp_type)->get()->pluck('name', 'id');

            $item_types = MarketingPlanItem::$item_type;
            $item = ProductService::where('created_by', '=', creatorId())->where('workspace_id', '=', getActiveWorkSpace())->get()->pluck('name', 'id');
            $marketingplans = MarketingPlanItem::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->where('marketing_plan_id', $MarketingPlan->id)->get();

            return view('marketing-plan::marketingplan.edit', compact('Planningstatus', 'Planningstage', 'users', 'role', 'MarketingPlan', 'id', 'challengesArray', 'item_types', 'item', 'marketingplans'));
        } else {

            return redirect()->back()->with('error', __('Permission denied'));
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
        if (Auth::user()->isAbleTo('marketing plan edit')) {

            $MarketingPlan = MarketingPlan::find($id);


            if (!empty($request->video)) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'name' => 'required|max:50',
                        // 'type' => 'required',
                        'status' => 'required',
                        'stage' => 'required',
                        'challenge' => 'required',
                        'description' => 'required',
                        'visibility_type' => 'required',
                        'video' => 'required | mimes:mp4,ogx,oga,ogv,ogg,webm',

                    ]
                );
            } elseif ($request->visibility_type == 'role') {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'role_list' => 'required',
                    ]
                );
            } else {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'name' => 'required|max:50',
                        // 'type' => 'required',
                        'status' => 'required',
                        'stage' => 'required',
                        'challenge' => 'required',
                        'description' => 'required',
                        'visibility_type' => 'required',
                        'users_list' => 'required',
                    ]
                );
            }
            if (!empty($request->video)) {
                if ($MarketingPlan->video_file == null) {
                    $rules = [

                        'video' => 'required',
                    ];
                    $validator = \Validator::make($request->all(), $rules);
                    if ($validator->fails()) {
                        $messages = $validator->getMessageBag();
                        return redirect()->back()->with('error', $messages->first());
                    }
                }
            } else {
                if ($MarketingPlan->thumbnail_image == null) {
                    $rules = [

                        'thumbnail_image' => 'required',
                    ];
                    $validator = \Validator::make($request->all(), $rules);
                    if ($validator->fails()) {
                        $messages = $validator->getMessageBag();
                        return redirect()->back()->with('error', $messages->first());
                    }
                }
            }

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $MarketingPlan->name                    = $request->name;
            $MarketingPlan->status                  = $request->status;
            $MarketingPlan->stage                   = $request->stage;
            $MarketingPlan->challenge               = $request->challenge;
            $MarketingPlan->visibility_type         = $request->visibility_type;
            $MarketingPlan->description              = $request->description;
            $MarketingPlan->business_summary         = $request->business_summary;
            $MarketingPlan->company_description      = $request->company_description;
            $MarketingPlan->team                     = $request->team;
            $MarketingPlan->business_initiative      = $request->business_initiative;
            $MarketingPlan->target_market            = $request->target_market;
            $MarketingPlan->marketing_channels       = $request->marketing_channels;
            $MarketingPlan->budget                   = $request->budget;
            $MarketingPlan->notes                    = $request->notes;


            if ($request->visibility_type == 'users') {

                $MarketingPlan->user_id = !empty($request->users_list) ? implode(',', $request->users_list) : null;
                $MarketingPlan->role_id = null; // Set role_id to null
            } else {

                $MarketingPlan->role_id = !empty($request->role_list) ? $request->role_list : null;
                $MarketingPlan->user_id = null; // Set user_id to null
            }


            if ($request->hasFile('video')) {

                if (!empty($MarketingPlan->video_file)) {
                    delete_file($MarketingPlan->video_file);
                }
                $filenameWithExt = $request->file('video')->getClientOriginalName();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $uploadedVideo = $request->file('video');
                $extension = $request->file('video')->getClientOriginalExtension();
                $fileName = 'video_' . $filename . time() . rand() . '.' . $extension;
                $upload_video = upload_file($request, 'video', $fileName, 'MarketingPlanAttachment');
                if ($upload_video['flag'] == 1) {
                    $url = $upload_video['url'];
                } else {
                    return redirect()->back()->with('error', $upload_video['msg']);
                }
                $MarketingPlan->video_file = $url;
            }

            if ($request->hasFile('thumbnail_image')) {
                if (!empty($MarketingPlan->thumbnail_image)) {
                    delete_file($MarketingPlan->thumbnail_image);
                }
                $fileName = 'thumbnail_' . time() . '.' . $request->file('thumbnail_image')->getClientOriginalExtension();
                $upload_thumbnail = upload_file($request, 'thumbnail_image', $fileName, 'MarketingPlanAttachment');
                if ($upload_thumbnail['flag'] == 1) {
                    $MarketingPlan->thumbnail_image = $upload_thumbnail['url'];
                } else {
                    return redirect()->back()->with('error', $upload_thumbnail['msg']);
                }
            }

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $key => $attachment) {
                    $filenameWithExt = $attachment->getClientOriginalName();
                    $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension       = $attachment->getClientOriginalExtension();
                    $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                    $upload = multi_upload_file($attachment, 'attachments', $fileNameToStore, 'MarketingPlanAttachment');
                }
            }

            if ($request->hasFile('attachments')) {

                if (!empty($MarketingPlan->attachments)) {
                    delete_file($MarketingPlan->attachments);
                }

                $attachments = [];

                foreach ($request->file('attachments') as $file) {
                    $name = $file->getClientOriginalName();

                    multi_upload_file($file, 'attachments', $name, 'MarketingPlanAttachment/');

                    $attachments[] = [
                        'name' => $name,
                        'path' => 'uploads/MarketingPlanAttachment' . '/' . $name,
                    ];
                }
                $MarketingPlan->marketing_attachments = json_encode($attachments);
            }

            $MarketingPlan->workspace      = getActiveWorkSpace();
            $MarketingPlan->created_by     = creatorId();
            $MarketingPlan->save();

            event(new UpdateMarketingPlan($request, $MarketingPlan));

            return redirect()->route('marketing-plan.index')->with('success', __('The marketing plan details are updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied'));
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('marketing plan delete')) {

            $MarketingPlans = MarketingPlan::find($id);

            if (!empty($MarketingPlans->thumbnail_image) || !empty($MarketingPlans->video_file)) {
                delete_file($MarketingPlans->thumbnail_image, $MarketingPlans->video_file);
            }
            event(new DestroyMarketingPlan($MarketingPlans));

            $MarketingPlans->delete();

            return redirect()->route('marketing-plan.index')->with('success', __('The marketing plan has been deleted.'));
        } else {
            return redirect()->back()->with('error', 'Permission denied.');
        }
    }

    public function marketingplanCommentStore(Request $request, $marketing_plan_id)
    {
        $MarketingPlans = MarketingPlan::find($marketing_plan_id);

        $validator = \Validator::make(
            $request->all(),
            [
                'comment' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect() - back()->with('error', $messages->first());
        }
        if ($request->hasFile('file')) {
            $filenameWithExt = $request->file('file')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('file')->getClientOriginalExtension();
            $fileName = 'file_' . $filename . time() . rand() . '.' . $extension;


            $upload_file = upload_file($request, 'file', $fileName, 'MarketingPlan');
            if ($upload_file['flag'] == 1) {
                $url = $upload_file['url'];
            } else {
                return redirect()->back()->with('error', $upload_file['msg']);
            }
        }

        $comments                       = new MarketingPlanComment();
        $comments->marketing_plan_id    = $MarketingPlans->id;
        $comments->file                 = !empty($fileName) ? $fileName : '';
        $comments->comment              = $request->comment;
        $comments->parent               = !empty($request->parent) ? $request->parent : 0;
        $comments->comment_by           = \Auth::user()->id;
        $comments->workspace            = getActiveWorkSpace();
        $comments->save();

        return redirect()->back()->with('success', __('Comment Successfully Posted.'));
    }

    public function marketingplanCommentReply($marketing_plan_id, $comment_id)
    {

        return view('marketing-plan::marketingplan.commentReply', compact('marketing_plan_id', 'comment_id'));
    }

    public function rating(Request $request, $id)
    {
        $MarketingPlan         = MarketingPlan::find($id);
        $MarketingPlan->rating     = $request->rating;
        $MarketingPlan->save();
    }

    public function receipt($id)
    {
        $MarketingPlan        = MarketingPlan::find($id);
        return view('marketing-plan::marketingplan.print', compact('MarketingPlan'));
    }

    public function businesssummaryStore($id, Request $request)
    {
        if (Auth::user()->isAbleTo('marketingplan business summary create')) {

            $MarketingPlans                   = MarketingPlan::find($id);
            $MarketingPlans->business_summary = $request->business_summary;
            $MarketingPlans->save();

            return redirect()->back()->with('success', __('Business summary successfully saved.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function companydescriptionStore($id, Request $request)
    {
        if (Auth::user()->isAbleTo('marketingplan company description create')) {

            $MarketingPlans                   = MarketingPlan::find($id);
            $MarketingPlans->company_description = $request->company_description;
            $MarketingPlans->save();

            return redirect()->back()->with('success', __('Company description successfully saved.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function teamStore($id, Request $request)
    {
        if (Auth::user()->isAbleTo('marketingplan team create')) {

            $MarketingPlans                   = MarketingPlan::find($id);
            $MarketingPlans->team = $request->team;
            $MarketingPlans->save();

            return redirect()->back()->with('success', __('Team successfully saved.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function businessinitiativeStore($id, Request $request)
    {
        if (Auth::user()->isAbleTo('marketingplan business initiative create')) {

            $MarketingPlans                   = MarketingPlan::find($id);
            $MarketingPlans->business_initiative = $request->business_initiative;
            $MarketingPlans->save();

            return redirect()->back()->with('success', __('Business initiative successfully saved.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function targetmarketStore($id, Request $request)
    {
        if (Auth::user()->isAbleTo('marketingplan target market create')) {

            $MarketingPlans                   = MarketingPlan::find($id);
            $MarketingPlans->target_market   = $request->target_market;
            $MarketingPlans->save();

            return redirect()->back()->with('success', __('Target market successfully saved.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function marketingchannelsStore($id, Request $request)
    {
        if (Auth::user()->isAbleTo('marketingplan marketing channels create')) {

            $MarketingPlans                     = MarketingPlan::find($id);
            $MarketingPlans->marketing_channels = $request->marketing_channels;
            $MarketingPlans->save();

            return redirect()->back()->with('success', __('Marketing channels successfully saved.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function budgetStore($id, Request $request)
    {
        if (Auth::user()->isAbleTo('marketingplan budget create')) {

            $MarketingPlans                   = MarketingPlan::find($id);
            $MarketingPlans->budget           = $request->budget;
            $MarketingPlans->save();

            return redirect()->back()->with('success', __('Budget successfully saved.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function NotesDescStore($id, Request $request)
    {
        if (Auth::user()->isAbleTo('marketingplan notes create')) {

            $MarketingPlans                  = MarketingPlan::find($id);
            $MarketingPlans->notes           = $request->notes;
            $MarketingPlans->save();

            return redirect()->back()->with('success', __('Notes successfully saved.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function marketingplan_treeview(Request $request)
    {
        $Challenges_name       = PlanningChallenge::where('workspace', getActiveWorkSpace())->where('created_by', '=', creatorId())->get()->pluck('name', 'id');
        return view('marketing-plan::marketingplan.treeview', compact('Challenges_name'));
    }

    public function marketingplan_getTreeView(Request $request)
    {
        $Challenges       = PlanningChallenge::where('workspace', getActiveWorkSpace())->where('created_by', '=', creatorId())->where('id', $request->challeng_id)->first();
        $creatvity_name = MarketingPlan::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->where('challenge', $request->challeng_id)->get()->pluck('name', 'id')->toArray();

        $responseData = [
            'Challenges_name' => $Challenges->name,
            'creatvity_name' => $creatvity_name,
        ];

        return response()->json($responseData);
    }

    public function marketingplan_kanban()
    {
        if (Auth::user()->isAbleTo('marketing plan manage')) {

            $Charters = MarketingPlan::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get();
            $CharterStage = PlanningStage::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();

            return view('marketing-plan::marketingplan.kanban_view', compact('Charters', 'CharterStage'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function order(Request $request)
    {
        if (\Auth::user()->isAbleTo('marketing plan move')) {
            $post = $request->all();
            foreach ($post['order'] as $key => $item) {
                $MarketingPlan        = MarketingPlan::where('id', '=', $item)->first();
                $MarketingPlan->order = $key;
                $MarketingPlan->stage = $post['stage_id'];
                $MarketingPlan->save();
            }
            return response()->json(['success' => 'Charters updated successfully.'], 200);
        } else {
            return response()->json(['error' => __('Permission denied.')], 403);
        }
    }

    public function grid()
    {
        if (Auth::user()->isAbleTo('marketing plan manage')) {

            $Charters = MarketingPlan::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace());

            $Charters = $Charters->paginate(11);
            return view('marketing-plan::marketingplan.grid', compact('Charters'));
        } else {
            return redirect()->route('marketing-plan.index')->with('error', __('Permission denied.'));
        }
    }
}
