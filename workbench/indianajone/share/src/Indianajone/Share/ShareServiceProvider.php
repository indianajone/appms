<?php namespace Indianajone\Share;

use Illuminate\Support\ServiceProvider;
use Indianajone\Share\Models\Share;
use Indianajone\Share\Repositories\DBShareRepository;

class ShareServiceProvider extends ServiceProvider {

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
		$this->package('indianajone/share');
		include __DIR__.'/../../routes.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind('Indianajone\Share\Repositories\ShareRepositoryInterface', function($app)
        {
            return new DBShareRepository(new Share, $app['Indianajone\Applications\DBAppRepository']);
        });
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
