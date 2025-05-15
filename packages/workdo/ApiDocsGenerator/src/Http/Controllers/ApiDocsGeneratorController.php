<?php

namespace Workdo\ApiDocsGenerator\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ApiDocsGeneratorController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
    */
    public function index()
    {
        if (Auth::user()->isAbleTo('api manage'))
        {
            $path = base_path("packages/workdo/ApiDocsGenerator/src/documentation/");
            $fileNames = [];
            $files = File::files($path);

            foreach ($files as $file) {
                $fileNames[] = pathinfo($file, PATHINFO_FILENAME);
            }
            $contents = [];
            foreach($fileNames as $filename){
                $contents[$filename] = json_decode(file_get_contents($path.$filename.'.json'));
            }
            return view('api-docs-generator::index',compact('contents','fileNames'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('api-docs-generator::create');
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
        return view('api-docs-generator::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('api-docs-generator::edit');
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
