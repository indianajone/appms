<?php namespace Max\User\Repository;
 
interface UserRepositoryInterface extends \AbstractRepositoryInterface
{
	public function findMany($ids, $columns=array('*'));

	public function findWith($id, $relations);

	public function findUserAndChildren($id);

	public function children($id);

	public function countChildren($id);
}