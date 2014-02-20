<?php

class BaseController extends Controller 
{
	function __construct()
	{
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