<?php

namespace Workdo\Workflow\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\Workflow\Entities\Workflow;
use Workdo\Workflow\Entities\WorkflowModule;
use Workdo\Workflow\Entities\WorkflowModuleField;
use Workdo\Workflow\Entities\WorkflowUtility;
use Workdo\Retainer\Events\CreateRetainer;

class CreateRetainerLis
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
    public function handle(CreateRetainer $event)
    {
        if(module_is_active('Workflow'))
        {
            $retainer = $event->retainer;
            $request = $event->request;

            $workflow_module = WorkflowModule::where('submodule','New Retainer')->first();
            if($workflow_module)
            {
                $workflows = Workflow::where('event',$workflow_module->id)->where('workspace',$retainer->workspace)->where('created_by',$retainer->created_by)->get();
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
                                // for price

                                if(($workflow_module_field->field_name == 'price') && isset($symbolToOperator[$symbol]))
                                {
                                    $status = $symbolToOperator[$symbol]($retainer->getTotal(),$condition->value);
                                }
                                else if($workflow_module_field->field_name == 'Tax')
                                {
                                    $itemcount = $request->items;
                                    foreach($itemcount as $item)
                                    {
                                        $finals = $item['tax'];
                                        $values = explode(',',$finals);
                                        foreach ($values as $value) {
                                            $status = $symbolToOperator[$symbol]($value, $condition->value);
                                        }
                                    }
                                }
                                else if($workflow_module_field->field_name == 'Category')
                                {
                                    $status = $symbolToOperator[$symbol]($retainer->category_id,$condition->value);
                                }
                                else if($workflow_module_field->field_name == 'quantity')
                                {
                                    $quantities = $request->items;
                                    foreach ($quantities as $quantityData)
                                    {
                                        $quantity = $quantityData['quantity'];
                                        $status = $symbolToOperator[$symbol]($quantity, $condition->value);
                                    }
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

                        WorkflowUtility::call_do_this($workflow->id,$retainer);

                    }
                }
            }

        }
    }
}
