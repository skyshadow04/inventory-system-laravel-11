<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstrumentItem extends Model
{
    use HasFactory;

    protected $table = 'items_instrument';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $fillable = [
        'sr_number',
        'category_name',
        'item_description',
        'location',
        'venue',
        'barcode',
        'make',
        'quantity_in_hand',
        'physical_stock',
        'remarks',
        'availability',
    ];
}
