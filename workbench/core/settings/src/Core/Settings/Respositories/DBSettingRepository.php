<?php namespace Core\Settings\Repositories;

use Core\Settings\Setting;

class DBSettingRepository extends \AbstractRepository implements SettingRepositoryInterface
{
	public function __construct(Setting $model)
	{
		parent::__construct($model);
	}

	public function all()
	{
		# code...
	}
}