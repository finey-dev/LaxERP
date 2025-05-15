<?php

namespace Workdo\BusinessProcessMapping\Providers;

use Illuminate\Support\ServiceProvider;
use Workdo\BusinessProcessMapping\Entities\Related;
use Workdo\BusinessProcessMapping\Entities\BusinessProcessMapping;
use Workdo\Taskly\Entities\Task;

class ViewComposer extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */

    public $views;

    public function boot()
    {
        $this->views  = [
            'Project'           => 'taskly::projects.show',
            'Task'              => 'taskly::projects.taskboard',
            'Lead'              => 'lead::leads.show',
            'Deal'              => 'lead::deals.show',
            'Property'          => 'property-management::property.show',
            'Contract'          => 'contract::contracts.show',
        ];


        view()->composer(array_values($this->views), function ($view) {


            $module = array_search($view->getName(), $this->views);

            $viewData = $view->getData();
            $module_data = null;

            if (isset($viewData['project'])) {
                $module_data = $viewData['project'];
            } elseif (isset($viewData['lead'])) {
                $module_data = $viewData['lead'];
            } elseif (isset($viewData['deal'])) {
                $module_data = $viewData['deal'];
            } elseif (isset($viewData['property'])) {
                $module_data = $viewData['property'];
            } elseif (isset($viewData['contract'])) {
                $module_data = $viewData['contract'];
            }

            try {
                $related = Related::where('model_name', $module)->first();
                $business = null;

                if ($module == 'Task') {
                    $taskIds = Task::where('project_id', $module_data->id)->pluck('id')->toArray();
                    $business = BusinessProcessMapping::where('related_to', $related->id)->whereIn('related_assign', $taskIds)->get();
                } elseif ($related && $module_data) {
                    $business = BusinessProcessMapping::where('related_to', $related->id)
                        ->where('related_assign', $module_data->id)
                        ->get();
                }
                if ($business) {
                    $view->getFactory()->startPush('addButtonHook', view('business-process-mapping::layouts.addhook', compact('related', 'business', 'module', 'module_data')));            
                }
            } catch (\Throwable $th) {
            }
        });
    }

    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
