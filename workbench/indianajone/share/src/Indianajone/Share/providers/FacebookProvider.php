<?php namespace Indianajone\Share\Providers;

use App, Appl, Config;

class FacebookProvider extends \Facebook implements ShareProviderInterface
{
	protected $app;

	public function __construct($app)
	{
		$config = array(
	        'appId' => \Config::get('share::facebook.appId'),
	        'secret' => \Config::get('share::facebook.secret'),
	        'allowSignedRequest' => true
	   );

	   $this->app = $app;
		
		parent::__construct($config);

		return $this;
	}

	public function getModel($type)
	{
		switch($type)
		{
			case 'article':
				$model = Config::get('share::model.article');
			break;

			default:
				$model = null;
			break;
		}

		return $model;
	}

	public function share($input=null)
	{
		$type = array_get($input, 'content_type');
		$id = array_get($input, 'content_id');

		$cls = $this->getModel($type);
		$model = $this->app[$cls];

		$content = $model->find($id);

		$access_token = Appl::getMeta('fb_access_token');
		// dd($access_token);
		// $this->setAccessToken($access_token);

		// dd($this);
   		// $this->setExtendedAccessToken();

   		// Appl::setMeta('fb_access_token', $this->getAccessToken());

   		// dd($this->getAccessToken());

		// $response = $this->api(
		//     // '229255610602615/feed',
		//     'me/accounts',
		// 	'GET'
		// );	

		$response = $this->api(
			'229255610602615/feed', // feed | photos
  			'POST',
  			array(
  				// 'message' => 'Yo!',
  				// 'name' => 'Name: Hello',
  				// 'caption' => 'Caption: Yo!',
  				// 'no_story' => true,
  				'message' => $content->content,
  				'picture' => 'http://api-thaimissing.truelife.com/pictures/2014-03-27-8351395914480.png',
  			)
		);

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

		return $response;   				      
	}
}