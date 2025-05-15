<?php

namespace Workdo\FileSharing\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Workdo\FileSharing\DataTables\FileTrashDatatable;
use Workdo\FileSharing\Entities\FileShare;
use Workdo\FileSharing\Entities\FileSharingVerification;
use Workdo\FileSharing\Events\FileTrashDelete;
use Workdo\FileSharing\Events\FileTrashRestore;

class FileTrashController extends Controller
{
    use SoftDeletes;

    public function index(FileTrashDatatable $dataTable)
    {
        if (Auth::user()->isAbleTo('files manage')) {

            $lastVerification = FileSharingVerification::where('created_by', creatorId())
            ->where('workspace', getActiveWorkSpace())->orderBy('id', 'desc')->first();

            $status = isset($lastVerification) ? (($lastVerification->status == 1 ) ? 1 : 0 ) : 0;

            if ($status == 1) {
                $file_status = FileShare::$statues;
                $users  = User::where('created_by', creatorId())->where('type', '!=', 'company')->where('active_workspace', getActiveWorkSpace())->orWhere('id', Auth::user()->id)->get()->pluck('name', 'id');
                return $dataTable->render('file-sharing::trash.index', compact('status', 'file_status', 'users'));
            } else {
                return view('file-sharing::files.oops');
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function restore($id)
    {
        if (Auth::user()->isAbleTo('files trash-restore')) {
            $id         = Crypt::decrypt($id);
            $trashFile  = FileShare::withTrashed()->find($id);
            if (!$trashFile) {
                return redirect()->back()->with('error', __('The file not found.'));
            }
            event(new FileTrashRestore($trashFile));
            $trashFile->restore();
            $trashFile->file_status = "Available";
            $trashFile->update();

            return redirect()->back()->with('success', __('The file has been restored'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('files trash-delete')) {
            $id         = Crypt::decrypt($id);
            $trashFile  = FileShare::withTrashed()->find($id);

            if (!$trashFile) {
                return redirect()->back()->with('error', __('The file not found.'));
            }
            $currentWorkspace = getActiveWorkSpace();
            if ($trashFile->created_by == creatorId() && $trashFile->workspace == $currentWorkspace) {

                if (!empty($trashFile->file_path)) {
                    delete_file($trashFile->file_path);
                }
                event(new FileTrashDelete($trashFile));
                $trashFile->forceDelete();
                return redirect()->back()->with('success', __('The file has been deleted'));
            } else {
                return redirect()->back()->with('error', 'Permission denied.');
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
