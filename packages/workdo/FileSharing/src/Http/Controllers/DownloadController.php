<?php

namespace Workdo\FileSharing\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\FileSharing\DataTables\DownloadDatatable;
use Workdo\FileSharing\Entities\FileDownload;
use Workdo\FileSharing\Entities\FileSharingVerification;

class DownloadController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */

    public function index(DownloadDatatable $dataTable)
    {
        if (Auth::user()->isAbleTo('downloads manage')) {

            $lastVerification = FileSharingVerification::where('created_by', creatorId())
            ->where('workspace', getActiveWorkSpace())->orderBy('id', 'desc')->first();

            $status = isset($lastVerification) ? (($lastVerification->status == 1 ) ? 1 : 0 ) : 0;

            if ($status == 1) {
                return $dataTable->render('file-sharing::download.download', compact('status'));
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
        return view('file-sharing::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        if (Auth::user()->isAbleTo('downloads show')) {

            $files_log = FileDownload::find($id);

            if ($files_log) {
                return view('file-sharing::download.show', compact('files_log'));
            } else {
                return redirect()->back()->with('error', __('Data not found.'));
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
        return view('file-sharing::edit');
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
        //
    }
}
