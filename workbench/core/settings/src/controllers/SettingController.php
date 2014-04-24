<?php namespace Core\Settings\Controllers;

use Core\Settings\Repositories\SettingRepositoryInterface;

class SettingController extends \BaseController
{
	public function __construct(SettingRepositoryInterface $setting)
	{
		parent::__construct();
	}

	public function getIndex()
	{
		return 'index';
	}
}