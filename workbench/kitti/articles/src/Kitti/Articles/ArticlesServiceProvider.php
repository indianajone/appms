<?php namespace Kitti\Articles;

use Illuminate\Support\ServiceProvider;
use Kitti\Articles\Repositories\DBArticleRepository;
use PluginableInterface as Pluginable;

class ArticlesServiceProvider extends ServiceProvider implements Pluginable {

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
	  $this->package('kitti/articles');
	  include __DIR__.'/../../routes.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind('Kitti\Articles\Repositories\ArticleRepositoryInterface', function($app){
			return new DBArticleRepository($app['Kitti\\Articles\\Article']);
		});
	}

	public function registerPlugin()
	{
		$this->app->plugin->register('articles', 'Kitti\\Articles\\Article');
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