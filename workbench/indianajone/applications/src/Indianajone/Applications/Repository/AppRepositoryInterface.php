<?php namespace Indianajone\Applications;

interface AppRepositoryInterface extends \AbstractRepositoryInterface
{
	public function findByKey($key);

	public function updateMeta($id, $attributes=array());
}