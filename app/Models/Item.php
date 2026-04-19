<?php

namespace App\Models;

use App\Models\BorrowHistory;
use App\Models\BorrowRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Item extends Model
{
    use HasFactory;

    protected $primaryKey = 'sr_number';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'sr_number',
        'category_name',
        'item_description',
        'venue',
        'location',
        'barcode',
        'supplier',
        'total_in',
        'total_out',
        'total_return',
        'quantity_in_hand_current',
        'physical_stock',
        'reconciliation',
        'difference',
        'remarks',
        'availability'
    ];

    public function borrowHistories(): HasMany
    {
        return $this->hasMany(BorrowHistory::class);
    }

    public function borrowRequests(): HasMany
    {
        return $this->hasMany(BorrowRequest::class);
    }
}
