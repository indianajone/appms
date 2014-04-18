<?php namespace Kitti\Medias\Repositories;

use Input;
use Kitti\Medias\Media;
use Indianajone\Applications\AppRepositoryInterface;

class DBMediaRepository extends \AbstractRepository implements MediaRepositoryInterface
{
	public function __construct(Media $media, AppRepositoryInterface $app)
	{
		parent::__construct($media);
		$this->app = $app->findByKey(Input::get('appkey'));
	}

	public function all()
	{
		$data = $this->model->whereAppId($this->app->getKey())->apiFilter()->get();
		
		return $data->each(function($model){
			$model->fields();
		})->toArray();
	}
}