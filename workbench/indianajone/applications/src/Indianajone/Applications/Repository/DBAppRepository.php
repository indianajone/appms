<?php namespace Indianajone\Applications;

use Input;
use Indianajone\Applications\Application;
use Indianajone\Applications\ApplicationMeta;
use Illuminate\Support\Contracts\ArrayableInterface;
use Max\User\Repository\UserRepositoryInterface;

class DBAppRepository extends \AbstractRepository Implements AppRepositoryInterface
{

	protected $countWithChildren;

	public function __construct(
		Application $apps, 
		ApplicationMeta $meta,
		UserRepositoryInterface $users)
	{
		parent::__construct($apps);
		$this->meta = $meta;
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

	public function find($id)
	{
		$user_id = Input::get('user_id');
		$users = $this->users->children($user_id);

		$app = $this->model->apiFilter()->with('meta')->findOrFail($id);
		$app->meta->each(function($meta) use($app){
			$app[$meta->getAttribute('meta_key')] = $meta->getAttribute('meta_value');
		});
		$app->fields();

		if(in_array($app->user_id, $users->lists('id')))
			return $app;
		else 
			return null;
	}

	public function findByKey($key)
	{
		return $this->model->whereAppkey($key)->first();
	}

	public function create($input)
	{
		foreach( $this->model->getFillable() as $key )
		{
			if(array_key_exists($key, $input))
			{
				$this->model->fill(array(
					$key => $input[$key]
				));
			}
		}

		$this->model->fill(array(
			'appkey' => $this->model->genKey()
		));

		return $this->model;
	}

	public function update($id, $input)
	{
		$model = $this->model->findOrFail($id);

		$model->meta->each(function($meta) use ($input) {
			if(array_key_exists($meta->getAttribute('meta_key'), $input))
			{
				$meta->update(array(
					'meta_value' => $input[$meta->getAttribute('meta_key')]
				));
			}
		});

		foreach( $model->getAttributes() as $key => $value )
		{
			if( array_key_exists($key, $input) && $value != $input[$key] )
			{
				if(array_key_exists('picture', $input))
	            {
	            	$response = $model->createPicture($model->app_id);
	            	if(is_object($response)) return $response;             	
	             	unset($input['picture']);
	            }
	            else
	            {
					$model->$key = $input[$key];
				}
			}
		}

		return $model;
	}
}