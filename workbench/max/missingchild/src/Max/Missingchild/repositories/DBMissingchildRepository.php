<?php namespace Max\Missingchild\Repositories;
 
use Appl;
use Carbon\Carbon;
use Max\Missingchild\Models\Missingchild as Child;
use Kitti\Articles\Repositories\ArticleRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class DBMissingchildRepository extends \AbstractRepository implements MissingChildRepositoryInterface
{

	protected $gallery_visible = array('id','name','description','picture','medias');
	protected $media_visible = array('id','name','link','picture');

	public function __construct(Child $child, ArticleRepositoryInterface $article)
	{
		parent::__construct($child);
		$this->article = $article;
	}

	public function all()
	{
		$children = $this->model->apiFilter()->with('gallery.medias', 'categories', 'app_content.gallery.medias')->get();

		$children->each(function($child)
        	{
        		$child->fields();
        		if($child->categories)
        		{
        			foreach($child->categories as $category)
        			{
        				if(!$category->isRoot())
        				{
        					$root = $category->getroot();
        					$relation = $category->descendantsAndSelf()->get();
        					$relation->each(function($relate)
        					{ 
        						$relate->setVisible(array('id','name'));
        					});

        					if($relation->count() > 0)
        						$child->setRelation($root->name, $relation->toHierarchy());
        					else
        						$child->setRelation($root->name, null);
        				}
        				else
        				{
        					$child->setRelation($category->name, $category->descendants()->get()->toHierarchy());
        				}
        			}
        		}

        		if($child->gallery)
        		{
	        		$child->gallery->setVisible($this->gallery_visible);

	        		if($child->gallery->medias)
	        		{
		        		$child->gallery->medias->each(function($media) 
		        		{	
		        			$media->setVisible($this->media_visible);
		        		});

		        		$medias = $child->gallery->getRelation('medias')->toArray();
						$count = count($medias);
						if($count >= \Config::get('galleries::limit'))
						$medias = array_slice($medias, 0, \Config::get('galleries::limit'));

						$child->gallery->setRelation('medias', new Collection($medias));
		        	}
	        	}

	        	if($child->app_content)
	        	{
	        		if($child->app_content->gallery)
	        		{
	        			$child->app_content->gallery->setVisible($this->gallery_visible);

			        	$child->app_content->gallery->medias->each(function($media) 
		        		{
		        			$media->setVisible($this->media_visible);
		        		});

		        		$medias = $child->app_content->medias;
						if ($medias) {
							$count = count($medias);
							if($count >= \Config::get('galleries::limit'))
							$medias = array_slice($medias, 0, \Config::get('galleries::limit'));

							$child->app_content->setRelation('medias', new Collection($medias));
						}
	        		}
        			
	        	}
        	});

		return $children->toArray();
	}

	public function create($input)
	{
		$child = $this->model->newInstance($input);

		if($child->save())
		{
			// Attach categories.
			$child->attachRelations('categories',array_get($input, 'category_id'));

			// Create child's gallery
			$gallery = $child->gallery()->create(array(
			  	'app_id' => array_get($input, 'app_id'),
			  	'content_id' => $child->id,
			  	'name' => $child->first_name .'\'s gallery',             
			  	'published_at' => array_get($input, 'published_at', Carbon::now()->timestamp)
			));

			// Create child's article
			$child->article_id = $this->article->create(array(
				'app_id' => $child->app_id,
			  	'title' => array_get($input, 'title'),
			  	'content' => $child->description,
			  	'wrote_by' =>  array_get($input, 'wrote_by', 'Admin'),       
			  	'published_at' => $gallery->published_at
			));
			
			if($child->save())
			{
				$app_content = $child->app_content()->first();
				// Create gallery for app content
				$app_content->gallery()->create(array(
					'app_id' => $child->app_id,
				  	'content_id' => $child->id,
				  	'name' => $child->first_name .'\'s gallery',             
				  	'published_at' => $gallery->published_at
				));

				$result = array(
					'id' => $child->id,
					'gallery_id' => $child->gallery->id,
					'app_content' => array(
						'id' => $child->article_id,
						'gallery_id' => $app_content->gallery->id
					)
				);

				return $result;
			}

			return null;
		}

		return null;
	}
}