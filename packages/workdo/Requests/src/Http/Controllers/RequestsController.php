<?php

namespace Workdo\Requests\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Workdo\Requests\Entities\Requests;
use Workdo\LandingPage\Entities\LandingPageSetting;
use Workdo\Requests\Entities\RequestCategory;
use Workdo\Requests\Entities\RequestSubcategory;
use Workdo\Requests\Entities\RequestConvertData;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Workdo\Lead\Entities\Pipeline;
use Workdo\Requests\Events\CreateRequests;
use Workdo\Requests\Events\UpdateRequests;
use Workdo\Requests\Events\DestroyRequests;
use Workdo\Requests\DataTables\RequestDatatable;

class RequestsController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(RequestDatatable $dataTable)
    {
        if (Auth::user()->isAbleTo('Requests manage')) {

        $requestscategory = RequestCategory::where('workspace', getActiveWorkSpace())->where('created_by',creatorId())->get()->pluck('name','id');
        $requestsubcategory = RequestSubcategory::where('workspace', getActiveWorkSpace())->where('created_by',creatorId())->get()->pluck('name','id');

        return $dataTable->render('requests::requests.index', compact('requestscategory','requestsubcategory'));

        } else {
            return redirect()
                ->back()
                ->with('error', __('Permission denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        if (Auth::user()->isAbleTo('Requests create')) {
        $themeOne = Requests::themeOne();
        $module_type = Requests::$module_type;

        $requestscategory = RequestCategory::where('workspace', getActiveWorkSpace())->where('created_by',creatorId())->get()->pluck('name','id');
        $requestssubcategory = RequestSubcategory::where('workspace', getActiveWorkSpace())->where('created_by',creatorId())->get()->pluck('name','id');

        return view('requests::requests.create' ,compact('requestssubcategory','requestscategory','themeOne','module_type'));
         } else {
            return redirect()
                ->back()
                ->with('error', __('Permission denied.'));
        }
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
       if (Auth::user()->isAbleTo('Requests create')) {
       $Requests                = new Requests();
       $Requests->name          = $request->name;
       $Requests->code          = uniqid() . time();
       $Requests->category_id   = $request->category;
       $Requests->active        = $request->active;
       $Requests->subcategory_id= $request->subcategory;
       $Requests->layouts       = $request->layouts;
       $Requests->theme_color   = $request->theme_color;
       $Requests->module_type   = $request->module_type;
       $Requests->created_by    = creatorId();
       $Requests->workspace    = getActiveWorkSpace();
       $Requests->save();
       event(new CreateRequests($request,$Requests));

       return redirect()->back()->with(['success'=> 'The Requests has been created successfully']);
        } else {
            return redirect()
                ->back()
                ->with('error', __('Permission denied.'));
        }

    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if (Auth::user()->isAbleTo('Requests edit')) {
        $themeOne = Requests::themeOne();
        $module_type = Requests::$module_type;

        $requestscategory = RequestCategory::get()->pluck('name','id');
        $requestssubcategory = RequestSubcategory::get()->pluck('name','id');
        $Requests = Requests::find($id);
        if($Requests){
            return view('requests::requests.edit',compact('Requests','requestscategory','requestssubcategory','themeOne','module_type'));
        }else{
            return redirect()
                ->back()
                ->with('error', __('Requests not found'));
        }
         } else {
            return redirect()
                ->back()
                ->with('error', __('Permission denied.'));
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

        if (Auth::user()->isAbleTo('Requests edit')) {
            $Requests                = Requests::find($id);
            if($Requests){
                $Requests->name          = $request->name;
                $Requests->category_id   = $request->category;
                $Requests->active        = $request->active;
                $Requests->subcategory_id= $request->subcategory;
                $Requests->layouts       = $request->layouts;
                $Requests->theme_color   = $request->theme_color;
                $Requests->module_type   = $request->module_type;
                $Requests->save();
                event(new UpdateRequests($request,$Requests));
                return redirect()->back()->with(['success'=> 'The requests details are updated successfully']);
            }else{
                return redirect()
                    ->back()
                    ->with('error', __('Requests not found'));
            }
        } else {
            return redirect()
                ->back()
                ->with('error', __('Permission denied.'));
        }

    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        if (Auth::user()->isAbleTo('Requests delete')) {

        $Requests = Requests::find($id);
            if($Requests){
                event(new DestroyRequests($Requests));
                $Requests->delete();
            return redirect()->back()->with(['success'=> 'The requests has been deleted']);
            }else{
                return redirect()
                    ->back()
                    ->with('error', __('Requests not found'));
            }
         } else {
            return redirect()
                ->back()
                ->with('error', __('Permission denied.'));
        }

    }


    public function request_category(Request $request){

        $requestsubscategory  = RequestSubcategory::where('category_id',$request->category)->where('workspace', getActiveWorkSpace())->where('created_by',creatorId())->get();
        return $requestsubscategory;

    }

    public function requestFieldBind(Request $request ,$id){

        $form               = Requests::find($id);
        $requestConvertData = RequestConvertData::where('request_id' ,$id)->where('workspace', getActiveWorkSpace())->where('created_by',creatorId())->first();
        $response_data      = [];
        if(!empty($requestConvertData)){
            $response_data =json_decode($requestConvertData->response_data);
        }
        $getActiveWorkSpace = getActiveWorkSpace();
        $creatorId          = creatorId();
        $fields             = $form->RequestFormField->pluck('name', 'id');
        $users              = User::where('workspace_id',$getActiveWorkSpace)->emp()->get()->pluck('name', 'id');
        $pipelines          = Pipeline::where('created_by', '=', $creatorId)->where('workspace_id',$getActiveWorkSpace)->get()->pluck('name', 'id');
        return view('requests::requests.form_field', compact('fields','form','users','pipelines','response_data'));

    }

    public function requestbindStore(Request $request ){
      // Prepare response data
            if(!empty($request->request_id)){
                $form               = Requests::find($request->request_id);
                $form->is_converted = $request->is_converted;
                $form->save();
            }
            $response_data_array = [
                'subject_id' => $request->subject_id ?? '',
                'name_id' => $request->name_id ?? '',
                'email_id' => $request->email_id ?? '',
                'user_id' => $request->user_id ?? '',
                'title_id' => $request->title_id ?? '',
                'description_id' => $request->description_id ?? '',
                'pipeline_id' => $request->pipeline_id ?? ''
            ];

            $response_data_json = json_encode($response_data_array, true);

            $request_id = $request->request_id;
            $workspace = getActiveWorkSpace();
            $created_by = creatorId();

            RequestConvertData::updateOrCreate(
                ['request_id' => $request_id], // Attributes to search for
                [
                    'response_data' => $response_data_json,
                    'workspace' => $workspace,
                    'created_by' => $created_by
                ]
            );


        return redirect()->back()->with(['success'=> 'Setting update successfully']);


    }

}
