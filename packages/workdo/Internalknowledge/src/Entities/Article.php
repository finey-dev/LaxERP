<?php

namespace Workdo\Internalknowledge\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\Internalknowledge\Entities\Book;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'book',
        'title',
        'description',
        'type',
        'content',
        'created_by',
        'workspace',
        'post_id',

    ];

    protected static function newFactory()
    {
        return \Workdo\Internalknowledge\Database\factories\ArticleFactory::new();
    }

    public function book_name()
    {
        return $this->hasOne(Book::class, 'id', 'book');
    }
}
