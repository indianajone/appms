<?php namespace Indianajone\Share\Repositories;

use Indianajone\Share\Models\Share;

class DBShareRepository extends \AbstractRepository Implements ShareRepositoryInterface
{
	public function __construct(Share $model) 
	{
		parent::__construct($model);
	}

	public function all()
	{
		# code...
	}

}