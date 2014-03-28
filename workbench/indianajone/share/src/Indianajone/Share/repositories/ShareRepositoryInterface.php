<?php namespace Indianajone\Share\Repositories;
 
interface ShareRepositoryInterface extends \AbstractRepositoryInterface
{
	public function validate($action, $input=null);
}