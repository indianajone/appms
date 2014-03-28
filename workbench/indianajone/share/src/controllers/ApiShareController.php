<?php namespace Indianajone\Share\Controller;

use Config, Response;
use Indianajone\Share\Repositories\ShareRepositoryInterface;
use Indianajone\Applications\AppRepositoryInterface;

class ApiShareController extends \BaseController
{
	public function __construct(ShareRepositoryInterface $shares)
	{
		$this->shares = $shares;
	}

	public function provider($provider)
	{
		if (method_exists($this, $provider))
			return call_user_func_array(array($this, $provider), array());
		
		return Response::message(400, 'Sharing to '. $provider . ' has not support yet');
	}

	public function facebook()
	{
		$facebook = new \Facebook(array(
	        'appId' => Config::get('share::facebook.appId'),
	        'secret' => Config::get('share::facebook.secret'),
	        'allowSignedRequest' => false
	    ));


		// $facebook->setAccessToken('CAAEMRr1XQ0oBAJ7atm83ilc4o61wqQ0X9w8709tLq4n9Ei9nXpL9WpEWZBL8MqtkzaZBx63S3CFLVhyVTS7Y2dWo4JZBGMUxZBuo3gUBAgIIsaZBtq7U5ZAXi06S9DU75RVEJcKwZAUfUina4lw9tpQLfo6ZCLQauBjU8T6VpX4HIVaUfUOPnRGZC0ZAriuYFBrC8ZD');
   		
   		// $facebook->setExtendedAccessToken();

   		print_r( $this->shares->getAccessToken('fb_access_token') );
   		exit;

		// $response = $facebook->api(
		//     // '229255610602615/feed',
		//     'me/accounts',
		// 	'GET'
		// );	

		// $response = $facebook->api(
		// 	'229255610602615/feed', // feed | photos
  // 			'POST',
  // 			array(
  // 				// 'message' => 'Yo!',
  // 				// 'name' => 'Name: Hello',
  // 				// 'caption' => 'Caption: Yo!',
  // 				'no_story' => true,
  // 				'message' => 'This is description hey yo',
  // 				'picture' => 'http://api-thaimissing.truelife.com/pictures/2014-03-27-8351395914480.png',
  // 			)
		// );

		// $response = $facebook->api(
		//   'me/objects/article',
		//   'POST',
		//   array(
		//     'app_id' => $facebook->getAppId(),
		//     'type' => "article",
		//     'url' => "http://samples.ogp.me/434264856596891",
		//     'title' => "Sample Article",
		//     'image' => "https://s-static.ak.fbcdn.net/images/devsite/attachment_blank.png",
		//     'description' => ""
		//   )
		// );  

		// return $response;   				      
	}

	public function missingMethod($parameters = array())
	{
	    return $parameters;
	}
}