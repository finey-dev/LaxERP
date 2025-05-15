<?php

namespace Workdo\SWOTAnalysisModel\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Workdo\Planning\Entities\PlanningChallenge;
use Workdo\Planning\Entities\PlanningCharters;
use Workdo\Planning\Entities\PlanningStage;
use Workdo\Planning\Entities\PlanningStatus;
use Illuminate\Routing\Controller;
use Workdo\SWOTAnalysisModel\DataTables\SWOTAnalysisDatatable;
use Workdo\SWOTAnalysisModel\Entities\SwotAnalysisModel;
use Workdo\SWOTAnalysisModel\Entities\SwotanalysisModelComment;
use Workdo\SWOTAnalysisModel\Events\CreateSWOTAnalysismodel;
use Workdo\SWOTAnalysisModel\Events\DestroySWOTAnalysismodel;
use Workdo\SWOTAnalysisModel\Events\UpdateSWOTAnalysismodel;

class SWOTAnalysisModelController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(SWOTAnalysisDatatable $dataTable)
    {
        if (Auth::user()->isAbleTo('SWOTAnalysisModel manage')) {

            return $dataTable->render('swotanalysis-model::swotanalysismodel.index');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create($id)
    {
        if (Auth::user()->isAbleTo('SWOTAnalysisModel manage')) {
            $Planningstatus = PlanningStatus::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id')->prepend('Select Status', '');
            $Challenge = PlanningChallenge::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id')->prepend('Select Challenge', '');
            $Planningstage = PlanningStage::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id')->prepend('Select Stage', '');
            $users  = User::where('created_by', creatorId())->where('type', '!=', 'company')->where('active_workspace', getActiveWorkSpace())->orWhere('id', Auth::user()->id)->get();
            $role   = Role::where('created_by', creatorId())->whereNotIn('name', Auth::user()->not_emp_type)->get()->pluck('name', 'id');
            return view('swotanalysis-model::swotanalysismodel.create', compact('Planningstage', 'Planningstatus', 'users', 'role', 'Challenge', 'id'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        if (Auth::user()->isAbleTo('SWOTAnalysisModel create')) {
            if ($request->type == 'video') {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'name' => 'required',
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
                        'name' => 'required',
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

                $swotanalysismodel                           = new SwotAnalysisModel();
                $swotanalysismodel->name             = $request->name;
                $swotanalysismodel->status                   = $request->status;
                $swotanalysismodel->stage                    = $request->stage;
                $swotanalysismodel->challenge                = $request->challenge;
                $swotanalysismodel->visibility_type          = $request->visibility_type;
                $swotanalysismodel->dsescription             = $request->description;
                $swotanalysismodel->strengths                = $request->strengths;
                $swotanalysismodel->weaknesses               = $request->weaknesses;
                $swotanalysismodel->opportunities            = $request->opportunities;
                $swotanalysismodel->threats                  = $request->threats;
                $swotanalysismodel->notes                    = $request->notes;
                $swotanalysismodel->role_id                  = !empty($request->role_list) ? $request->role_list : 0;
                $swotanalysismodel->user_id                  = implode(',', $users);


                if ($request->hasFile('thumbnail_image')) {
                    $filenameWithExt = $request->file('thumbnail_image')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('thumbnail_image')->getClientOriginalExtension();
                    $fileName = 'thumbnail_' . $filename . time() . rand() . '.' . $extension;

                    $upload_thumbnail = upload_file($request, 'thumbnail_image', $fileName, 'SWOTAnalysisModelAttachment');
                    if ($upload_thumbnail['flag'] == 1) {
                        $url = $upload_thumbnail['url'];
                    } else {
                        return redirect()->back()->with('error', $upload_thumbnail['msg']);
                    }
                    $swotanalysismodel->thumbnail_image = $url;
                }

                if ($request->hasFile('video')) {
                    $filenameWithExt = $request->file('video')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $uploadedVideo = $request->file('video');
                    $extension = $request->file('video')->getClientOriginalExtension();
                    $fileName = 'video_' . $filename . time() . rand() . '.' . $extension;
                    $upload_video = upload_file($request, 'video', $fileName, 'SWOTAnalysisModelAttachment');
                    if ($upload_video['flag'] == 1) {
                        $url = $upload_video['url'];
                    } else {
                        return redirect()->back()->with('error', $upload_video['msg']);
                    }
                    $swotanalysismodel->video_file = $url;
                }

                if ($request->hasFile('attachments')) {
                    $attachments = [];

                    foreach ($request->file('attachments') as $file) {
                        $name = $file->getClientOriginalName();

                        multi_upload_file($file, 'attachments', $name, 'SWOTAnalysisModelAttachment/');

                        $attachments[] = [
                            'name' => $name,
                            'path' => 'uploads/SWOTAnalysisModelAttachment' . '/' . $name,
                        ];
                    }

                    $swotanalysismodel->swotanalysismodel_attachments = json_encode($attachments);
                }
                $swotanalysismodel->workspace           = getActiveWorkSpace();
                $swotanalysismodel->created_by          = creatorId();
                $swotanalysismodel->save();

                event(new CreateSWOTAnalysismodel($request, $swotanalysismodel));
            } else {
                return redirect()->back()->with('error', __('The challenge deadline has expired!'));
            }

            return redirect()->route('swotanalysis-model.index')->with('success', __('The SWOT analysis model has been created successfully'));
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
        if (Auth::user()->isAbleTo('SWOTAnalysisModel show')) {
            $Charters = SwotAnalysisModel::find($id);

            if (!empty($Charters)) {

                $comments   = SwotanalysisModelComment::where('swotanalysis_model_id', $id)->where('parent', 0)->get();
                $allComments   = SwotanalysisModelComment::where('swotanalysis_model_id', $id)->get();

                return view('swotanalysis-model::swotanalysismodel.show', compact('Charters', 'comments', 'allComments'));
            } else {
                return redirect()->back()->with('error', __('SWOT Not Found.'));
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

        if (Auth::user()->isAbleTo('SWOTAnalysisModel edit')) {
            $swotanalysismodel = SwotAnalysisModel::find($id);
            $Planningstatus = PlanningStatus::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id')->prepend('Select Status', '');
            $challengesArray = PlanningChallenge::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id')->prepend('Select Challenge', '');

            $Planningstage = PlanningStage::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id')->prepend('Select Stage', '');
            $users  = User::where('created_by', creatorId())->where('type', '!=', 'company')->where('active_workspace', getActiveWorkSpace())->orWhere('id', Auth::user()->id)->pluck('name', 'id');

            $swotanalysismodel->user_id      = explode(',', $swotanalysismodel->user_id);
            $role   = Role::where('created_by', creatorId())->whereNotIn('name', Auth::user()->not_emp_type)->get()->pluck('name', 'id');
            return view('swotanalysis-model::swotanalysismodel.edit', compact('Planningstatus', 'Planningstage', 'users', 'role', 'swotanalysismodel', 'id', 'challengesArray'));
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
        if (Auth::user()->isAbleTo('SWOTAnalysisModel edit')) {

            $swotanalysismodel = SwotAnalysisModel::find($id);
            if ($request->type == 'video') {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'name' => 'required',
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
                        'name' => 'required',
                        'status' => 'required',
                        'stage' => 'required',
                        'challenge' => 'required',
                        'description' => 'required',
                        'visibility_type' => 'required',
                        'users_list' => 'required',
                    ]
                );
            }
            if ($request->type == 'video') {
                if ($swotanalysismodel->video_file == null) {
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
                if ($swotanalysismodel->thumbnail_image == null) {
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

            $swotanalysismodel->name         = $request->name;
            $swotanalysismodel->status                  = $request->status;
            $swotanalysismodel->stage                   = $request->stage;
            $swotanalysismodel->challenge               = $request->challenge;
            $swotanalysismodel->visibility_type         = $request->visibility_type;
            $swotanalysismodel->dsescription            = $request->description;
            $swotanalysismodel->strengths               = $request->strengths;
            $swotanalysismodel->weaknesses              = $request->weaknesses;
            $swotanalysismodel->opportunities           = $request->opportunities;
            $swotanalysismodel->threats                 = $request->threats;
            $swotanalysismodel->notes                   = $request->notes;


            if ($request->visibility_type == 'users') {

                $swotanalysismodel->user_id = !empty($request->users_list) ? implode(',', $request->users_list) : null;
                $swotanalysismodel->role_id = null; // Set role_id to null
            } else {

                $swotanalysismodel->role_id = !empty($request->role_list) ? $request->role_list : null;
                $swotanalysismodel->user_id = null; // Set user_id to null
            }


            if ($request->hasFile('video')) {

                if (!empty($swotanalysismodel->video_file)) {
                    delete_file($swotanalysismodel->video_file);
                }
                $filenameWithExt = $request->file('video')->getClientOriginalName();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $uploadedVideo = $request->file('video');
                $extension = $request->file('video')->getClientOriginalExtension();
                $fileName = 'video_' . $filename . time() . rand() . '.' . $extension;
                $upload_video = upload_file($request, 'video', $fileName, 'SWOTAnalysisModelAttachment');
                if ($upload_video['flag'] == 1) {
                    $url = $upload_video['url'];
                } else {
                    return redirect()->back()->with('error', $upload_video['msg']);
                }
                $swotanalysismodel->video_file = $url;
            }

            if ($request->hasFile('thumbnail_image')) {
                if (!empty($swotanalysismodel->thumbnail_image)) {
                    delete_file($swotanalysismodel->thumbnail_image);
                }
                $fileName = 'thumbnail_' . time() . '.' . $request->file('thumbnail_image')->getClientOriginalExtension();
                $upload_thumbnail = upload_file($request, 'thumbnail_image', $fileName, 'SWOTAnalysisModelAttachment');
                if ($upload_thumbnail['flag'] == 1) {
                    $swotanalysismodel->thumbnail_image = $upload_thumbnail['url'];
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

                    $upload = multi_upload_file($attachment, 'attachments', $fileNameToStore, 'SWOTAnalysisModelAttachment');
                }
            }


            if ($request->hasFile('attachments')) {

                if (!empty($swotanalysismodel->attachments)) {
                    delete_file($swotanalysismodel->attachments);
                }

                $attachments = [];

                foreach ($request->file('attachments') as $file) {
                    $name = $file->getClientOriginalName();

                    multi_upload_file($file, 'attachments', $name, 'SWOTAnalysisModelAttachment/');

                    $attachments[] = [
                        'name' => $name,
                        'path' => 'uploads/SWOTAnalysisModelAttachment' . '/' . $name,
                    ];
                }
                $swotanalysismodel->swotanalysismodel_attachments = json_encode($attachments);
            }

            $swotanalysismodel->workspace      = getActiveWorkSpace();
            $swotanalysismodel->created_by     = creatorId();
            $swotanalysismodel->save();
            event(new UpdateSWOTAnalysismodel($request, $swotanalysismodel));
            return redirect()->route('swotanalysis-model.index')->with('success', __('The SWOT analysis model details are updated successfully'));
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
        if (Auth::user()->isAbleTo('SWOTAnalysisModel delete')) {

            $currentWorkspace = getActiveWorkSpace();
            $swotanalysismodel = SwotAnalysisModel::find($id);
            if ($swotanalysismodel->created_by == creatorId() && $swotanalysismodel->workspace == $currentWorkspace) {

                if (!empty($swotanalysismodel->thumbnail_image) || !empty($swotanalysismodel->video_file)) {
                    delete_file($swotanalysismodel->thumbnail_image, $swotanalysismodel->video_file);
                }
                event(new DestroySWOTAnalysismodel($swotanalysismodel));
                $swotanalysismodel->delete();
                return redirect()->route('swotanalysis-model.index')->with('success', __('The SWOT analysis model has been deleted'));
            } else {
                return redirect()->back()->with('error', 'Permission denied.');
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function grid()
    {
        if (Auth::user()->isAbleTo('SWOTAnalysisModel manage')) {

            $Charters = SwotAnalysisModel::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace());

            $Charters = $Charters->paginate(11);
            return view('swotanalysis-model::swotanalysismodel.grid', compact('Charters'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function swotanalysismodel_kanban()
    {
        if (Auth::user()->isAbleTo('SWOTAnalysisModel manage')) {


            $Charters = SwotAnalysisModel::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get();

            $CharterStage = PlanningStage::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();

            return view('swotanalysis-model::swotanalysismodel.kanban_view', compact('CharterStage', 'Charters'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function order(Request $request)
    {

        if (\Auth::user()->isAbleTo('SWOTAnalysisModel Move')) {
            $post = $request->all();
            foreach ($post['order'] as $key => $item) {
                $swotanalysismodels        = SwotAnalysisModel::where('id', '=', $item)->first();
                $swotanalysismodels->order = $key;
                $swotanalysismodels->stage = $post['stage_id'];
                $swotanalysismodels->save();
            }
            return response()->json(['message' => 'Charters updated successfully.'], 200);
        } else {
            return redirect()->route('swotanalysis-model.index')->with('error', __('Permission denied.'));
        }
    }

    public function swotanalysismodel_treeview(Request $request)
    {
        $Challenges_name       = PlanningChallenge::where('workspace', getActiveWorkSpace())->where('created_by', '=', creatorId())->get()->pluck('name', 'id');
        $Challenges = PlanningChallenge::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get();;

        return view('swotanalysis-model::swotanalysismodel.treeview', compact('Challenges_name'));
    }

    public function swotanalysismodel_getTreeView(Request $request)
    {
        $Challenges       = PlanningChallenge::where('workspace', getActiveWorkSpace())->where('created_by', '=', creatorId())->where('id', $request->challeng_id)->first();
        $creatvity_name = SwotAnalysisModel::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->where('challenge', $request->challeng_id)->get()->pluck('name', 'id')->toArray();

        $responseData = [
            'Challenges_name' => $Challenges->name,
            'creatvity_name' => $creatvity_name,
        ];

        return response()->json($responseData);
    }

    public function rating(Request $request, $id)
    {
        $swotanalysismodels         = SwotAnalysisModel::find($id);
        $swotanalysismodels->rating = $request->rating;
        $swotanalysismodels->save();
    }

    public function receipt($id)
    {

        $swotanalysismodel        = SwotAnalysisModel::find($id);

        return view('swotanalysis-model::swotanalysismodel.print', compact('swotanalysismodel'));
    }

    public function strengthStore($id, Request $request)
    {
        if (Auth::user()->isAbleTo('SWOTAnalysisModel Strengths create')) {

            $Charters        = SwotAnalysisModel::find($id);
            $Charters->strengths = $request->strengths;
            $Charters->save();

            return redirect()->back()->with('success', __('Strengths successfully saved.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function descriptionStore($id, Request $request)
    {
        if (Auth::user()->isAbleTo('SWOTAnalysisModel decription create')) {

            $Charters        = SwotAnalysisModel::find($id);
            $Charters->dsescription = $request->description;
            $Charters->save();

            return redirect()->back()->with('success', __('Description successfully saved.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function WeaknessDescStore($id, Request $request)
    {
        if (Auth::user()->isAbleTo('SWOTAnalysisModel Weaknesses create')) {

            $Charters        = SwotAnalysisModel::find($id);
            $Charters->weaknesses = $request->weaknesses;
            $Charters->save();

            return redirect()->back()->with('success', __('Weaknesses successfully saved.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function OpportunitiesDescStore($id, Request $request)
    {
        if (Auth::user()->isAbleTo('SWOTAnalysisModel Opportunities create')) {

            $Charters        = SwotAnalysisModel::find($id);
            $Charters->opportunities = $request->opportunities;
            $Charters->save();

            return redirect()->back()->with('success', __('Opportunities successfully saved.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function ThreatsDescStore($id, Request $request)
    {
        if (Auth::user()->isAbleTo('SWOTAnalysisModel Threats create')) {

            $Charters        = SwotAnalysisModel::find($id);
            $Charters->threats = $request->threats;
            $Charters->save();

            return redirect()->back()->with('success', __('Threats successfully saved.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function NotesDescStore($id, Request $request)
    {
        if (Auth::user()->isAbleTo('SWOTAnalysisModel notes create')) {

            $Charters        = SwotAnalysisModel::find($id);
            $Charters->notes = $request->notes;
            $Charters->save();

            return redirect()->back()->with('success', __('Notes successfully saved.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function swotanalysismodelCommentReply($charters_id, $comment_id)
    {

        return view('swotanalysis-model::swotanalysismodel.commentReply', compact('charters_id', 'comment_id'));
    }

    public function swotanalysismodelCommentStore(Request $request, $charters_id)
    {
        $Charters = SwotAnalysisModel::find($charters_id);

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


            $upload_file = upload_file($request, 'file', $fileName, 'SWOTAnalysisModel');
            if ($upload_file['flag'] == 1) {
                $url = $upload_file['url'];
            } else {
                return redirect()->back()->with('error', $upload_file['msg']);
            }
        }

        $comments             = new SwotanalysisModelComment();
        $comments->swotanalysis_model_id   = $Charters->id;
        $comments->file       = !empty($fileName) ? $fileName : '';
        $comments->comment    = $request->comment;
        $comments->parent     = !empty($request->parent) ? $request->parent : 0;
        $comments->comment_by = \Auth::user()->id;
        $comments->workspace = getActiveWorkSpace();
        $comments->save();

        return redirect()->back()->with('success', __('Comment Successfully Posted.'));
    }
}
