<?php namespace Indianajone\Share\Providers;

interface ShareProviderInterface 
{
	public function getAccessToken();

	public function getModel($type);

	public function share($input=null);
}