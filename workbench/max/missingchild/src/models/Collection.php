<?php 
namespace Max\Missingchild\Models;
use Illuminate\Support\Collection as BaseCollection;

class Collection extends BaseCollection
{

    public function members()
    {
//        $members = new BaseCollection();
        
//        foreach ($this->items as $mcs) {
//            echo $mcs;
//            foreach ($mcs->members() as $mc) {
////                var_dump($mc);
//                $members->add($mc);
//            }
//        }

//        return $members;
        
        return new BaseCollection($this->combine($this->items));
    }
    
    private function combine($result) {
        $new = [];
        
        if(is_array($result))
        {
            while(list($key, $value) = each($result))
            {                
                $temp = array('parent_id', 'fbid', 'fbtoken', 'username', 'title', 'first_name', 'last_name', 'other_name', 'phone',
                    'mobile', 'verified', 'email', 'address', 'gender', 'birthday', 'description', 'type', 'created_at',
                    'updated_at', 'last_seen', 'status');
                $value->setHidden(array('member'));
                foreach ($temp as $t)
                {
                    $value[$t] = $value['member'][$t];
                }
                $new[] = $value;
            }
        }
        
        return $new;
    }
}