<?php

namespace Workdo\Requests\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Workdo\Requests\Entities\Requests;
use Workdo\Requests\Entities\RequestResponse;
use Workdo\Requests\Events\CreateRequestResponse;
use Workdo\Requests\Events\DestroyRequestResponse;
use Workdo\Requests\Entities\RequestFormField;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Workdo\Lead\Entities\UserLead;

class RequestResponseController extends Controller
{



    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
      //
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('requests::edit');
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
        if (Auth::user()->isAbleTo('Requests response delete')) {
            $response = RequestResponse::find($id);
            if($response){
                event(new DestroyRequestResponse($response));
                $response->delete();
                return redirect()->back()->with('success', __('The response has been deleted'));
            }
            else {
                return redirect()
                    ->back()
                    ->with('error', __('Response not found.'));
            }
        } else {
            return redirect()
                ->back()
                ->with('error', __('Permission denied.'));
        }

    }


    public function requests_response_show($id){
        if (Auth::user()->isAbleTo('Requests response manage')) {
            $form = Requests::find($id);
            if($form){
                return view('requests::request_response.index',compact('form'));
            }else {
                return redirect()
                    ->back()
                    ->with('error', __('Response not found.'));
            }
        } else {
            return redirect()
                ->back()
                ->with('error', __('Permission denied.'));
        }
    }


    public function form_show($code)
    {
        if(!empty($code))
        {
            try {
                $code = $code;
            } catch (\Throwable $th) {
                return redirect('login');
            }
            $form = Requests::where('code', 'LIKE', $code)->first();
            if(!empty($form))
            {
                $company_settings = getCompanyAllSetting($form->created_by, $form->workspace);
                if($form->active == 'on')
                {
                    $objFields = $form->RequestFormField;

                    return view('requests::theme.' . $form->layouts . '.form', compact('objFields', 'code', 'form'));
                }
                else
                {
                    return view('requests::theme.' . $form->layouts . '.form', compact('code', 'form'));
                }
            }
            else
            {
                return redirect()->route('login')->with('error', __('Form not found please contact to admin.'));
            }
        }
        else
        {
            return redirect()->route('login')->with('error', __('Permission Denied.'));
        }
    }


    public function post_response(Request $request , $code)
    {

        $form       = Requests::where('code', 'LIKE', $code)->first();

        $arrFieldResp = [];
        foreach($request->field as $key => $value)
        {
            $arrFieldResp[RequestFormField::find($key)->name] = (!empty($value)) ? $value : '-';
        }
        // response store
       $RequestResponse = RequestResponse::create(
            [
                'request_id' => $form->id,
                'response' => json_encode($arrFieldResp),
            ]
        );
        event(new CreateRequestResponse($request,$RequestResponse));

        if($form->is_converted == 1 && module_is_active('Lead')){
        $formBuilderModuleData  = $form->fieldResponse;
        $objField               = json_decode($formBuilderModuleData->response_data);
        $usr                    = User::find($form->created_by);
        $stage = \Workdo\Lead\Entities\LeadStage::where('pipeline_id', '=', $objField->pipeline_id)->first();

            if(!empty($stage))
            {
                $post              = new \Workdo\Lead\Entities\Lead();
                $post->name        = $request->field[$objField->name_id] ?? '';
                $post->email       = $request->field[$objField->email_id] ?? '';
                $post->subject     = $request->field[$objField->subject_id] ?? '';
                $post->user_id     = $objField->user_id ?? '';
                $post->pipeline_id = $objField->pipeline_id ?? '';
                $post->stage_id    = $stage->id ?? '';
                $post->created_by  = $usr->id ??  '';
                $post->date        = date('Y-m-d');
                $post->workspace_id= $usr->active_workspace;
                $post->save();


                $usrLeads = [
                    $usr->id,
                    $objField->user_id,
                ];

                foreach($usrLeads as $usrLead)
                {
                    UserLead::create(
                        [
                            'user_id' => $usrLead,
                            'lead_id' => $post->id,
                        ]
                    );
                }

            }
        }

        $msg = __('Response suceessfully saved!');
        return redirect()->back()->with(['msg' => $msg]);
    }

    public function responseDetail($response_id)
    {
        if (Auth::user()->isAbleTo('Requests response show')) {

            $formResponse = RequestResponse::find($response_id);
            if($formResponse){

                $form         = Requests::find($formResponse->request_id);
                if($form->created_by == creatorId())
                {
                    $response = json_decode($formResponse->response, true);

                    return view('requests::request_response.response_detail', compact('response'));
                }
                else
                {
                    return response()->json(['error' => __('Permission Denied . ')], 401);
                }
            }else{
                return redirect()->back()->with('error', __('Response not found.'));

            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
