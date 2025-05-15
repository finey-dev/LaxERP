<?php

namespace Workdo\ApiDocsGenerator\Providers;

use Illuminate\Support\ServiceProvider;
use Workdo\ApiDocsGenerator\Providers\EventServiceProvider;
use Workdo\ApiDocsGenerator\Providers\RouteServiceProvider;

class ApiDocsGeneratorServiceProvider extends ServiceProvider
{

    protected $moduleName = 'ApiDocsGenerator';
    protected $moduleNameLower = 'apidocsgenerator';

    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(EventServiceProvider::class);
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'api-docs-generator');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->registerTranslations();
        $this->registerMiddleware('custom.jwt');
    }


    protected function registerMiddleware($alias)
    {
        $router = $this->app['router'];

        $router->aliasMiddleware($alias, \Workdo\ApiDocsGenerator\Http\Middleware\CustomJwtAuth::class);
    }


    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', $this->moduleNameLower);
            $this->loadJsonTranslationsFrom(__DIR__.'/../Resources/lang');
        }
    }
}
