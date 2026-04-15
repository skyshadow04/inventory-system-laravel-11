<?php

namespace App\Models;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BorrowHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'item_name',
        'item_description',
        'count',
        'borrowed_at',
        'returned_at',
        'return_status',
        'return_requested_at',
        'admin_return_notes',
    ];

    protected function casts(): array
    {
        return [
            'borrowed_at' => 'datetime',
            'returned_at' => 'datetime',
            'return_requested_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id', 'sr_number');
    }
}
