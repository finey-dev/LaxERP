<?php

namespace Workdo\Requests\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\Requests\Entities\RequestCategory;


class RequestSubcategory extends Model
{
    use HasFactory;

    protected $fillable = ['name','category_id','created_by','workspace'];

    protected static function newFactory()
    {
        return \Workdo\Requests\Database\factories\RequestSubcategoryFactory::new();
    }




    protected function category(){
        return $this->hasOne(RequestCategory::class, 'id', 'category_id');

    }
}
