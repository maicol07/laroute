<?php

namespace Dogado\Laroute;

use Illuminate\Support\ServiceProvider;
use Dogado\Laroute\Console\Commands\LarouteGeneratorCommand;
use Dogado\Laroute\Routes\Collection as Routes;

class LarouteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $source = $this->getConfigPath();
        $this->publishes([$source => config_path('laroute.php')], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $source = $this->getConfigPath();
        $this->mergeConfigFrom($source, 'laroute');

        $this->registerGenerator();

        $this->registerCompiler();

        $this->registerCommand();
    }

    /**
     * Get the config path
     *
     * @return string
     */
    protected function getConfigPath()
    {
        return realpath(__DIR__.'/../config/laroute.php');
    }

    /**
     * Register the generator.
     *
     * @return void
     */
    protected function registerGenerator()
    {
        $this->app->bind(
            'Dogado\Laroute\Generators\GeneratorInterface',
            'Dogado\Laroute\Generators\TemplateGenerator'
        );
    }

    /**
     * Register the compiler.
     *
     * @return void
     */
    protected function registerCompiler()
    {
        $this->app->bind(
            'Dogado\Laroute\Compilers\CompilerInterface',
            'Dogado\Laroute\Compilers\TemplateCompiler'
        );
    }

    /**
     * Register the command
     *
     * @return void
     */
    protected function registerCommand()
    {
        $this->app->singleton(
            'command.laroute.generate',
            function ($app) {
                $config     = $app['config'];
                $routes     = new Routes($app['router']->getRoutes(), $config->get('laroute.filter', 'all'), $config->get('laroute.action_namespace', ''));
                $generator  = $app->make('Dogado\Laroute\Generators\GeneratorInterface');

                return new LarouteGeneratorCommand($config, $routes, $generator);
            }
        );

        $this->commands('command.laroute.generate');
    }
}
