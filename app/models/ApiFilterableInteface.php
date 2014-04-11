<?php

interface ApiFilterableInteface 
{
	public function getDefaults();
	public function setDefaults($value);

	public function getMultiple();
	public function setMultiple($value);

	public function getDelimiter();
	public function setDelimiter($value);

	public function getMap();
	public function setMap($value);
}