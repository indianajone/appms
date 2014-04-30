<?php

use Carbon\Carbon;
use \Image, \Config;
use Indianajone\Categories\Category;
use Kitti\Medias\Media;

Trait BaseModel 
{
    use ApiFilterable;
	/** 
	 * Override getDateFormat to unixtime stamp.
	 * @return String
	 */
	protected function getDateFormat()
    {
        return 'U';
    }

  //   public function formatTime($value)
  //   {
  //   	$format = Input::get('date_format', null);
		// return $format ? Carbon::createFromTimeStamp($value, Config::get('app.timezone'))->format($format) : $value;    
  //   }

    // public function toArray()
    // {
    //     $attributes = $this->attributesToArray();

    //     foreach ($attributes as $key => $value) 
    //     {
    //         if(in_array($key, $this->getDates()))
    //         {
    //             $attributes[$key] = $this->formatTime($value);
    //         }
    //     }

    //     return array_merge($attributes, $this->relationsToArray());
    // }

    public function getPictureAttribute($value)
    {
        if($value)
        {
            $path = parse_url($value, PHP_URL_PATH);
            if(preg_match('/[0-9]{4}[- \/.](0[1-9]|1[0-2])[- \/.](0[1-9]|[1-2][0-9]|3[0-1])/', $path, $match))
            {
                $folder = str_replace('/', '-', $match[0]); 
                return asset(Config::get('timthumb::prefix').'/'.$folder.'-'.basename($path));
            }

            return $value;
        }

        return $value;
    }

    public function getMeta()
    {
        $this->meta->each(function($meta) {
            $this->setAttribute($meta->getAttribute('meta_key'), $meta->getAttribute('meta_value'));
        });

        return $this;
    }

	public function scopeKeywords($query, $fields)
	{
		$keyword = Input::get('q', '');
		$builder =  $query->where($fields[0],'like', '%'.$keyword.'%');

		foreach ($fields as $key => $field) {
			if($key >= 1) $builder = $builder->orWhere($field, 'like', '%'.$keyword.'%');
		}

		return $builder;
	}

	public function scopeApiFilter($query)
    {
        foreach ($this->getMap() as $filter => $field) 
        {
            if (in_array($filter, $this->getMultiple())) 
            {
                $input = Input::get($field)
                    ? explode($this->getDelimiter(), Input::get($field))
                    : array_get($this->getDefaults(), $filter, null);
            } 
            else 
            {
                $input = Input::get($field)
                    ? Input::get($field)
                    : array_get($this->getDefaults(), $filter, null);
            }

            if (!is_null($input)) 
            {
                $query = $query->{$filter}($input);
            }
        }
        return $query;
    }

    public function createPicture($app_id)
    {
        $picture = Input::get('picture', null);

        if($picture)
        {
            if (is_numeric($picture))
            {
                $id = (int) $picture;
                $media = Media::find($id);

                if($media)
                {
                    $this->update(array(
                        'picture' => $media->getOriginal('picture')
                    ));
                }
                else
                {
                    return Response::json(array(
                        'header'=> [
                            'code'=> 400,
                            'message'=> 'Selected id is invalid.'
                        ]
                    ), 200);
                }
            }
            // elseif(filter_var($picture, FILTER_VALIDATE_URL))
            // {
            //     $this->update(array(
            //         'picture' => $picture
            //     ));
            // }
            else
            {
                // #FIXED iDevice base64 encode.
                $picture = str_replace('%2B','+', $picture);
                $picture = str_replace('-','+', $picture);
                
                $response = Image::upload($picture);

                if(is_object($response)) return $response;

                if('Kitti\Medias\Media' != get_called_class() && $this->gallery)
                {
                    $this->gallery->medias()->create(array(
                         'app_id' => $app_id,
                         'gallery_id' => $this->gallery->id,
                         'name' => 'Image',
                         'picture' => $response,
                         'type' => 'image'
                    ));
                    
                    $this->update(array(
                        'picture' => $response
                    ));
                }

                return $response;
            }
        }
    }

    /**
    * Get definded rules in Model.
    *
    * @return Array
    **/
    public function rules($action)
    {
         return isset($this->rules) ? $this->rules[$action] : array();
    }

    public function fields()
    {
        $field = Input::get('fields', null);
        $fields = $field ? explode(',', $field) : $field;

        $hidden = Input::get('hidden', null);
        $hiddens = $hidden ? explode(',', $hidden) : $hidden;
        
        if($fields) $this->setVisible($fields);
        if($hiddens) $this->setHidden(array_merge($hiddens, $this->getHidden()));

        return $this;
    }

    public function scopeFilterCats($query, $ids)
    {
        if(is_null($this->categories)) return;

        if($ids != '*')
        {
            $categories = Category::findMany($ids);
            
            foreach($categories as $category)
            {
                if($category->isRoot())
                    $cids = $category->getDescendantsAndSelf(['id'])->lists('id'); 
                else
                {
                    $root = $category->getRoot();
                    $cids = $root->getDescendantsAndSelf(['id'])->lists('id');
                }

                $query = $query->whereIn('id', function($type) use($cids) {

                    $foreign_key = $this->getForeignKey();
                    $other_key = $this->categories()->getOtherKey();
                    $pivot = $this->categories()->getTable();

                    $type->select($foreign_key)->from($pivot)->whereIn($other_key, $cids)->groupBy($foreign_key);
                });
            }
        }

        return $query;
    }

    public function scopeOrder_by($query, $order)
    {
        if(!array_key_exists(1, $order)) $order[1] = 'asc';

        $field = $order[0];
        $dir = $order[1];

        
        if(\Schema::hasColumn($this->getTable(), $field))
        {
            return $query->orderBy($field, $dir);
        }   
    }

    public function scopeSearch($query)
    {
        return $this->keywords(array('first_name', 'username'));
    }

    public function scopeTime($query, $field, $value)
	{
        $format = Input::get('date_format', null);

	    if($format)
	    {
	    	try {
                $time = Carbon::createFromFormat($format, $value, Config::get('app.timezone'));
            }
            catch(InvalidArgumentException $e)
            {
                throw new InvalidArgumentException('date format '. $format . ' and '. $value .' are not match');
            }

	    	if($time) return $query->where($field, '>=', $time->timestamp);
	    }
	    
	    return $query->where($field, '>=', $value);
	}

    public function scopeWhereUpdated($query, $time)
    {
        return $this->time('updated_at', $time);
    }

    public function scopeWhereCreated($query, $time)
    {
        return $this->time('created_at', $time);
    }

	public function scopeApp($query)
    {
        return $query->whereAppId(Appl::getAppIdByKey(Input::get('appkey')));
    }
}