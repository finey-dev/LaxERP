<?php

namespace Workdo\Workflow\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Workflow\Entities\Workflow;
use Workdo\Workflow\Entities\WorkflowModule;
use Workdo\Workflow\Entities\WorkflowModuleField;
use Workdo\Workflow\Entities\WorkflowUtility;
use Workdo\Recruitment\Events\CreateJob;

class CreateJobLis
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(CreateJob $event)
    {
        if(module_is_active('Workflow'))
        {
            $job = $event->job;
            $request = $event->request;
            $workflow_module = WorkflowModule::where('submodule','New Job')->first();

            if($workflow_module)
            {
                $workflows = Workflow::where('event',$workflow_module->id)->where('workspace',$job->workspace)->where('created_by',$job->created_by)->get();
                $condition_symbol = Workflow::$condition_symbol;

                $symbolToOperator = [
                    '>' => function ($a, $b) { return $a > $b; },
                    '<' => function ($a, $b) { return $a < $b; },
                    '=' => function ($a, $b) { return $a == $b; },
                    '!=' => function ($a, $b) { return $a != $b; },
                ];

                foreach ($workflows as $key => $workflow)
                {
                    $conditions = !empty($workflow->json_data) ? json_decode($workflow->json_data) : [];
                    $status = false;
                    foreach ($conditions as $key => $condition)
                    {
                        if($condition->value)
                        {
                            $workflow_module_field = WorkflowModuleField::find($condition->preview_type);
                            if(!empty($workflow_module_field))
                            {
                                $symbol = array_key_exists($condition->condition,$condition_symbol) ? $condition_symbol[$condition->condition] : '=';

                                if(($workflow_module_field->field_name == 'Recruitment Type'))
                                {
                                    $status = $symbolToOperator[$symbol]($request->recruitment_type,$condition->value);
                                }
                                else if($workflow_module_field->field_name == 'Charges')
                                {
                                    $status = $symbolToOperator[$symbol]($request->charges,$condition->value);
                                }
                                else if($workflow_module_field->field_name == 'Location')
                                {
                                    $status = $symbolToOperator[$symbol]($request->location,$condition->value);
                                }
                                else{

                                    break;
                                }
                            }
                        }

                        if($status == false)
                        {
                            break;
                        }
                    }

                    if($status == true)
                    {

                        WorkflowUtility::call_do_this($workflow->id,$job);

                    }
                }
            }
        }
    }
}
