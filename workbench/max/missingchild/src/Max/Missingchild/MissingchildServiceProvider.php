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
		include __DIR__.'/../../routes.php';

		$this->app->validator->resolver(function($translator, $data, $rules, $messages)
        {
            return new \Indianajone\Validators\Rules\ExistsInApp($translator, $data, $rules, $messages);
        });
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
		return array();
	}

}