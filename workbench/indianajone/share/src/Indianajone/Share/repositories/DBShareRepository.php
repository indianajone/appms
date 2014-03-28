<?php namespace Indianajone\Share\Repositories;

use \DB;
use Indianajone\Share\Models\Share;
use Indianajone\Applications\AppRepositoryInterface;

class DBShareRepository extends \AbstractRepository Implements ShareRepositoryInterface
{
	public function __construct(Share $model, AppRepositoryInterface $apps) 
	{
		parent::__construct($model);
		$this->apps = $apps;

	}

	public function all()
	{
		
	}

	public function validate($action, $input=null)
	{
		// # code...
	}

	public function getAccessToken($key)
	{
		return $this->model->getAttributes();
	}
}