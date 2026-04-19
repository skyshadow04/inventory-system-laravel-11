<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationItem extends Model
{
    use HasFactory;

    protected $table = 'items_ops';
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'sr_no',
        'category_name',
        'item_description',
        'location',
        'venue',
        'barcode',
        'supplier',
        'total_in',
        'total_out',
        'total_return',
        'quantity_in_hand',
        'physical_stock',
        'reconciliation',
        'difference',
        'remarks',
    ];
}
