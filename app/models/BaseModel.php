<?php

	/**
	* Base Model
	*/

	class BaseModel extends Eloquent
	{
		/** 
		 * Override getDateFormat to unixtime stamp.
		 * @return String
		 */
		protected function getDateFormat()
	    {
	        return 'U';
	    }
	}