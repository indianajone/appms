<?php

use  League\Fractal\Manager;

class BaseController extends Controller 
{
	protected $current_user;

	protected $transformer;

	public function __construct()
	{
		$this->transformer = new Manager;

		$this->transformer->setRequestedScopes(explode(',', Input::get('with')));

		Validator::resolver(function($translator, $data, $rules, $messages)
		{
			return new \Indianajone\Validators\CustomValidator($translator, $data, $rules, $messages);
		});
	}
	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}
}