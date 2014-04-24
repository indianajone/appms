<?php namespace Core\Response;

use Illuminate\Foundation\AliasLoader;
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
		$this->app->bind('response', function($app)
		{	
			return new Response($app['config']);
		});
            
      $this->app->booting(function()
		{
			$loader = AliasLoader::getInstance();

			$loader->alias('Response', 'Core\Response\Facades\Response');
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		//return array();
            return array('response');
	}

}