<?php namespace Kitti\Medias;

use Illuminate\Support\ServiceProvider;

class MediasServiceProvider extends ServiceProvider 
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
      $this->package('kitti/medias');
      include __DIR__.'/../../routes.php';
   }

   /**
   * Register the service provider.
   *
   * @return void
   */
   public function register()
   {
      $this->app->bind('Kitti\\Medias\\Repositories\\MediaRepositoryInterface', function($app){
         return new Repositories\DBMediaRepository(
            $app['Kitti\\Medias\\Media'],
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