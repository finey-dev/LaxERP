<?php

namespace Workdo\Facilities\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\ProductService\Events\UpdateProduct;
use Workdo\Facilities\Entities\FacilitiesService;

class UpdateProductLis
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

    public function handle(UpdateProduct $event)
    {
        $productService = $event->productService;
        $request        = $event->request;
        if (module_is_active('Facilities')) {
            $service = FacilitiesService::where('item_id', $productService->id)->first();

            if ($service) {
                $service->item_id          = $productService->id;
                $service->space            = implode(',',$request->space);
                $service->slot             = $request->slot;
                $service->time             = $request->time;
                $service->save();
            }
        }
    }
}
