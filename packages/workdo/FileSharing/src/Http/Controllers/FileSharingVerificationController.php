<?php

namespace Workdo\FileSharing\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Workdo\FileSharing\DataTables\FileSharingVerificationesDatatable;
use Workdo\FileSharing\Entities\FileSharingVerification;
use Workdo\FileSharing\Events\FileVerificationCreate;
use Workdo\FileSharing\Events\FileVerificationDelete;
use Workdo\FileSharing\Events\FileVerificationDestroyRequest;
use Workdo\FileSharing\Events\FileVerificationUpdate;

class FileSharingVerificationController extends Controller
{

    public function index(FileSharingVerificationesDatatable $dataTable)
    {
        if (Auth::user()->isAbleTo('verification manage')) {
            if(Auth::user()->type == 'super admin'){
                $verification = FileSharingVerification::get();
                return $dataTable->render('file-sharing::verification.index', compact('verification'));

            }else{

            $lastVerification = FileSharingVerification::where('created_by', creatorId())
            ->where('workspace', getActiveWorkSpace())->orderBy('id', 'desc')->first();

            $verifications = FileSharingVerification::where('created_by', creatorId())
            ->where('workspace', getActiveWorkSpace())->get();

            return view('file-sharing::verification.company_verification', compact('lastVerification','verifications'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (Auth::user()->isAbleTo('files create')) {

            return view('file-sharing::verification.create');
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store(Request $request)
    {

        if (Auth::user()->isAbleTo('files create')) {
            $rules = [
                'attachment' => 'required',
            ];

            $validator = \Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->route('file-verification.index')->with('error', $messages->first());
            }
            if ($request->hasFile('attachment')) {
                $filenameWithExt = $request->file('attachment')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('attachment')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $uplaod = upload_file($request, 'attachment', $fileNameToStore, 'filesshare');

                if (isset($uplaod['flag']) && $uplaod['flag'] == 1 && isset($uplaod['url'])) {
                    $url = $uplaod['url'];
                } else {
                    // Handle the case where the upload failed or 'url' key is not present
                    return redirect()->back()->with('error', isset($uplaod['msg']) ? $uplaod['msg'] : 'File upload failed');
                }
            }

            $verification                   = new FileSharingVerification();
            $verification->user_id          = Auth::user()->id;
            $verification->applied_date     = date('Y-m-d H:i:s');
            $verification->status           = 0;
            $verification->attachment       = ($request->hasFile('attachment')) ? $url : '';
            $verification->workspace        = getActiveWorkSpace();
            $verification->created_by       = creatorId();
            $verification->save();

            event(new FileVerificationCreate($request, $verification));

            return redirect()->back()->with('success', __('The document has been submited successfully'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show($id)
    {
        return redirect()->back()->with('error', __('Permission denied.'));
        return view('file-sharing::show');
    }

    public function edit($id)
    {
        if (Auth::user()->isAbleTo('verification edit')) {

            $id = Crypt::decrypt($id);
            $verification = FileSharingVerification::find($id);

            if (!$verification) {
                return redirect()->back()->with('error', __('Verification document not found.'));
            }
            if (Auth::user()->type == 'super admin') {

                return view('file-sharing::verification.super_admin_edit', compact('verification'));
            } else {

                return view('file-sharing::verification.edit', compact('verification'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->isAbleTo('verification edit')) {

            $verification = FileSharingVerification::find($id);
            if (!$verification) {
                return redirect()->back()->with('error', __('Verification document not found.'));
            }
            if (Auth::user()->type == 'super admin') {
                if ($verification && $verification->status == 0) {

                    $verification->status       = $request->status;
                    $verification->action_date  = date('Y-m-d H:i:s');
                    $verification->save();

                    if ($request->status == 1) {
                        return redirect()->back()->with('success', __('The document verification request Approve successfully'));
                    } else {
                        return redirect()->back()->with('success', __('Document verification request Rejected'));
                    }
                } else {
                    return redirect()->back()->with('error', __('The document verification request status already update'));
                }
            } else {

                $rules = [
                    'attachment' => 'required|mimes:jpeg,png,jpg',
                ];
                $validator = \Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->route('file-verification.index')->with('error', $messages->first());
                }
                if ($request->hasFile('attachment')) {

                    $filenameWithExt    = $request->file('attachment')->getClientOriginalName();
                    $filename           = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension          = $request->file('attachment')->getClientOriginalExtension();
                    $fileNameToStore    = $filename . '_' . time() . '.' . $extension;

                    $uplaod = upload_file($request, 'attachment', $fileNameToStore, 'filesshare');

                    if (isset($uplaod['flag']) && $uplaod['flag'] == 1 && isset($uplaod['url'])) {
                        $url = $uplaod['url'];

                        // delete old attachment
                        if (!empty($request->attachment) && file_exists($verification->attachment)) {
                            delete_file($verification->attachment);
                        }
                    }

                    $verification->attachment   = ($request->hasFile('attachment')) ? $url : '';
                    $verification->workspace    = getActiveWorkSpace();
                    $verification->created_by   = creatorId();
                    $verification->update();

                    event(new FileVerificationUpdate($request, $verification));

                    return redirect()->back()->with('success', __('The document has been updated successfully'));
                }
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('verification delete')) {
            $id = Crypt::decrypt($id);
            $verification = FileSharingVerification::find($id);

            if (!$verification) {
                return redirect()->back()->with('error', __('Verification document not found.'));
            }

            $currentWorkspace = getActiveWorkSpace();
            if ($verification->created_by == creatorId() && $verification->workspace == $currentWorkspace) {

                if (!empty($verification->attachment)) {
                    delete_file($verification->attachment);
                }
                event(new FileVerificationDelete($verification));
                $verification->delete();
                return redirect()->back()->with('success', __('The document has been deleted'));
            } else {
                return redirect()->back()->with('error', 'Permission denied.');
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroyRequest($id)
    {
        if (Auth::user()->isAbleTo('verification delete') && Auth::user()->type == 'super admin') {
            $id = Crypt::decrypt($id);
            $verification = FileSharingVerification::find($id);

            if (!$verification) {
                return redirect()->back()->with('error', __('Verification request not found.'));
            }

            if (!empty($verification->attachment)) {
                delete_file($verification->attachment);
            }
            event(new FileVerificationDestroyRequest($verification));
            $verification->delete();
            return redirect()->back()->with('success', __('The document verification request has been deleted'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
