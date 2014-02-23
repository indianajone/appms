<?php namespace Indianajone\Validators;

use Validator;
	
class CustomValidator extends \Illuminate\Validation\Validator
{	
	use Rules\ExistsInApp;
	use Rules\ExistsLoop;
	use Rules\ExistsOrNull;
	use Rules\UniqueInApp;
}
