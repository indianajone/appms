<?php namespace  Max\User\Transformers;

use Config, Input;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * List of resources possible to embed via this transformer
     *
     * @var array
     */
    protected $availableEmbeds = [
        'apps'
    ];

	/**
     * Turn this item object into a generic array
     *
     * @return array
     */
    public function transform($user)
    {
        $transformed = array_map(function($item) use($user)
        {
            if(in_array($item, $user->getDates())) 

                return $this->formatTime($user->{$item});

            return $user->{$item};

        }, Config::get('user::map'));

        $show = Input::get('fields', '*');

        if($show != '*')
        {
            $show = explode(',', $show);

            $transformed = array_only($transformed, $show);
        }

        $hidden = Input::get('hidden', null);

        if($hidden)
        {
            $hidden = explode(',', $hidden);

            $transformed = array_except($transformed, $hidden);
        }

        return $transformed;
    }

    private function formatTime( $time, $format=null )
    {
        if(is_null($time)) return null;

        $format = Input::get('date_format', $format);

        if( is_null( $format ) ) 
        {
            if( $time instanceof Carbon) return $time->timestamp;

            return $time;
        }

        if( $time instanceof Carbon)
            return $time->format($format);

        return Carbon::createFromTimestamp($time)->format($format);

        // throw new \Exception('$time is not instanceof Carbon.');
    }

    /**
     * Embed Author
     *
     * @return League\Fractal\ItemResource
     */
    public function embedApps($user)
    {
        $apps = $user->apps;

        return $this->collection($apps, new UserAppTransformer);
    }
}