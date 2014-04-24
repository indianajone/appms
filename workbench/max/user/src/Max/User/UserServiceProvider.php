<?php namespace Max\User;

use PluginableInterface as Pluginable;
use Illuminate\Support\ServiceProvider;
use Max\User\Repository\DBUserRepository;

class UserServiceProvider extends ServiceProvider implements Pluginable {

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
		$this->app->bind('Max\\User\\Repository\\UserRepositoryInterface', 'Max\User\Repository\DBUserRepository');

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