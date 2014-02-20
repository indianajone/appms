<?php namespace Max\Missingchild;

use Illuminate\Support\ServiceProvider;

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

        // $this->app['validator']->resolver(function($translator, $data, $rules, $messages)
        // {
        // 	return new \Indianajone\Validators\CustomValidator($translator, $data, $rules, $messages);

        // });

        // var_dump($this->app['validator']->translator());
        // exit;

        // Validator::extend('existsinapp', 'Indianajone\\Validators\\Rules\\ExistsInApp@validateExistsInApp', $this->app['validator']->getTranslator());

        

		include __DIR__.'/../../routes.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
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