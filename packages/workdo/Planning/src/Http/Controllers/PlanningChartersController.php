<?php

namespace Workdo\Planning\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
use Workdo\Planning\DataTables\ChartersDataTable;
use Workdo\Planning\Entities\PlanningChallenge;
use Workdo\Planning\Entities\PlanningCharters;
use Workdo\Planning\Entities\PlanningComment;
use Workdo\Planning\Entities\PlanningStage;
use Workdo\Planning\Entities\PlanningStatus;
use Workdo\Planning\Events\CreateCharter;
use Workdo\Planning\Events\DestroyCharter;
use Workdo\Planning\Events\UpdateCharter;

class PlanningChartersController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(ChartersDataTable $dataTable)
    {
        if (Auth::user()->isAbleTo('charters manage')) {
            return $dataTable->render('planning::planningcharter.index');
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
        if (Auth::user()->isAbleTo('charters create')) {

            $Planningstatus = PlanningStatus::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id')->prepend('Select Status', '');
            $Challenge = PlanningChallenge::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id')->prepend('Select Challenge', '');
            $Planningstage = PlanningStage::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id')->prepend('Select Stage', '');
            $users = User::where('created_by', creatorId())->where('type', '!=', 'company')->where('active_workspace', getActiveWorkSpace())->orWhere('id', Auth::user()->id)->get();
            $role = Role::where('created_by', creatorId())->whereNotIn('name', Auth::user()->not_emp_type)->get()->pluck('name', 'id');

            return view('planning::planningcharter.create', compact('Planningstage', 'Planningstatus', 'users', 'role', 'Challenge', 'id'));
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
        if (Auth::user()->isAbleTo('charters create')) {

            $rules = [
                'charter_name' => 'required',
                'status' => 'required',
                'stage' => 'required',
                'challenge' => 'required',
                'visibility_type' => 'required',
                'description' => 'required',
                'thumbnail_image' => 'required|image',
            ];

            if ($request->type == 'video') {
                $rules['video'] = 'required|mimes:mp4,ogx,oga,ogv,ogg,webm';
            }

            if ($request->visibility_type == 'users') {
                $rules['users_list'] = 'required|array';
            } else {
                $rules['role_list'] = 'required';
            }

            $validator = \Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->getMessageBag()->first());
            }

            $users = [];
            if ($request->has('users_list') && !empty($request['users_list'])) {
                $users = User::whereIn('email', $request['users_list'])
                    ->where('created_by', creatorId())
                    ->where('type', '!=', 'company')
                    ->where('active_workspace', getActiveWorkSpace())
                    ->get()
                    ->pluck('id')
                    ->toArray();

                if (!empty($users)) {
                    $userIds = $users;
                }
            }

            $Challenge = PlanningChallenge::where('created_by', '=', creatorId())
                ->where('id', $request->challenge)
                ->where('workspace', getActiveWorkSpace())
                ->first();

            $formattedDate = Carbon::now()->format('Y-m-d');
            $ExpireDate = Carbon::parse($Challenge->end_date)->format('Y-m-d');

            if (!empty($ExpireDate) && $ExpireDate > $formattedDate) {

                $Charter                           = new PlanningCharters();
                $Charter->charter_name          = $request->charter_name;
                $Charter->status                   = $request->status;
                $Charter->stage                    = $request->stage;
                $Charter->challenge                = $request->challenge;
                $Charter->visibility_type          = $request->visibility_type;
                $Charter->dsescription             = $request->description;
                $Charter->organisational_effects   = $request->organisational_effects;
                $Charter->goal_description         = $request->goal_description;
                $Charter->notes                    = $request->notes;
                $Charter->role_id                  = !empty($request->role_list) ? $request->role_list : 0;
                $Charter->user_id                  = implode(',', $users);


                if ($request->hasFile('thumbnail_image')) {
                    $filenameWithExt = $request->file('thumbnail_image')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension = $request->file('thumbnail_image')->getClientOriginalExtension();
                    $fileName = 'thumbnail_' . $filename . time() . rand() . '.' . $extension;

                    $upload_thumbnail = upload_file($request, 'thumbnail_image', $fileName, 'CharterAttachment');
                    if ($upload_thumbnail['flag'] == 1) {
                        $url = $upload_thumbnail['url'];
                    } else {
                        return redirect()->back()->with('error', $upload_thumbnail['msg']);
                    }
                    $Charter->thumbnail_image = $url;
                }

                if ($request->hasFile('video')) {
                    $filenameWithExt = $request->file('video')->getClientOriginalName();
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $uploadedVideo = $request->file('video');
                    $extension = $request->file('video')->getClientOriginalExtension();
                    $fileName = 'video_' . $filename . time() . rand() . '.' . $extension;
                    $upload_video = upload_file($request, 'video', $fileName, 'CharterAttachment');
                    if ($upload_video['flag'] == 1) {
                        $url = $upload_video['url'];
                    } else {
                        return redirect()->back()->with('error', $upload_video['msg']);
                    }
                    $Charter->video_file = $url;
                }

                if ($request->hasFile('attachments')) {
                    $attachments = [];

                    foreach ($request->file('attachments') as $file) {
                        $name = $file->getClientOriginalName();

                        multi_upload_file($file, 'attachments', $name, 'CharterAttachment/');

                        $attachments[] = [
                            'name' => $name,
                            'path' => 'uploads/CharterAttachment' . '/' . $name,
                        ];
                    }

                    $Charter->charter_attachments = json_encode($attachments);
                }

                $Charter->workspace = getActiveWorkSpace();
                $Charter->created_by = creatorId();
                $Charter->save();

                event(new CreateCharter($request, $Charter));
            } else {
                return redirect()->back()->with('error', __('The challenge deadline has expired!'));
            }

            return redirect()->route('planningcharters.index')->with('success', __('The Charter has been created successfully'));
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
        if (Auth::user()->isAbleTo('charters show')) {
            $Charters = PlanningCharters::find($id);

            if (!empty($Charters)) {

                $comments = PlanningComment::where('charter_id', $id)->where('parent', 0)->get();
                $allComments = PlanningComment::where('charter_id', $id)->get();

                return view('planning::planningcharter.show', compact('Charters', 'comments', 'allComments'));
            } else {
                return redirect()->back()->with('error', __('Charter Not Found.'));
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
        if (Auth::user()->isAbleTo('charters edit')) {
            $Charters = PlanningCharters::find($id);
            $Planningstatus = PlanningStatus::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id')->prepend('Select Status', '');
            $challengesArray = PlanningChallenge::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id')->prepend('Select Challenge', '');

            $Planningstage = PlanningStage::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get()->pluck('name', 'id')->prepend('Select Stage', '');
            $users = User::where('created_by', creatorId())->where('type', '!=', 'company')->where('active_workspace', getActiveWorkSpace())->orWhere('id', Auth::user()->id)->pluck('name', 'id');

            $Charters->user_id = explode(',', $Charters->user_id);
            $role = Role::where('created_by', creatorId())->whereNotIn('name', Auth::user()->not_emp_type)->get()->pluck('name', 'id');
            return view('planning::planningcharter.edit', compact('Planningstatus', 'Planningstage', 'users', 'role', 'Charters', 'id', 'challengesArray'));
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

        if (Auth::user()->isAbleTo('charters edit')) {

            $Charters = PlanningCharters::find($id);

            $rules = [
                'charter_name' => 'required',
                'status' => 'required',
                'stage' => 'required',
                'challenge' => 'required',
                'visibility_type' => 'required',
                'description' => 'required',
            ];

            if ($request->type == 'video') {
                $rules['video'] = 'required|mimes:mp4,ogx,oga,ogv,ogg,webm';
            }

            if ($request->visibility_type == 'users') {
                $rules['users_list'] = 'required|array';
            } else {
                $rules['role_list'] = 'required';
            }


            $validator = \Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->getMessageBag()->first());
            }

            $Charters->charter_name = $request->charter_name;
            $Charters->status = $request->status;
            $Charters->stage = $request->stage;
            $Charters->challenge = $request->challenge;
            $Charters->visibility_type = $request->visibility_type;
            $Charters->dsescription = $request->description;
            $Charters->organisational_effects = $request->organisational_effects;
            $Charters->goal_description = $request->goal_description;
            $Charters->notes = $request->notes;


            if ($request->visibility_type == 'users') {

                $Charters->user_id = !empty($request->users_list) ? implode(',', $request->users_list) : null;
                $Charters->role_id = null;
            } else {

                $Charters->role_id = !empty($request->role_list) ? $request->role_list : null;
                $Charters->user_id = null;
            }
            if ($request->hasFile('video')) {

                if (!empty($Charters->video_file)) {
                    delete_file($Charters->video_file);
                }
                $filenameWithExt = $request->file('video')->getClientOriginalName();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $uploadedVideo = $request->file('video');
                $extension = $request->file('video')->getClientOriginalExtension();
                $fileName = 'video_' . $filename . time() . rand() . '.' . $extension;
                $upload_video = upload_file($request, 'video', $fileName, 'CharterAttachment');
                if ($upload_video['flag'] == 1) {
                    $url = $upload_video['url'];
                } else {
                    return redirect()->back()->with('error', $upload_video['msg']);
                }
                $Charters->video_file = $url;
            }

            if ($request->hasFile('thumbnail_image')) {
                if (!empty($Charters->thumbnail_image)) {
                    delete_file($Charters->thumbnail_image);
                }
                $fileName = 'thumbnail_' . time() . '.' . $request->file('thumbnail_image')->getClientOriginalExtension();
                $upload_thumbnail = upload_file($request, 'thumbnail_image', $fileName, 'CharterAttachment');
                if ($upload_thumbnail['flag'] == 1) {
                    $Charters->thumbnail_image = $upload_thumbnail['url'];
                } else {
                    return redirect()->back()->with('error', $upload_thumbnail['msg']);
                }
            }

            if ($request->hasFile('attachments')) {

                if (!empty($Charters->attachments)) {
                    delete_file($Charters->attachments);
                }

                $attachments = [];

                foreach ($request->file('attachments') as $file) {
                    $name = $file->getClientOriginalName();

                    multi_upload_file($file, 'attachments', $name, 'CharterAttachment/');

                    $attachments[] = [
                        'name' => $name,
                        'path' => 'uploads/CharterAttachment' . '/' . $name,
                    ];
                }
                $Charters->charter_attachments = json_encode($attachments);
            }

            $Charters->workspace = getActiveWorkSpace();
            $Charters->created_by = creatorId();
            $Charters->save();
            event(new UpdateCharter($request, $Charters));
            return redirect()->route('planningcharters.index')->with('success', __('The Charter details are updated successfully'));
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
        if (Auth::user()->isAbleTo('charters delete')) {

            $currentWorkspace = getActiveWorkSpace();
            $Charters = PlanningCharters::find($id);
            if ($Charters->created_by == creatorId() && $Charters->workspace == $currentWorkspace) {

                if (!empty($Charters->thumbnail_image) || !empty($Charters->video_file)) {
                    delete_file($Charters->thumbnail_image, $Charters->video_file);
                }
                event(new DestroyCharter($Charters));
                $Charters->delete();
                return redirect()->route('planningcharters.index')->with('success', __('Charters successfully deleted.'));
            } else {
                return redirect()->back()->with('error', __('The Charters has been deletedsss'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function grid()
    {
        if (Auth::user()->isAbleTo('charters manage')) {

            $Charters = PlanningCharters::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace());

            $Charters = $Charters->paginate(11);
            return view('planning::planningcharter.grid', compact('Charters'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function charters_treeview(Request $request)
    {
        $Challenges_name = PlanningChallenge::where('workspace', getActiveWorkSpace())->where('created_by', '=', creatorId())->get()->pluck('name', 'id');
        $Challenges = PlanningChallenge::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get();

        return view('planning::planningcharter.treeview', compact('Challenges_name'));
    }

    public function charters_getTreeView(Request $request)
    {
        $Challenges = PlanningChallenge::where('workspace', getActiveWorkSpace())->where('created_by', '=', creatorId())->where('id', $request->challeng_id)->first();
        $creatvity_name = PlanningCharters::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->where('challenge', $request->challeng_id)->get()->pluck('charter_name', 'id')->toArray();
        $responseData = [
            'Challenges_name' => $Challenges->name,
            'creatvity_name' => $creatvity_name,
        ];


        return response()->json($responseData);
    }

    public function charters_kanban()
    {
        if (Auth::user()->isAbleTo('charters manage')) {
            $Charters = PlanningCharters::where('created_by', '=', creatorId())->where('workspace', getActiveWorkSpace())->get();
            $CharterStage = PlanningStage::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->get();
            return view('planning::planningcharter.kanban_view', compact('Charters', 'CharterStage'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function order(Request $request)
    {
        if (\Auth::user()->isAbleTo('charters move')) {
            $post = $request->all();
            foreach ($post['order'] as $key => $item) {
                $Charters = PlanningCharters::where('id', '=', $item)->first();
                $Charters->order = $key;
                $Charters->stage = $post['stage_id'];
                $Charters->save();
            }
            return response()->json(['message' => 'Charters updated successfully.'], 200);
        } else {
            return response()->json(['error' => __('Permission denied.')], 403);
        }
    }
    public function rating(Request $request, $id)
    {
        $Charters = PlanningCharters::find($id);
        $Charters->rating = $request->rating;
        $Charters->save();
    }

    public function chartersCommentStore(Request $request, $charters_id)
    {
        $Charters = PlanningCharters::find($charters_id);

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


            $upload_file = upload_file($request, 'file', $fileName, 'Planning');
            if ($upload_file['flag'] == 1) {
                $url = $upload_file['url'];
            } else {
                return redirect()->back()->with('error', $upload_file['msg']);
            }
        }

        $comments = new PlanningComment();
        $comments->charter_id = $Charters->id;
        $comments->file = !empty($fileName) ? $fileName : '';
        $comments->comment = $request->comment;
        $comments->parent = !empty($request->parent) ? $request->parent : 0;
        $comments->comment_by = \Auth::user()->id;
        $comments->workspace = getActiveWorkSpace();
        $comments->save();

        return redirect()->back()->with('success', __('Comment Successfully Posted.'));
    }
    public function chartersCommentReply($charters_id, $comment_id)
    {

        return view('planning::planningcharter.commentReply', compact('charters_id', 'comment_id'));
    }



    public function organisationalStore($id, Request $request)
    {
        if (Auth::user()->isAbleTo('charters organisational effects create')) {

            $Charters = PlanningCharters::find($id);
            $Charters->organisational_effects = $request->organisational_effects;
            $Charters->save();

            return redirect()->back()->with('success', __('Organisational Effects successfully saved.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function descriptionStore($id, Request $request)
    {
        if (Auth::user()->isAbleTo('charters decription create')) {

            $Charters = PlanningCharters::find($id);
            $Charters->dsescription = $request->description;
            $Charters->save();

            return redirect()->back()->with('success', __('Description successfully saved.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function GoalDescStore($id, Request $request)
    {
        if (Auth::user()->isAbleTo('charters goal description create')) {

            $Charters = PlanningCharters::find($id);
            $Charters->goal_description = $request->goal_description;
            $Charters->save();

            return redirect()->back()->with('success', __('Goal Description successfully saved.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function NotesDescStore($id, Request $request)
    {
        if (Auth::user()->isAbleTo('charters notes create')) {

            $Charters = PlanningCharters::find($id);
            $Charters->notes = $request->notes;
            $Charters->save();

            return redirect()->back()->with('success', __('Notes successfully saved.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function receipt($id)
    {
        $Charters = PlanningCharters::find($id);
        return view('planning::planningcharter.print', compact('Charters'));
    }
}
