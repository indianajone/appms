<?php namespace Indianajone\Share\Repositories;

use Indianajone\Share\Models\Share;

class DBShareRepository extends \AbstractRepository Implements ShareRepositoryInterface
{
	public function __construct(Share $model, $app) 
	{
		parent::__construct($model);
		$this->app = $app;
	}

	public function all()
	{
		# code...
	}

	public function getApp()
	{
		return $this->app;
	}

}