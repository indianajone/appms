<?php namespace Max\User\Repository;
 
interface UserRepositoryInterface 
{
	public function validate($action, $input=null);

	public function all();

	public function find($id);

	public function findMany($ids, $columns=array('*'));

	public function findWith($id, $relations);

	public function findUserAndChildren($id);

	public function create($input);

	public function update($id, $input);

	public function delete($id);

	public function children($id);

	public function countChildren($id);
}