<?php namespace Kitti\Articles\Repositories;

use Appl, Input;
use Kitti\Articles\Article;

class DBArticleRepository extends \AbstractRepository implements ArticleRepositoryInterface
{
	/**
	 * Required columns for hierarchy sorting.
	 *
	 * @var Array
	 */
	protected $columns = array('id', 'name', 'lft', 'rgt', 'parent_id', 'depth');

	public function __construct(Article $model) 
	{
		parent::__construct($model);
	}

	public function all()
	{
		$app_id = Appl::getAppIDByKey(Input::get('appkey'));

		$articles = $this->model->whereAppId($app_id)->apiFilter()->get();

		$articles->each(function($article) {
			$article->fields();
			
			foreach ($article->categories()->get() as $cat)
			{
				if($cat->isRoot())
				{
					$article->setRelation($cat->getRoot()->name, $cat->getDescendants($this->columns)->toHierarchy());
				}
				else
				{
					$root = $cat->getRoot();
					if(!is_null($root))
						$article->setRelation($root->name, $cat->getDescendantsAndSelf($this->columns)->toHierarchy());
				}
			};

			$gallery = $article->gallery()->first();
			if($gallery) $article->setRelation('gallery', $gallery);
		
 		});

		return $articles;
	}

	public function find($id)
	{
		$article = $this->model->apiFilter()->with('categories')->findOrFail($id)->fields();
		
		$article->setHidden(array_add($article->getHidden(), null,'categories'));
	
		
		foreach ($article->categories as $key => $cat) 
		{
			
			if($cat->isRoot())
			{
				$article->setRelation($cat->getRoot()->name, $cat->getDescendants($this->columns)->toHierarchy());
			}
			else
			{
				$root = $cat->getRoot();
				if(!is_null($root))
					$article->setRelation($root->name, $cat->getDescendantsAndSelf($this->columns)->toHierarchy());
			}
		}

		$gallery = $article->gallery()->with(array('medias' => function($query){
			// $query->select(array('id', 'picture', 'gallery_id'));
			$query->offset(0)->limit(5)->orderBy('id', 'desc');
		}))->first(); //array('id', 'name', 'picture'));
		if($gallery) $article->setRelation('gallery', $gallery);

		return $article;
	}

}