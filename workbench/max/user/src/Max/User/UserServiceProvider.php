<?php namespace Max\User;

use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider {

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
        $this->package('max/user');
        include __DIR__.'/../../routes.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind('Max\\User\\Repository\\UserRepositoryInterface', function($app){
			return new  \Max\User\Repository\DBUserRepository($app['Max\\User\\Models\\User']);
		});

		$this->registerPlugin();
	}

	public function registerPlugin()
	{
		$this->app->plugin->register('user', 'Max\\User\\Models\\User');
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