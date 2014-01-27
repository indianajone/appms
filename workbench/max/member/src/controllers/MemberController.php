<?php
namespace Max\Member\Controllers;

use Validator, Input, Response;
use Indianajone\Applications\Application;
use Max\Member\Models\Member;

class MemberController extends \BaseController {

 	public function index()
    {

    }

    public function create()
	{
		$this->store();
	}

	public function store()
    {
 
    }

    public function show($id)
	{

	}

	public function edit($id)
    {
    	$this->update($id);
    }

    public function update($id)
    {

    }

    public function delete($id)
    {
        return $this->destroy($id);
    }

    public function destroy($id)
    {

    }

}