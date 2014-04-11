<?php namespace Kitti\Galleries;

use Appl, Input;
use Kitti\Galleries\GalleryRepositoryInterface;
use Indianajone\Applications\AppRepositoryInterface;

class DBGalleryRepository extends \AbstractRepository implements GalleryRepositoryInterface
{
	public function __construct(Gallery $gallery, AppRepositoryInterface $app)
	{
		parent::__construct($gallery);
		$this->app = $app->findByKey(Input::get('appkey'));
	}

	public function all()
	{
		return $this->model->whereAppId($this->app->getKey())->get()->toArray();
	}

	public function owner($name)
	{
		return $this->model->$name();
	}
}