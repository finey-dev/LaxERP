<?php

namespace Workdo\Facilities\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\ProductService\Events\CreateProduct;
use Workdo\Facilities\Entities\FacilitiesService;

class CreateProductLis
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

    public function handle(CreateProduct $event)
    {
        $productService = $event->productService;
        $request        = $event->request;
        if (module_is_active('Facilities') && $request->type == "facilities") {
            $service                   = new FacilitiesService();
            $service->item_id          = $request->id;
            $service->space            = implode(',',$productService->space);
            $service->slot             = $productService->slot;
            $service->time             = $productService->time;
            $service->created_by       = creatorId();
            $service->workspace        = getActiveWorkSpace();
            $service->save();
        }
    }
}
