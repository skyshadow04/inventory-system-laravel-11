<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MechanicalItem extends Model
{
    use HasFactory;

    protected $table = 'items_mech';
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'sr_no',
        'category_name',
        'description',
        'total_qty',
        'precision_measurement_class_1',
        'location',
        'w_18_b',
        'w_17',
        'w_18_a_compressor_area',
        'balance_qty_in_store',
        'remarks',
        'availability',
    ];
}
