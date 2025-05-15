<?php

namespace Workdo\Internalknowledge\Listeners;

use App\Events\CompanyMenuEvent;

class CompanyMenuListener
{
    /**
     * Handle the event.
     */
    public function handle(CompanyMenuEvent $event): void
    {
        $module = 'Internalknowledge';
        $menu = $event->menu;
        $menu->add([
            'category' => 'Productivity',
            'title' => __('Internal Knowledge'),
            'icon' => 'book',
            'name' => 'internalknowledge',
            'parent' => null,
            'order' => 1125,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => '',  
            'module' => $module,
            'permission' => 'internalknowledge manage'
        ]);
        $menu->add([
            'category' => 'Productivity',
            'title' => __('Book'),
            'icon' => '',
            'name' => 'book',
            'parent' => 'internalknowledge',
            'order' => 10,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'book.index',  
            'module' => $module,
            'permission' => 'book manage'
        ]);
        $menu->add([
            'category' => 'Productivity',
            'title' => __('Article'),
            'icon' => '',
            'name' => 'article',
            'parent' => 'internalknowledge',
            'order' => 15,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'article.index',  
            'module' => $module,
            'permission' => 'article manage'
        ]);
        $menu->add([
            'category' => 'Productivity',
            'title' => __('My Article'),
            'icon' => '',
            'name' => 'my-article',
            'parent' => 'internalknowledge',
            'order' => 20,
            'ignore_if' => [],
            'depend_on' => [],
            'route' => 'myarticle.index',  
            'module' => $module,
            'permission' => 'my article manage'
        ]);
    }
}
