<?php namespace Indianajone\RolesAndPermissions;

use Illuminate\Support\Facades\Facade;
use Zizaco\Entrust\Entrust;

class RolesAndPermissions extends Entrust
{
	function __construct($app)
	{
		parent::__construct($app);
	}
}