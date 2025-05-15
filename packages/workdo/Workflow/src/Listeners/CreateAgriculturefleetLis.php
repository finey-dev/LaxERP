<?php

namespace Workdo\Workflow\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Workflow\Entities\Workflow;
use Workdo\Workflow\Entities\WorkflowModule;
use Workdo\Workflow\Entities\WorkflowModuleField;
use Workdo\Workflow\Entities\WorkflowUtility;
use Workdo\AgricultureManagement\Events\CreateAgriculturefleet;

class CreateAgriculturefleetLis
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
    public function handle(CreateAgriculturefleet $event)
    {
        if(module_is_active('Workflow'))
        {

            $agriculture_fleet = $event->agriculture_fleet;
            $request = $event->request;
            $workflow_module = WorkflowModule::where('submodule','New Agriculture Fleet')->first();
            if($workflow_module)
            {
                $workflows = Workflow::where('event',$workflow_module->id)->where('workspace',$agriculture_fleet->workspace)->where('created_by',$agriculture_fleet->created_by)->get();
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

                                if($workflow_module_field->field_name == 'Capacity')
                                {
                                    $status = $symbolToOperator[$symbol]($request->capacity,$condition->value);
                                }
                                else if($workflow_module_field->field_name == 'Status')
                                {
                                    $status = $symbolToOperator[$symbol]($request->status,$condition->value);
                                }
                                else if($workflow_module_field->field_name == 'Price')
                                {
                                    $status = $symbolToOperator[$symbol]($request->price,$condition->value);
                                }
                                else if($workflow_module_field->field_name == 'Quantity')
                                {
                                    $status = $symbolToOperator[$symbol]($request->quantity,$condition->value);
                                }
                                else
                                {
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

                        WorkflowUtility::call_do_this($workflow->id,$agriculture_fleet);
                    }
                }
            }

        }
    }
}
