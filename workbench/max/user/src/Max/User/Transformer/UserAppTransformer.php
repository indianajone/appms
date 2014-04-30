<?php namespace  Max\User\Transformers;

use League\Fractal\TransformerAbstract;

class UserAppTransformer extends TransformerAbstract
{
    /**
     * Turn this item object into a generic array
     *
     * @return array
     */
    public function transform($app)
    {
        return [
            'id' => (int) $app->id,
            'name' => $app->name
        ];
    }
}
