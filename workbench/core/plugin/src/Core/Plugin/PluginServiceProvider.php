<?php namespace Core\Plugin;

use Illuminate\Support\ServiceProvider;

class PluginServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;
        
        /**
	 * Boot the service provider.
	 *
	 * @return void
	 */
        public function boot()
	{
	    $this->package('core/plugin');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
            $this->app['plugin'] = $this->app->share(function($app)
            {
                    return new Plugin();
            });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('plugin');
	}

}