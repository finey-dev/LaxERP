<?php

namespace Workdo\ApiDocsGenerator\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Workdo\Lead\Entities\ClientDeal;
use Workdo\Lead\Entities\Deal;
use Workdo\Lead\Entities\DealDiscussion;
use Workdo\Lead\Entities\DealFile;
use Workdo\Lead\Entities\DealStage;
use Workdo\Lead\Entities\DealTask;
use Workdo\Lead\Entities\Label;
use Workdo\Lead\Entities\Lead;
use Workdo\Lead\Entities\LeadStage;
use Workdo\Lead\Entities\Pipeline;
use Workdo\Lead\Entities\Source;
use Workdo\Lead\Entities\UserDeal;
use Workdo\Lead\Events\CreateDealStage;
use Workdo\Lead\Events\CreateLabel;
use Workdo\Lead\Events\CreateLeadStage;
use Workdo\Lead\Events\CreatePipeline;
use Workdo\Lead\Events\CreateSource;
use Workdo\Lead\Events\DestroyDealStage;
use Workdo\Lead\Events\DestroyLabel;
use Workdo\Lead\Events\DestroyLeadStage;
use Workdo\Lead\Events\DestroyPipeline;
use Workdo\Lead\Events\DestroySource;
use Workdo\Lead\Events\UpdateDealStage;
use Workdo\Lead\Events\UpdateLabel;
use Workdo\Lead\Events\UpdateLeadStage;
use Workdo\Lead\Events\UpdatePipeline;
use Workdo\Lead\Events\UpdateSource;
use Workdo\Taskly\Entities\ActivityLog;

class CrmController extends Controller
{
    public function pipelinesList(Request $request)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'Lead Module Not Active!']);
        }

        $pipelines = Pipeline::where('created_by', '=', creatorId())
                                ->where('workspace_id', '=', $request->workspace_id)
                                ->get()
                                ->map(function($pipeline){
                                    return [
                                        'id'    => $pipeline->id,
                                        'name'    => $pipeline->name
                                    ];
                                });

        return response()->json(['status'=>'success','data' => $pipelines]);
    }

    public function pipelineCreate(Request $request)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'Lead Module Not Active!']);
        }
        $validator = \Validator::make(
            $request->all(), [
                'name' => 'required|max:20',
            ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();
            return response()->json(['status' => 'error','message' => $messages->first()]);
        }

        $pipeline               = new Pipeline();
        $pipeline->name         = $request->name;
        $pipeline->created_by   = creatorId();
        $pipeline->workspace_id = $request->workspace_id;
        $pipeline->save();

        event(new CreatePipeline($request,$pipeline));

        return response()->json(['status'=>'success','message' => 'Pipeline Created Successfully!']);
    }

    public function pipelineUpdate(Request $request,$id)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'Lead Module Not Active!']);
        }

        $validator = \Validator::make(
            $request->all(), [
                'name' => 'required|max:20',
            ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();
            return response()->json(['status' => 'error','message' => $messages->first()]);
        }

        $pipeline = Pipeline::where('id',$id)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$pipeline){
            return response()->json(['status' => 'error','message' => 'Pipeline Not Found!']);
        }

        $pipeline->name         = $request->name;
        $pipeline->save();

        event(new UpdatePipeline($request,$pipeline));

        return response()->json(['status'=>'success','message'=>'Pipeline Updated Successfully!']);
    }

    public function pipelineDelete(Request $request,$id)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'Lead Module Not Active!']);
        }

        $pipeline = Pipeline::where('id',$id)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$pipeline){
            return response()->json(['status' => 'error','message' => 'Pipeline Not Found!']);
        }

        foreach($pipeline->dealStages as $dealStage)
        {
            $deals = Deal::where('pipeline_id', '=', $pipeline->id)->where('stage_id', '=', $dealStage->id)->get();
            foreach($deals as $deal)
            {
                DealDiscussion::where('deal_id', '=', $deal->id)->delete();
                DealFile::where('deal_id', '=', $deal->id)->delete();
                ClientDeal::where('deal_id', '=', $deal->id)->delete();
                UserDeal::where('deal_id', '=', $deal->id)->delete();
                DealTask::where('deal_id', '=', $deal->id)->delete();
                ActivityLog::where('deal_id', '=', $deal->id)->delete();

                $deal->delete();
            }

            $dealStage->delete();
        }

        $pipeline->delete();

        event(new DestroyPipeline($pipeline));

        return response()->json(['status'=>'success','message'=>'Pipeline Deleted Successfully!']);
    }

    public function leadStages(Request $request)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'Lead Module Not Active!']);
        }
        $pipelineWiseLeadStage = [];
        $lead_stages = LeadStage::select('lead_stages.*', 'pipelines.name as pipeline')
                    ->join('pipelines', 'pipelines.id', '=', 'lead_stages.pipeline_id')
                    ->where('pipelines.created_by', '=', creatorId())
                    ->where('lead_stages.created_by', '=', creatorId())
                    ->where('lead_stages.workspace_id', '=', $request->workspace_id)
                    ->orderBy('lead_stages.pipeline_id')
                    ->orderBy('lead_stages.order')
                    ->get()
                    ->map(function($lead_stage) use(&$pipelineWiseLeadStage){

                        // $pipelineWiseLeadStage[$lead_stage->pipeline][] = [
                        //     'id'    => $lead_stage->id,
                        //     'name'  => $lead_stage->name,
                        //     'order' => $lead_stage->order
                        // ];

                        $pipelineId = $lead_stage->pipeline_id;
                        if (!isset($pipelineWiseLeadStage[$pipelineId])) {
                            $pipelineWiseLeadStage[$pipelineId] = [
                                'id' => $pipelineId,
                                'name' => $lead_stage->pipeline,
                                'stages' => []
                            ];
                        }

                        $pipelineWiseLeadStage[$pipelineId]['stages'][] = [
                            'id' => $lead_stage->id,
                            'name' => $lead_stage->name,
                            'order' => $lead_stage->order
                        ];
                        return $pipelineWiseLeadStage;
                    });
                    $pipelineWiseLeadStage = array_values($pipelineWiseLeadStage);

        return response()->json(['status'=>'success','data'=>$pipelineWiseLeadStage]);
    }

    public function leadStageCreate(Request $request)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'Lead Module Not Active!']);
        }

        $pipelines = Pipeline::where('id',$request->pipeline_id)->where('created_by', '=', creatorId())->where('workspace_id', $request->workspace_id)->first();
        if(!$pipelines){
            return response()->json(['status'=>'error','message'=>'Pipeline Not Found!']);
        }

        $validator = \Validator::make(
            $request->all(),
            [
                'name' => 'required|max:20',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return response()->json(['status'=>'error', 'message'=> $messages->first()]);
        }

        $lead_stage                = new LeadStage();
        $lead_stage->name          = $request->name;
        $lead_stage->pipeline_id   = $request->pipeline_id;
        $lead_stage->order         = $request->order;
        $lead_stage->created_by    = creatorId();
        $lead_stage->workspace_id  = $request->workspace_id;
        $lead_stage->save();

        event(new CreateLeadStage($request,$lead_stage));

        return response()->json(['status'=>'success','message'=>'Lead Stage Created Successfully!']);
    }

    public function leadStageUpdate(Request $request, $id)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'Lead Module Not Active!']);
        }

        $pipelines = Pipeline::where('id',$request->pipeline_id)->where('created_by', '=', creatorId())->where('workspace_id', $request->workspace_id)->first();
        if(!$pipelines){
            return response()->json(['status'=>'error','message'=>'Pipeline Not Found!']);
        }

        $validator = \Validator::make(
            $request->all(),
            [
                'name' => 'required|max:20',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return response()->json(['status'=>'error', 'message'=> $messages->first()]);
        }

        $lead_stage = LeadStage::where('id',$id)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$lead_stage){
            return response()->json(['status'=>'error','message'=>'Lead Stage Not Found!']);
        }

        $lead_stage->name          = $request->name;
        $lead_stage->pipeline_id   = $request->pipeline_id;
        $lead_stage->order         = $request->order;
        $lead_stage->save();

        event(new UpdateLeadStage($request,$lead_stage));

        return response()->json(['status'=>'success','message'=>'Lead Stage Updated Successfully!']);

    }

    public function leadStageDelete(Request $request, $id)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'Lead Module Not Active!']);
        }

        $lead_stage = LeadStage::where('id',$id)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$lead_stage){
            return response()->json(['status'=>'error','message'=>'Lead Stage Not Found!']);
        }

        $lead_stage->delete();

        event(new DestroyLeadStage($lead_stage));

        return response()->json(['status'=>'success','message'=>'Lead Stage Deleted Successfully!']);

    }

    public function dealStages(Request $request)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'Lead Module Not Active!']);
        }

        $pipelineWiseDealStage = [];
        $stages  = DealStage::select('deal_stages.*', 'pipelines.name as pipeline')
                ->join('pipelines', 'pipelines.id', '=', 'deal_stages.pipeline_id')
                ->where('pipelines.created_by', '=', creatorId())
                ->where('deal_stages.created_by', '=', creatorId())
                ->orderBy('deal_stages.pipeline_id')->where('deal_stages.workspace_id', getActiveWorkSpace())
                ->orderBy('deal_stages.order')
                ->get()
                ->each(function($stage) use (&$pipelineWiseDealStage){
                    // $pipelineWiseDealStage[$stage->pipeline][] = [
                    //     'id'    => $stage->id,
                    //     'name'    => $stage->name,
                    //     'order'    => $stage->order,
                    // ];

                    $pipelineId = $stage->pipeline_id;
                    if (!isset($pipelineWiseDealStage[$pipelineId])) {
                        $pipelineWiseDealStage[$pipelineId] = [
                            'id' => $pipelineId,
                            'name' => $stage->pipeline,
                            'stages' => []
                        ];
                    }

                    $pipelineWiseDealStage[$pipelineId]['stages'][] = [
                        'id' => $stage->id,
                        'name' => $stage->name,
                        'order' => $stage->order
                    ];
                    return $pipelineWiseDealStage;
                });
                $pipelineWiseDealStage = array_values($pipelineWiseDealStage);

        return response()->json(['status'=>'success','data'=>$pipelineWiseDealStage]);
    }

    public function dealStageCreate(Request $request)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'Lead Module Not Active!']);
        }

        $pipelines = Pipeline::where('id',$request->pipeline_id)->where('created_by', '=', creatorId())->where('workspace_id', $request->workspace_id)->first();
        if(!$pipelines){
            return response()->json(['status'=>'error','message'=>'Pipeline Not Found!']);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|max:20',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        $dealStage               = new DealStage();
        $dealStage->name         = $request->name;
        $dealStage->pipeline_id  = $request->pipeline_id;
        $dealStage->created_by   = creatorId();
        $dealStage->workspace_id = $request->workspace_id;
        $dealStage->save();

        event(new CreateDealStage($request,$dealStage));

        return response()->json(['status'=>'success','message'=>'Deal Stage Successfully Created!']);
    }

    public function dealStageUpdate(Request $request, $id)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'Lead Module Not Active!']);
        }

        $pipelines = Pipeline::where('id',$request->pipeline_id)->where('created_by', '=', creatorId())->where('workspace_id', $request->workspace_id)->first();
        if(!$pipelines){
            return response()->json(['status'=>'error','message'=>'Pipeline Not Found!']);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|max:20',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        $dealStage = DealStage::where('id',$id)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$dealStage){
            return response()->json(['status'=>'error','message'=>'Deal Stage Not Found!']);
        }

        $dealStage->name         = $request->name;
        $dealStage->pipeline_id  = $request->pipeline_id;
        $dealStage->save();

        event(new UpdateDealStage($request,$dealStage));

        return response()->json(['status'=>'success', 'message'=> 'Deal Stage Successfully Updated!']);
    }

    public function dealStageDelete(Request $request, $id)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'Lead Module Not Active!']);
        }

        $dealStage = DealStage::where('id',$id)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$dealStage){
            return response()->json(['status'=>'error','message'=>'Deal Stage Not Found!']);
        }

        $deals = Deal::where('stage_id', '=', $dealStage->id)->count();

        if ($deals == 0) {
            $dealStage->delete();

            event(new DestroyDealStage($dealStage));

            return response()->json(['status'=>'success', 'message'=> 'Deal Stage Successfully Deleted!']);

        } else {
            return response()->json(['status'=>'error', 'message'=>'There are some deals on stage, please remove it first!']);
        }
    }

    public function labelsList(Request $request)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'Lead Module Not Active!']);
        }

        $pipelineWiseLabel = [];
        $labels = Label::select('labels.*', 'pipelines.name as pipeline')
                    ->join('pipelines', 'pipelines.id', '=', 'labels.pipeline_id')
                    ->where('pipelines.created_by', '=', creatorId())
                    ->where('labels.created_by', '=', creatorId())
                    ->where('labels.workspace_id', '=', $request->workspace_id)
                    ->orderBy('labels.pipeline_id')
                    ->get()
                    ->map(function($label) use (&$pipelineWiseLabel){
                        $pipelineWiseLabel[$label->pipeline][] = [
                            'id'    => $label->id,
                            'name'  => $label->name,
                            'color' => $label->color
                        ];
                    });

        return response()->json(['status'=>'success','data'=>$pipelineWiseLabel]);
    }

    public function labelCreate(Request $request)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'Lead Module Not Active!']);
        }

        $pipelines = Pipeline::where('id',$request->pipeline_id)->where('created_by', '=', creatorId())->where('workspace_id', $request->workspace_id)->first();
        if(!$pipelines){
            return response()->json(['status'=>'error','message'=>'Pipeline Not Found!']);
        }

        $validator = Validator::make(
            $request->all(), [
                'name' => 'required|max:20',
                'color' => 'required|in:'.implode(',',Label::$colors),
            ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message'=> $messages->first()]);
        }

        $label                = new Label();
        $label->name          = $request->name;
        $label->color         = $request->color;
        $label->pipeline_id   = $request->pipeline_id;
        $label->created_by    = creatorId();
        $label->workspace_id  = $request->workspace_id;
        $label->save();

        event(new CreateLabel($request,$label));

        return response()->json(['status'=>'success', 'message'=> 'Label Successfully Created!']);
    }

    public function labelUpdate(Request $request, $id)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'Lead Module Not Active!']);
        }

        $pipelines = Pipeline::where('id',$request->pipeline_id)->where('created_by', '=', creatorId())->where('workspace_id', $request->workspace_id)->first();
        if(!$pipelines){
            return response()->json(['status'=>'error','message'=>'Pipeline Not Found!']);
        }

        $validator = Validator::make(
            $request->all(), [
                'name' => 'required|max:20',
                'color' => 'required|in:'.implode(',',Label::$colors),
            ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();
            return response()->json(['status'=>'error', 'message'=> $messages->first()]);
        }

        $label = Label::where('id',$id)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$label){
            return response()->json(['status'=>'error', 'message'=> 'Label Not Found!']);
        }

        $label->name        = $request->name;
        $label->color       = $request->color;
        $label->pipeline_id = $request->pipeline_id;
        $label->save();

        event(new UpdateLabel($request,$label));

        return response()->json(['status'=>'success','message'=>'Label Successfully Updated!']);
    }

    public function labelDelete(Request $request, $id)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'Lead Module Not Active!']);
        }

        $label = Label::where('id',$id)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$label){
            return response()->json(['status'=>'error', 'message'=> 'Label Not Found!']);
        }

        $lead = Lead::where('labels', '=', $label->id)->count();
        $deal = Deal::where('labels', '=', $label->id)->count();

        if($lead == 0 && $deal == 0){
            $label->delete();
            event(new DestroyLabel($label));

            return response()->json(['status' => 'success', 'message' => 'Label Successfully Deleted!']);
        }
        else{
            return response()->json(['status'=>'error', 'message'=> 'There are some Lead and Deal on Label, please remove it first!']);
        }
    }

    public function sourcesList(Request $request)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'Lead Module Not Active!']);
        }

        $sources = Source::where('created_by', '=', creatorId())
                            ->where('workspace_id', $request->workspace_id)
                            ->get()
                            ->map(function($source){
                                return [
                                    'id' => $source->id,
                                    'name'  => $source->name
                                ];
                            });

        return response()->json(['status'=>'success','data'=>$sources]);

    }

    public function sourceCreate(Request $request)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'Lead Module Not Active!']);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|max:20',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        $source                 = new Source();
        $source->name           = $request->name;
        $source->workspace_id   = $request->workspace_id;
        $source->created_by     = creatorId();
        $source->save();

        event(new CreateSource($request,$source));

        return response()->json(['status'=>'success', 'message' => 'Source Successfully Created!']);
    }

    public function sourceUpdate(Request $request, $id)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'Lead Module Not Active!']);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|max:20',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return response()->json(['status'=>'error', 'message' => $messages->first()]);
        }

        $source = Source::where('id',$id)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$source){
            return response()->json(['status'=>'error', 'message' => 'Source Not Found!']);
        }
        $source->name           = $request->name;
        $source->save();

        event(new UpdateSource($request,$source));

        return response()->json(['status'=>'success', 'message' => 'Source Successfully Updated!']);
    }

    public function sourceDelete(Request $request, $id)
    {
        if (!module_is_active('Lead')) {
            return response()->json(['status'=>'error','message'=>'Lead Module Not Active!']);
        }

        $source = Source::where('id',$id)->where('workspace_id',$request->workspace_id)->where('created_by',creatorId())->first();
        if(!$source){
            return response()->json(['status'=>'error', 'message' => 'Source Not Found!']);
        }

        $lead = Lead::where('sources', '=', $source->id)->count();
        $deal = Deal::where('sources', '=', $source->id)->count();

        if ($lead == 0 && $deal == 0) {
            $source->delete();

            event(new DestroySource($source));

            return response()->json(['status'=>'success', 'message' => 'Source Successfully Deleted!']);
        } else {
            return response()->json(['status'=>'error', 'message' => 'There are some Lead and Deal on Sources, please remove it first!']);
        }

    }

}
