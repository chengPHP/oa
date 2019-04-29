<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    public static function getSpaceTreeData(){
        $data = self::where('status',1)->get()->toArray();
        return formatTreeData($data,$id = "id", $parent_id = "pid");
    }
}
