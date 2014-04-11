<?php namespace Indianajone\Share\Providers;

interface ShareProviderInterface 
{
	public function getAccessToken();

	public function share($input=null);
}