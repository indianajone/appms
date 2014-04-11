<?php namespace Kitti\Galleries;

use Illuminate\Support\ServiceProvider;

class GalleriesServiceProvider extends ServiceProvider 
{
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
      $this->package('kitti/galleries');
      include __DIR__.'/../../routes.php';
  }

  /**
   * Register the service provider.
   *
   * @return void
   */
  public function register()
  {
      $this->app->bind('Kitti\\Galleries\\GalleryRepositoryInterface', function($app){
         return new DBGalleryRepository(
            $app['Kitti\\Galleries\\Gallery'], 
            $app['Indianajone\\Applications\\AppRepositoryInterface']
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
      return array();
  }

}