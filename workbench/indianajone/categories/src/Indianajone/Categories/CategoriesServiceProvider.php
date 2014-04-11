<?php namespace Indianajone\Categories;

use Illuminate\Support\ServiceProvider;

class CategoriesServiceProvider extends \Baum\BaumServiceProvider {

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
		$this->package('indianajone/categories');
		include __DIR__.'/../../routes.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		parent::register();

		$this->app->bind('Indianajone\Categories\Repositories\CategoryRepositoryInterface', function($app){
			return new Repositories\DBCategoryRepository( new Category );
		});

		$this->app->error(function(\Baum\MoveNotPossibleException $exception){
			return \Response::message(400, $exception->getMessage());
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('categories');
	}

}