<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use Max\User\Models\BaseModel;

class User extends BaseModel implements UserInterface, RemindableInterface {

//    public $timestamps = false;
//    protected $table = 'users';
//    protected $primaryKey = 'user_id';
    
        /**
         * The database table used by the model.
         *
         * @var string
         */
        protected $table = 'users';

        /**
         * The attributes excluded from the model's JSON form.
         *
         * @var array
         */
        protected $hidden = array('password');

        /**
         * Get the unique identifier for the user.
         *
         * @return mixed
         */
        public function getAuthIdentifier()
        {
                return $this->getKey();
        }

        /**
         * Get the password for the user.
         *
         * @return string
         */
        public function getAuthPassword()
        {
                return $this->password;
        }

        /**
         * Get the e-mail address where password reminders are sent.
         *
         * @return string
         */
        public function getReminderEmail()
        {
                return $this->email;
        }
        
//        protected function getDateFormat()
//        {
//            return 'U';
//        }

        public function apps(){
            return $this->belongsTo('Max\Application\Models\Application', 'id', 'user_id');
        }
        
        public function missingchild(){
            return $this->belongsTo('Max\Missingchild\Models\Missingchild');
        }

}