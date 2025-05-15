<?php

namespace Workdo\SupportTicket\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KnowledgeBase extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'category', 'workspace_id','created_by'
    ];


    public function getCategoryInfo()
    {
        $th = $this->hasOne('Workdo\SupportTicket\Entities\KnowledgeBaseCategory', 'id', 'category');
        return $th;
    }
    public static function knowlege_details($id)
    {
        $knowledge = KnowledgeBaseCategory::where('id', $id)->first();

        if ($knowledge) {
            return $knowledge->title;
        }
    }

    public static function category_count($id)
    {
        $knowledge = KnowledgeBase::where('category', $id)->count();
        if ($knowledge) {
            return $knowledge;
        }
    }
}
