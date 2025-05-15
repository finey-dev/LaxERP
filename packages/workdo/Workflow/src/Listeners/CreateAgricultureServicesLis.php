<?php

namespace Workdo\Workflow\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Workflow\Entities\Workflow;
use Workdo\Workflow\Entities\WorkflowModule;
use Workdo\Workflow\Entities\WorkflowModuleField;
use Workdo\Workflow\Entities\WorkflowUtility;
use Workdo\AgricultureManagement\Events\CreateAgricultureServices;

class CreateAgricultureServicesLis
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
    public function handle(CreateAgricultureServices $event)
    {
        if(module_is_active('Workflow'))
        {
            $agricultureservice = $event->agricultureservice;
            $request = $event->request;
            $workflow_module = WorkflowModule::where('submodule','New Agriculture Service')->first();
            if($workflow_module)
            {
                $workflows = Workflow::where('event',$workflow_module->id)->where('workspace',$agricultureservice->workspace)->where('created_by',$agricultureservice->created_by)->get();
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

                                if($workflow_module_field->field_name == 'Activity')
                                {
                                    $status = $symbolToOperator[$symbol]($request->activity,$condition->value);
                                }
                                else if($workflow_module_field->field_name == 'Quantity')
                                {
                                    $status = $symbolToOperator[$symbol]($request->qty,$condition->value);
                                }
                                else if($workflow_module_field->field_name == 'UOM')
                                {
                                    $status = $symbolToOperator[$symbol]($request->utm,$condition->value);
                                }
                                else if($workflow_module_field->field_name == 'Total Price')
                                {
                                    $status = $symbolToOperator[$symbol]($request->total_price,$condition->value);
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

                        WorkflowUtility::call_do_this($workflow->id,$agricultureservice);
                    }
                }
            }

        }
    }
}
