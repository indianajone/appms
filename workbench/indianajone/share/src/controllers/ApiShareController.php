<?php namespace Indianajone\Share\Controller;

use Config, Response;
use Indianajone\Share\Repositories\ShareRepositoryInterface;

class ApiShareController extends \BaseController
{
	public function __construct(ShareRepositoryInterface $shares)
	{
		$this->shares = $shares;
	}

	public function provider($provider)
	{
		$cls = 'Indianajone\\Share\\Providers\\'.ucfirst($provider).'Provider';

		if( class_exists($cls) )
		{
			if($this->shares->validate('share'))
			{
				$this->provider = new $cls;
				return $this->provider->share();
			}

			return Response::message(400, $this->shares->errors());
		}

		return Response::message(400, 'Sharing to '. $provider . ' has not support yet');
		
	}
}