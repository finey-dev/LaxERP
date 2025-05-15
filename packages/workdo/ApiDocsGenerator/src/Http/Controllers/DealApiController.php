<?php

namespace Workdo\ApiDocsGenerator\Http\Controllers;

use App\Models\WorkSpace;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Workdo\Lead\Entities\Deal;
use Workdo\Lead\Entities\DealTask;
use Workdo\Lead\Entities\Pipeline;
use Workdo\Lead\Entities\User as EntitiesUser;

class DealApiController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        if (module_is_active('Lead')) {
            $usr = Auth::user();

            if ($usr->default_pipeline) {
                $pipeline = Pipeline::where('created_by', '=', creatorId())->where('workspace_id', $request->workspace_id)->where('id', '=', $usr->default_pipeline)->first();
                if (!$pipeline) {
                    $pipeline = Pipeline::where('created_by', '=', creatorId())->where('workspace_id', $request->workspace_id)->first();
                }
            } else {
                $pipeline = Pipeline::where('created_by', '=', creatorId())->where('workspace_id', $request->workspace_id)->first();
            }
            $stages = $pipeline->dealStages->map(function($stage){
                $deals = $stage->deals()->map(function($deal){
                    return [
                        'id'            => $deal->id,
                        'name'          => $deal->name,
                        'price'         => currency_format_with_sym($deal->price),
                        'sources'       => count($deal->sources()),
                        'products'      => count($deal->products()),
                        'status'        => $deal->status,
                        'order'         => $deal->order,
                        'phone'         => $deal->phone,
                        'tasks'         => count($deal->tasks).'/'.count($deal->complete_tasks)
                    ];
                });
                return [
                    'id'                => $stage->id,
                    'name'              => $stage->name,
                    'order'             => $stage->order,
                    'deals' =>$deals
                ];
            });

            $pipeline_detail = [
                'id'  =>$pipeline->id,
                'name'  =>$pipeline->name,
                'stages'    =>$stages
            ];
            $data = [];
            $data['pipeline'] = $pipeline_detail;
            return response()->json(['status'=>'success',"data"=>$data],200);
        }
        else{
            return response()->json(['status'=>'error','message'=>__('CRM Module Not Found!')],404);
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
    public function show(Request $request, $id)
    {
        if (module_is_active('Lead')) {
            // $workspace = WorkSpace::where('slug',$slug)->first();
            $workspace = WorkSpace::find($request->workspace_id);
            if(!$workspace){
                return response()->json(['status'=>"error","message"=>"Workspace Not Found!"],404);
            }
            $deal = Deal::where('workspace_id',$request->workspace_id)->where('created_by', creatorId())->where('id', $id)->first();
            if($deal){
                if ($deal->is_active) {
                    if(count( $deal->sources()) > 0){
                        $sources = $deal->sources()->map(function($source){
                            return [
                                'name'  => $source->name
                            ];
                        });
                    }
                    else{
                        $sources = [];
                    }

                    if(count($deal->products()) > 0){
                        $products = $deal->products()->map(function($product){
                           return [
                            'name'              => $product->name,
                            'sku'               => $product->sku,
                            'sale_price'        => currency_format_with_sym($product->sale_price),
                            'purchase_price'    => currency_format_with_sym($product->purchase_price),
                           ];
                        });
                    }
                    else{
                        $products = [];
                    }
                    $deal_detail = [
                        'id'                        => $deal->id,
                        'name'                      => $deal->name,
                        'price'                     => currency_format_with_sym($deal->price),
                        'status'                    => $deal->status,
                        'order'                     => $deal->order,
                        'phone'                     => $deal->phone,
                        'is_active'                 => $deal->is_active,
                        'pipeline'                  => $deal->pipeline->name,
                        'stage'                     => $deal->stage->name,
                        'tasks'                     => $deal->tasks->map(function($task){
                            return [
                                'name'          => $task->name,
                                'date'          => $task->date,
                                'time'          => $task->time,
                                'priority'      => DealTask::$priorities[$task->priority],
                                'status'        => DealTask::$status[$task->status]
                            ];
                        }),
                        'products'                  => $products,
                        'sources'                   => $sources,
                        'calls'                     => $deal->calls->map(function($call){
                            return [
                                'subject'         => $call->subject,
                                'call_type'       => $call->call_type,
                                'duration'        => $call->duration,
                                'description'     => $call->description,
                                'user'            => !empty($call->getDealCallUser->name)?$call->getDealCallUser->name:''

                            ];
                        }),
                        'emails'                    => $deal->emails->map(function($email){
                            return [
                                'to'            => $email->to,
                                'subject'       => $email->subject,
                                'description'   => $email->description,
                            ];
                        }),
                        'discussion'        => $deal->discussions->map(function($discussion){
                            return [
                                'comment'       => $discussion->comment,
                                'user_name'          => $discussion->user->name,
                                'user_type'          => $discussion->user->type,
                                'user_avatar'        => get_file($discussion->user->avatar),

                            ];
                        }),
                        'files'              => $deal->files->map(function($file){
                            return [
                                'file_path' => get_file($file->file_path)
                            ];
                        })
                    ];
                    $data = [];
                    $data['deal']    = $deal_detail;
                    return response()->json(['status'=>'success','message'=>$data],200);
                } else {
                    return response()->json(['status'=>'error','message'=> __('This Deal Is Not Active!')]);
                }
            }
            else{
                return response()->json(['status'=>'error', 'message'=>__('Deal Not Found!')],404);
            }
        }
        else{
            return response()->json(['status'=>'error','message'=>__('CRM Module Not Found!')],404);
        }
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
