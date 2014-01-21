<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class Member extends Eloquent implements UserInterface, RemindableInterface {

//    public $timestamps = false;
//    protected $table = 'users';
    protected $primaryKey = 'member_id';
    
        /**
         * The database table used by the model.
         *
         * @var string
         */
        protected $table = 'members';

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
            return $this->belongsTo('Max\Application\Models\Application', 'app_id', 'id');
        }
        
//        public function missingchild(){
//            return $this->belongsTo('Max\Missingchild\Models\Missingchild');
//        }

}