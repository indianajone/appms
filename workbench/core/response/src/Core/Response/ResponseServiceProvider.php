<?php namespace Core\Response;

use Illuminate\Support\ServiceProvider;
use App;

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
		App::bind('response', function($app)
		{	
			return new Response($app['config']);
		});
            // $this->app['response'] = $this->app->share(function($app)
            // {
                // return new Response($app['config']);
            // });
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