<?php

namespace Workdo\Requests\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\Requests\Entities\RequestCategory;
use Workdo\Requests\Entities\RequestSubcategory;

class Requests extends Model
{
    use HasFactory;

    protected $fillable = ['name','code','category_id','subcategory_id','active','module_type','is_converted','layouts','theme_color','workspace','created_by'];

    protected static function newFactory()
    {
        return \Workdo\Requests\Database\factories\RequestFactory::new();
    }

    public function Requestcategory(){
        return $this->hasOne(RequestCategory::class, 'id', 'category_id');
    }
    public function RequestSubcategory(){
        return $this->hasOne(RequestSubcategory::class, 'id', 'subcategory_id');
    }

    public function RequestFormField()
    {
        return $this->hasMany(RequestFormField::class, 'request_id', 'id');
    }
    public function response()
    {
        return $this->hasMany(RequestResponse::class, 'request_id', 'id');
    }
    public function fieldResponse()
    {
        return $this->hasOne(RequestConvertData::class, 'request_id', 'id');
    }

    public static $module_type =['Lead'=>'Lead'];

    public static function themeOne()
    {
        $arr = [];

        $arr = [
            'Formlayout1' => [
                'Formlayout1-v1' => [
                    'img_path' => asset('packages/workdo/Requests/src/Resources/assets/form/Formlayout1/images/form.png'),
                    'color' => '#3CA295',
                    'theme_name' => 'Formlayout1-v1'
                ],
                'Formlayout1-v2' => [
                    'img_path' => asset('packages/workdo/Requests/src/Resources/assets/form/Formlayout1/images/form2.png'),
                    'color' => '#7469B6',
                    'theme_name' => 'Formlayout1-v2'
                ],
                'Formlayout1-v3' => [
                    'img_path' => asset('packages/workdo/Requests/src/Resources/assets/form/Formlayout1/images/form3.png'),
                    'color' => '#944E63',
                    'theme_name' => 'Formlayout1-v3'
                ],
                'Formlayout1-v4' => [
                    'img_path' => asset('packages/workdo/Requests/src/Resources/assets/form/Formlayout1/images/form4.png'),
                    'color' => '#114232',
                    'theme_name' => 'Formlayout1-v4'
                ],
                'Formlayout1-v5' => [
                    'img_path' => asset('packages/workdo/Requests/src/Resources/assets/form/Formlayout1/images/form5.png'),
                    'color' => '#EE7214',
                    'theme_name' => 'Formlayout1-v5'
                ],
            ],
            'Formlayout2' => [
                'Formlayout2-v1' => [
                    'img_path' => asset('packages/workdo/Requests/src/Resources/assets/form/Formlayout2/images/form.png'),
                    'color' => '#0075FE',
                    'theme_name' => 'Formlayout2-v1'
                ],
                'Formlayout2-v2' => [
                    'img_path' => asset('packages/workdo/Requests/src/Resources/assets/form/Formlayout2/images/form2.png'),
                    'color' => '#FF407D',
                    'theme_name' => 'Formlayout2-v2'
                ],
                'Formlayout2-v3' => [
                    'img_path' => asset('packages/workdo/Requests/src/Resources/assets/form/Formlayout2/images/form3.png'),
                    'color' => '#EE6B4D',
                    'theme_name' => 'Formlayout2-v3'
                ],
                'Formlayout2-v4' => [
                    'img_path' => asset('packages/workdo/Requests/src/Resources/assets/form/Formlayout2/images/form4.png'),
                    'color' => '#634A00',
                    'theme_name' => 'Formlayout2-v4'
                ],
                'Formlayout2-v5' => [
                    'img_path' => asset('packages/workdo/Requests/src/Resources/assets/form/Formlayout2/images/form5.png'),
                    'color' => '#00254E',
                    'theme_name' => 'Formlayout2-v5'
                ],
            ],
            'Formlayout3' => [
                'Formlayout3-v1' => [
                    'img_path' => asset('packages/workdo/Requests/src/Resources/assets/form/Formlayout3/images/form.png'),
                    'color' => '#2760A7',
                    'theme_name' => 'Formlayout3-v1'
                ],
                'Formlayout3-v2' => [
                    'img_path' => asset('packages/workdo/Requests/src/Resources/assets/form/Formlayout3/images/form2.png'),
                    'color' => '#59522C',
                    'theme_name' => 'Formlayout3-v2'
                ],
                'Formlayout3-v3' => [
                    'img_path' => asset('packages/workdo/Requests/src/Resources/assets/form/Formlayout3/images/form3.png'),
                    'color' => '#F2A30F',
                    'theme_name' => 'Formlayout3-v3'
                ],
                'Formlayout3-v4' => [
                    'img_path' => asset('packages/workdo/Requests/src/Resources/assets/form/Formlayout3/images/form4.png'),
                    'color' => '#6420AA',
                    'theme_name' => 'Formlayout3-v4'
                ],
                'Formlayout3-v5' => [
                    'img_path' => asset('packages/workdo/Requests/src/Resources/assets/form/Formlayout3/images/form5.png'),
                    'color' => '#3D5B81',
                    'theme_name' => 'Formlayout3-v5'
                ],
            ]


        ];

        return $arr;
    }

}
