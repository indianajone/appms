<?php namespace Core\Response;

use Illuminate\Support\ServiceProvider;

class ResponseServiceProvider extends ServiceProvider {

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
	    $this->package('core/response');
	    include __DIR__.'/../../routes.php';
	}
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
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
		return array();
	}

}