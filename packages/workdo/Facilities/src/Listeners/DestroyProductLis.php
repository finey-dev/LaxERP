<?php

namespace Workdo\Facilities\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Workdo\ProductService\Events\DestroyProduct;
use Workdo\Facilities\Entities\FacilitiesService;

class DestroyProductLis
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

    public function handle(DestroyProduct $event)
    {
        $productService = $event->productService;
        if (module_is_active('Facilities')) {
            $service = FacilitiesService::where('item_id', $productService->id)->first();
            if(!empty($service))
            {
                $service->delete();
            }
        }
    }
}
