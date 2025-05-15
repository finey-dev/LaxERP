<?php

namespace Workdo\SalesAgent\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Workdo\ProductService\Entities\ProductService;

class ProgramItems extends Model
{
    use HasFactory;
    protected $table = 'sales_agents_program_items';

    protected $fillable = [
        'id',
        'program_id',
        'product_type',
        'items',
        'from_amount',
        'to_amount',
        'discount',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function productService()
    {
        return $this->belongsTo(ProductService::class, 'items');
    }
}
