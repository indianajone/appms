<?php namespace Max\Missingchild;

use Illuminate\Support\ServiceProvider;
use Max\Missingchild\Repositories\DBMissingchildRepository;

class MissingchildServiceProvider extends ServiceProvider {

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

		$this->package('max/missingchild');
		include __DIR__.'/../../routes.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind('Max\Missingchild\Repositories\MissingchildRepositoryInterface', function($app){
			return new DBMissingchildRepository(
				$app['Max\\Missingchild\\Models\\Missingchild'],
				$app['Kitti\\Articles\\Repositories\\DBArticleRepository']);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('missingchild');
	}

}