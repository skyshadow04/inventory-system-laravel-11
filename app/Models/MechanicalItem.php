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
        'physical_stock',
        'remarks',
        'availability',
    ];

    // Accessors to map database columns to expected attribute names
    public function getSrNumberAttribute()
    {
        return $this->sr_no;
    }

    public function getItemDescriptionAttribute()
    {
        return $this->description;
    }

    public function getPhysicalStockAttribute()
    {
        return $this->balance_qty_in_store;
    }

    public function setPhysicalStockAttribute($value)
    {
        $this->attributes['balance_qty_in_store'] = $value;
    }

    public function getQuantityInHandCurrentAttribute()
    {
        return $this->balance_qty_in_store;
    }

    public function setQuantityInHandCurrentAttribute($value)
    {
        $this->attributes['balance_qty_in_store'] = $value;
    }

    public function getSupplierAttribute()
    {
        return null;  // Mechanical items don't have supplier data
    }

    public function getVenueAttribute()
    {
        return null;  // Mechanical items don't have venue data
    }

    public function getBarcodeAttribute()
    {
        return null;  // Mechanical items don't have barcode data
    }
}
