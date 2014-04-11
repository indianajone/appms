<?php 

Interface AbstractRepositoryInterface 
{
	public function validate($action, $input=null);

	public function all();

	public function find($id);

	public function count();

	public function create($input);

	public function update($id, $input);

	public function delete($id);

	public function errors();
}