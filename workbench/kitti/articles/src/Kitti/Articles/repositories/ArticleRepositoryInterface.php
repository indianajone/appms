<?php namespace Kitti\Articles\Repositories;
 
interface ArticleRepositoryInterface extends \AbstractRepositoryInterface
{
	public function validate($action, $input=null);
}