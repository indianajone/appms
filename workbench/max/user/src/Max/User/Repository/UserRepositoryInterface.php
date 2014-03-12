<?php namespace Max\User\Repository;
 
interface UserRepositoryInterface 
{
	public function all();

	public function find($id);
}