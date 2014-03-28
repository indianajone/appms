<?php namespace Indianajone\Applications;

use Input;
use Max\User\Repository\UserRepositoryInterface;
use Indianajone\Applications\Application;
use Illuminate\Support\Contracts\ArrayableInterface;

class DBAppRepository extends \AbstractRepository Implements AppRepositoryInterface
{

	protected $countWithChildren;

	public function __construct(Application $apps, UserRepositoryInterface $users)
	{
		parent::__construct($apps);
		$this->users = $users;
	}

	public function countWithChildren()
	{
		return $this->countWithChildren;
	}

	public function all()
	{
		$users = $this->users->children(Input::get('user_id'));
		$query = $this->model->whereIn('user_id', $users->lists('id'));
		$this->countWithChildren = $query->count();
		$apps = $query->apiFilter()->with('meta')->get();

		$apps->each(function($app)
		{
			$app->fields();
			$app->meta->each(function($meta) use($app){
				$app[$meta->getAttribute('meta_key')] = $meta->getAttribute('meta_value');
			});
		});

		return $apps;
	}
}