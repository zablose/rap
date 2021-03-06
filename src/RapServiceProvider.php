<?php declare(strict_types=1);

namespace Zablose\Rap;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class RapServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/rap.php' => config_path('rap.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../migrations/' => base_path('/database/migrations'),
        ], 'migrations');

        $this->registerBladeExtensions();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/rap.php', 'rap');
    }

    protected function registerBladeExtensions(): void
    {
        /** @var Blade $blade */
        $blade = resolve('view')->getEngineResolver()->resolve('blade')->getCompiler();

        $blade->directive('role', function ($roles)
        {
            return "<?php if (Auth::check() && Auth::user()->rap()->is({$roles})): ?>";
        });

        $blade->directive('endrole', function ()
        {
            return "<?php endif; ?>";
        });

        $blade->directive('permission', function ($permissions)
        {
            return "<?php if (Auth::check() && Auth::user()->rap()->can({$permissions})): ?>";
        });

        $blade->directive('endpermission', function ()
        {
            return "<?php endif; ?>";
        });
    }
}
