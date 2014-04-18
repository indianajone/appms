<?php namespace Kitti\Galleries;

use Appl, Config, Input;
use Kitti\Galleries\GalleryRepositoryInterface;
use Indianajone\Applications\AppRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class DBGalleryRepository extends \AbstractRepository implements GalleryRepositoryInterface
{
	public function __construct(Gallery $gallery, AppRepositoryInterface $app)
	{
		parent::__construct($gallery);
		$this->app = $app->findByKey(Input::get('appkey'));
	}

	private function queryMedia($query)
	{

	}

	public function all()
	{
		$galleries = $this->model->whereAppId($this->app->getKey())->apiFilter()->with(array(
                    	'medias' => function($query)
                    	{
                        $query->select(array(
                           'id', 'gallery_id', 'name', 'picture', 'type'
                        ));
                    	}))->get();

		$galleries->each(function($gallery){
			$medias = $gallery->getRelation('medias')->toArray();
			$count = count($medias);
			if($count >= Config::get('galleries::limit'))
				$medias = array_slice($medias, 0, Config::get('galleries::limit'));

			$gallery->setRelation('medias', new Collection($medias));
		});

		return $galleries->toArray();
	}

	public function countMedia($id)
	{
		return $this->find($id)->medias()->count();
	}

	public function find($id)
	{
		$gallery = $this->model->with(array('medias' => function($query)
                    {
                        $query->apiFilter()->select(array(
                            'id', 'gallery_id', 'name', 'picture', 'type'
                        ));
                    }))->findOrFail($id)->fields();

		$medias = $gallery->getRelation('medias')->toArray();
		$count = count($medias);
			if($count >= Config::get('galleries::limit'))
				$medias = array_slice($medias, 0, Config::get('galleries::limit'));

			$gallery->setRelation('medias', new Collection($medias));

		return $gallery->toArray();
	}

	public function owner($name)
	{
		return $this->model->$name();
	}
}