<?php

namespace Workdo\FileSharing\Http\Controllers;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Workdo\FileSharing\DataTables\FileDatatable;
use Workdo\FileSharing\Emails\SendFileshare;
use Workdo\FileSharing\Entities\FileDownload;
use Workdo\FileSharing\Entities\FileShare;
use Workdo\FileSharing\Entities\FileSharingVerification;
use Workdo\FileSharing\Events\CreateFile;
use Workdo\FileSharing\Events\DestroyFile;
use Workdo\FileSharing\Events\UpdateFile;

class FilesController extends Controller
{
    use SoftDeletes;
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(FileDatatable $dataTable)
    {
        if (Auth::user()->isAbleTo('files manage')) {

            $lastVerification = FileSharingVerification::where('created_by', creatorId())
            ->where('workspace', getActiveWorkSpace())->orderBy('id', 'desc')->first();

            $status = isset($lastVerification) ? (($lastVerification->status == 1 ) ? 1 : 0 ) : 0;

            if ($status == 1) {
                $file_status = FileShare::$statues;
                $filesharing_type = FileShare::$share_mode;
                $users  = User::where('created_by', creatorId())->where('type', '!=', 'company')->where('active_workspace', getActiveWorkSpace())->orWhere('id', Auth::user()->id)->get()->pluck('name', 'id');
                return $dataTable->render('file-sharing::files.index', compact('status', 'file_status', 'filesharing_type', 'users'));
            } else {
                return view('file-sharing::files.oops');
            }
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
        if (Auth::user()->isAbleTo('files create')) {

            $users  = User::where('created_by', creatorId())->where('type', '!=', 'company')->where('active_workspace', getActiveWorkSpace())->orWhere('id', Auth::user()->id)->get();

            return view('file-sharing::files.create', compact('users'));
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

        if (Auth::user()->isAbleTo('files create')) {

            $rules = [
                'attachment'    => 'required',
                'type'          => 'required',
                'description'   => 'required',
                'users_list'    => 'required',
            ];

            $validator = \Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->route('files.index')->with('error', $messages->first());
            }

            $users = [];
            if ($request->users_list) {
                $users = User::whereIn('email', $request->users_list)->where('created_by', creatorId())->where('type', '!=', 'company')->where('active_workspace', getActiveWorkSpace())->get()->pluck('id')->toArray();
            }


            if ($request->hasFile('attachment')) {
                $filenameWithExt = $request->file('attachment')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('attachment')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $uplaod = upload_file($request, 'attachment', $fileNameToStore, 'filesshare');

                if (isset($uplaod['flag']) && $uplaod['flag'] == 1 && isset($uplaod['url'])) {
                    $fileSizeInBytes = filesize($uplaod['url']);
                    $fileSizeInKB = round($fileSizeInBytes / 1024, 2);

                    if ($fileSizeInKB < 1024) {
                        $fileSizeFormatted = $fileSizeInKB . " KB";
                    } else {
                        $fileSizeInMB = round($fileSizeInKB / 1024, 2);
                        $fileSizeFormatted = $fileSizeInMB . " MB";
                    }

                    $url = $uplaod['url'];
                } else {
                    // Handle the case where the upload failed or 'url' key is not present
                    return redirect()->back()->with('error', isset($uplaod['msg']) ? $uplaod['msg'] : 'File upload failed');
                }
            }

            $file                   = new FileShare();
            $file->file_name        = isset($filenameWithExt) ? $filenameWithExt : '';
            $file->file_path        = !empty($request->attachment) ? $url : '';
            $file->file_size        = $fileSizeFormatted;
            $file->filesharing_type = $request->type;
            $file->email            = $request->email;
            $file->auto_destroy     = !empty($request->auto_destroy) ? $request->auto_destroy : 'off';
            $file->password         = !empty($request->password) ? \Hash::make($request->password) : null;
            $file->user_id          = implode(',', $users);
            $file->description      = $request->description;
            $file->workspace        = getActiveWorkSpace();
            $file->created_by       = creatorId();
            $file->is_pass_enable   = 0;

            if (!empty($request->password_switch) && $request->password_switch == 'on') {
                $file->is_pass_enable   = 1;

                $validator = \Validator::make(
                    $request->all(),
                    ['password' => 'required|min:6']
                );

                if ($validator->fails()) {
                    return redirect()->back()->with('error', $validator->errors()->first());
                }
            }

            $file->save();
            // Send mail to User

            try {
                $setconfing =  SetConfigEmail();

                if ($setconfing ==  true) {
                    try {

                        Mail::to($file->email)->send(new SendFileshare($file));
                    } catch (\Throwable $th) {
                        $smtp_error['status']   = false;
                        $smtp_error['msg']      = $th->getMessage();
                    }
                } else {
                    $smtp_error['status']   = false;
                    $smtp_error['msg']      = __('Something went wrong please try again ');
                }
            } catch (\Throwable $th) {
                $smtp_error['status']   = false;
                $smtp_error['msg']      = $th->getMessage();
            }
            event(new CreateFile($request, $file));
            return redirect()->back()->with('success', __('The file has been created successfully'));
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
        return redirect()->back()->with('error', __('Permission denied.'));

        return view('file-sharing::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if (Auth::user()->isAbleTo('files edit')) {
            $file = FileShare::find($id);
            $users = User::where('created_by', '=', creatorId())->where('workspace_id', getActiveWorkSpace())->get()->pluck('name', 'id');
            $users->prepend(__('Select Users'), '');
            return view('file-sharing::files.edit', compact('file', 'users'));
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
        if (Auth::user()->isAbleTo('files edit')) {
            $file = FileShare::find($id);

            $rules = [

                'type' => 'required',
            ];
            $validator = \Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->route('files.index')->with('error', $messages->first());
            }

            if (!file_exists($file->file_path)) {
                $rules = [
                    'attachment' => 'required',
                ];
                $validator = \Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();
                    return redirect()->route('files.index')->with('error', $messages->first());
                }
            }

            if ($request->attachment) {
                // old file delete

                if (!empty($request->attachment)) {
                    delete_file($request->attachment);
                }
                $filenameWithExt = $request->file('attachment')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('attachment')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;


                $uplaod = upload_file($request, 'attachment', $fileNameToStore, 'filesshare');
                if (isset($uplaod['flag']) && $uplaod['flag'] == 1 && isset($uplaod['url'])) {
                    $fileSizeInBytes = filesize($uplaod['url']);
                    $fileSizeInKB = round($fileSizeInBytes / 1024, 2);

                    if ($fileSizeInKB < 1024) {
                        $fileSizeFormatted = $fileSizeInKB . " KB";
                    } else {
                        $fileSizeInMB = round($fileSizeInKB / 1024, 2);
                        $fileSizeFormatted = $fileSizeInMB . " MB";
                    }

                    $url = $uplaod['url'];

                    $fileSizeInBytes = File::size($uplaod['url']);

                    $fileSizeInKB = round($fileSizeInBytes / 1024, 2);

                    if ($fileSizeInKB < 1024) {
                        $fileSizeFormatted = $fileSizeInKB . " KB";
                        $file->file_size            =  $fileSizeFormatted;
                    } else {
                        $fileSizeInMB = round($fileSizeInKB / 1024, 2);
                        $fileSizeFormatted = $fileSizeInKB . " MB";
                        $file->file_size            =  $fileSizeFormatted;
                    }
                    if (!empty($file->file_path)) {
                        delete_file($file->file_path);
                    }
                    $url = $uplaod['url'];
                    $file->file_path            =  $url;
                    $file->file_name            = isset($filenameWithExt) ? $filenameWithExt : '';
                } else {

                    return redirect()->back()->with('error', isset($uplaod['msg']) ? $uplaod['msg'] : 'File upload failed');
                }
            }
            $file->filesharing_type     = $request->type;
            $file->email                = $request->email;
            $file->auto_destroy         = !empty($request->auto_destroy) ? $request->auto_destroy : 'off';
            $file->password             = !empty($request->password) ? \Hash::make($request->password) : null;
            $file->user_id              = implode(",", $request->users_list);
            $file->description          = $request->description;
            $file->workspace            = getActiveWorkSpace();
            $file->created_by           = creatorId();
            $file->is_pass_enable       = 0;

            if (!empty($request->password_switch) && $request->password_switch == 'on') {
                $file->is_pass_enable   = 1;

                $validator = \Validator::make(
                    $request->all(),
                    ['password' => 'required|min:6']
                );

                if ($validator->fails()) {
                    return redirect()->back()->with('error', $validator->errors()->first());
                }
            }
            $file->save();

            try {
                $setconfing =  SetConfigEmail();

                if ($setconfing ==  true) {
                    try {

                        Mail::to($file->email)->send(new SendFileshare($file));
                    } catch (\Throwable $th) {
                        $smtp_error['status'] = false;
                        $smtp_error['msg'] = $th->getMessage();
                    }
                } else {
                    $smtp_error['status'] = false;
                    $smtp_error['msg'] = __('Something went wrong please try again ');
                }
            } catch (\Throwable $th) {
                $smtp_error['status'] = false;
                $smtp_error['msg'] = $th->getMessage();
            }
            event(new UpdateFile($request, $file));
            return redirect()->back()->with('success', __('The file details are updated successfully'));
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
        if (Auth::user()->isAbleTo('files delete')) {

            $currentWorkspace = getActiveWorkSpace();
            $file = FileShare::find($id);
            if ($file->created_by == creatorId() && $file->workspace == $currentWorkspace) {

                event(new DestroyFile($file));
                $file->delete();
                return redirect()->back()->with('success', __('The file has been moved to trash'));
            } else {
                return redirect()->back()->with('error', 'Permission denied.');
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function PasswordCheck(Request $request, $id = null, $lang = null)
    {
        $id_de = Crypt::decrypt($id);
        $file = FileShare::find($id_de);
        \App::setLocale($lang);
        $hashedPassword = $file->password;
        $userInputPassword = $request->password;
        if (Hash::check($userInputPassword, $hashedPassword)) {
            \Session::put('checked_' . $file->id, $file->id);
            $file = FileShare::find($id_de);
            $company_id = $file->created_by;
            $workspace_id = $file->workspace;
            return view('file-sharing::files.sharedlink', compact('id', 'lang', 'company_id', 'workspace_id', 'file'));
        } else {
            return redirect()->route('file.shared.link', [$id, $lang])->with('error', __('Password is wrong! Please enter the valid password'));
        }
    }

    public function FileSharedLink($id = null, $lang = null)
    {
        try {
            $id_de = Crypt::decrypt($id);
        } catch (\Throwable $th) {
            return redirect()->route('login', [$lang]);
        }
        $file = FileShare::find($id_de);
        if (!$file) {
            return redirect()->route('login')->with('error', __('File not found please contact to admin.'));
        }
        $company_id = $file->created_by;
        $workspace_id = $file->workspace;
        $file_id = \Session::get('checked_' . $id_de);
        if ($lang == '') {
            $company_settings = getCompanyAllSetting($company_id);
            $lang = isset($company_settings['defult_language']) ? $company_settings['defult_language'] : 'en';
        }
        \App::setLocale($lang);
        getActiveLanguage();
        if (!empty($file->is_pass_enable)  == 1) {
            return view('file-sharing::files.password_check', compact('company_id', 'workspace_id', 'id', 'lang'));
        }
        if ($file) {
            $file_share = $file->file_path;
            if($file->auto_destroy == 'on'){
                $file->delete();
            }

            return view('file-sharing::files.sharedlink', compact('file_share', 'company_id', 'workspace_id', 'file', 'lang', 'company_settings'));
        } else {
            return redirect()->route('login')->with('error', __('File not found please contact to admin.'));
        }
    }

    public function download(Request $request, $file)
    {
        $file_id = FileShare::find($file);

        if ($file_id->auto_destroy == 'on' && $file_id->file_status == 'Available') {
            $file_id->file_status = 'Not Available';

            $file_id->save();
        }

        $ip = $_SERVER['REMOTE_ADDR'];
        // $ip = '49.36.83.79'; // This is static ip address
        $whichbrowser = new \WhichBrowser\Parser($_SERVER['HTTP_USER_AGENT']);
        if ($whichbrowser->device->type == 'bot') {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        $referrer = isset($_SERVER['HTTP_REFERER']) ? parse_url($_SERVER['HTTP_REFERER']) : null;
        $query = @unserialize(file_get_contents('http://ip-api.com/php/' . $ip));
        if (isset($query['status']) && $query['status'] == 'success') {

            $query['browser_name'] = $whichbrowser->browser->name ?? null;
            $query['os_name'] = $whichbrowser->os->name ?? null;
            $query['browser_language'] = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? mb_substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : null;
            $query['device_type'] = GetDeviceType($_SERVER['HTTP_USER_AGENT']);
            $query['referrer_host'] = !empty($referrer['host']);
            $query['referrer_path'] = !empty($referrer['path']);
            $json = json_encode($query);

            $download  = FileDownload::create([

                'file_id'    => $file,
                'file_path'  => $file_id->file_path,
                'ip_address' => $ip,
                'details'    => $json,
                'date'       => date('Y-m-d H:i:s'),
                'workspace'  => $file_id->workspace,
                'created_by' => $file_id->created_by,
            ]);
        }

        return redirect()->back()->with('success', __('The file has been download successfully'));
    }

    public function grid(Request $request)
    {
        $lastVerification = FileSharingVerification::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->orderBy('id', 'desc')->first();

        $status = isset($lastVerification) ? (($lastVerification->status == 1 ) ? 1 : 0 ) : 0;

        if ($status == 1) {

            $file_status = FileShare::$statues;
            $filesharing_type = FileShare::$share_mode;
            $users  = User::where('created_by', creatorId())->where('type', '!=', 'company')->where('active_workspace', getActiveWorkSpace())->orWhere('id', Auth::user()->id)->get()->pluck('name', 'id');

            $files = FileShare::where('created_by', creatorId())->where('workspace', getActiveWorkSpace())->orderBy('id', 'desc');

            if (!empty($request->file_status)) {
                $files->where('file_status', '=', $request->file_status);
            }
            if (!empty($request->filesharing_type)) {
                $files->where('filesharing_type', '=', $request->filesharing_type);
            }
            if (!empty($request->user)) {
                $files->whereRaw('FIND_IN_SET(?, user_id)', [$request->user]);
            }

            $files = $files->paginate(11);

            return view('file-sharing::files.grid', compact('files', 'status', 'file_status', 'filesharing_type', 'users'));
        } else {
            return view('file-sharing::files.oops');
        }
    }
}
