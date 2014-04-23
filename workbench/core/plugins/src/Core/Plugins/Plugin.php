<?php namespace Core\Plugins;

use Core\Plugins\Exception\Plugin as Exception;

class Plugin 
{
	protected $plugins = array();

	public function all()
	{
		return $this->plugins;
	}

	public function get($name)
	{
		if(array_key_exists($name, $this->plugins))
		{
			return $this->plugins[$name];
		}

		throw new Exception("Can't find [ {$name} ]");
	}

	public function register($name, $model)
	{
		$this->plugins[$name] = new $model;
	}
}