<?php 
namespace Indianajone\Applications;

use Illuminate\Support\ServiceProvider;
use Indianajone\Applications\Application;
use Max\User\Repository\DBUserRepository;

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

        $this->app->bind('Indianajone\Applications\AppRepositoryInterface', function()
        {
            return new DBAppRepository(
            	new Application, 
            	$this->app['Max\User\Repository\DBUserRepository']
            );
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