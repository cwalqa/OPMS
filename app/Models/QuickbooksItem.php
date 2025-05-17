<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuickbooksItem extends Model
{
    use HasFactory;
    protected $table = 'quickbooks_item';
    protected $guarded = [];

    public static function itemInsertUpdate($insert_item_data){
        $data = self::get();
        if(count($data) > 0){
            foreach($insert_item_data as $key => $item_details){
                $result = self::updateOrCreate([                    
                    'item_id' => $item_details['item_id'],
                    ],$item_details);
            }
        }
        else{
            $result = self::insert($insert_item_data);
        }

        return $result;
    }
}


