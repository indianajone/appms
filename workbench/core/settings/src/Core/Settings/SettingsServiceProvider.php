<?php namespace Core\Settings;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider {

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
		$this->package('core/settings');

		include __DIR__.'/../../routes.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind('Core\Settings\Repositories\SettingRepositoryInterface', 'Core\Settings\Repositories\DBSettingRepository', true);

		$this->app->booting(function()
		{
			$loader = AliasLoader::getInstance();

			$loader->alias('Settings', 'Core\Settings\Facades\Setting');
		});

		$this->app->register('Core\Plugins\PluginsServiceProvider');

		$this->app->register('Core\Response\ResponseServiceProvider');

		$this->app->register('Max\User\UserServiceProvider');

	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('settings');
	}

}
