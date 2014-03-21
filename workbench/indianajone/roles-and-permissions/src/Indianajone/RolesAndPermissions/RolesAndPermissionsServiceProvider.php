<?php namespace Indianajone\RolesAndPermissions;

use Illuminate\Support\ServiceProvider;
use Indianajone\RolesAndPermissions\RolesAndPermissions;

class RolesAndPermissionsServiceProvider extends ServiceProvider {

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
		$this->package('indianajone/roles-and-permissions');
		include __DIR__.'/../../routes.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerRoleRepository();
		$this->app->bind('RolesAndPermissions', function($app)
        {
            return new RolesAndPermissions($app);
        });	
	}

	public function registerRoleRepository()
	{
		$this->app->bind('Indianajone\RolesAndPermissions\RoleRepositoryInterface', function(){
			return new DBRoleRepository;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('RolesAndPermissions');
	}

}