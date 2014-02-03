<?php 
namespace Indianajone\Applications;

use Illuminate\Support\ServiceProvider;

class ApplicationsServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;
	
	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('indianajone/applications');
		include __DIR__.'/../../routes.php';

		$this->app->validator->resolver(function($translator, $data, $rules, $messages)
		{
		    return new \Indianajone\Validators\Rules\ExistLoop($translator, $data, $rules, $messages);
		});
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind('appl', function($app)
        {
            return new Appl();
        });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('appl');
	}

}