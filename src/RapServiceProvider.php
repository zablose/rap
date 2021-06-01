<?php

declare(strict_types=1);

namespace Zablose\Rap;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class RapServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([__DIR__.'/../config/rap.php' => config_path('rap.php')], ['config']);

        $this->publishes([__DIR__.'/../migrations/' => base_path('/database/migrations')], ['migrations']);

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

        $blade->directive(
            'role',
            fn($roles) => self::php('if (Auth::check() && Auth::user()->rap()->is('.$roles.')):')
        );

        $blade->directive('endrole', fn() => self::php('endif;'));

        $blade->directive(
            'permission',
            fn($permissions) => self::php('if (Auth::check() && Auth::user()->rap()->can('.$permissions.')):')
        );

        $blade->directive('endpermission', fn() => self::php('endif;'));
    }

    protected static function php(string $code): string
    {
        return '<?php '.$code.' ?>';
    }
}
