<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Item;
use App\Models\EngineeringItem;
use App\Models\MechanicalItem;
use App\Models\OperationItem;

class BorrowRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'item_name',
        'item_description',
        'quantity',
        'status',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
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

    public function getItem()
    {
        // Try to find the item in different tables based on the item_id format
        $itemId = $this->item_id;

        // Check APP items first (numeric IDs)
        if (is_numeric($itemId)) {
            $item = Item::where('sr_number', $itemId)->first();
            if ($item) return $item;
        }

        // Check Engineering items (E prefix)
        if (str_starts_with($itemId, 'E')) {
            $item = EngineeringItem::where('sr_number', $itemId)->first();
            if ($item) return $item;
        }

        // Check Mechanical items (ME prefix)
        if (str_starts_with($itemId, 'ME')) {
            $item = MechanicalItem::where('sr_no', $itemId)->first();
            if ($item) return $item;
        }

        // Check Operations items (OP prefix)
        if (str_starts_with($itemId, 'OP')) {
            $item = OperationItem::where('sr_no', $itemId)->first();
            if ($item) return $item;
        }

        // Fallback to original relationship
        return $this->item;
    }

    public function getItemGroup()
    {
        $item = $this->getItem();
        $location = strtolower(trim($item->location ?? ''));

        return match (true) {
            $location === 'app' => 'APP',
            $location === 'engg / ins' => 'Engineering',
            $location === 'engg / mec' => 'Mechanical',
            $location === 'optns' => 'Operations',
            str_contains($location, 'engg') => 'Engineering',
            str_contains($location, 'mec') => 'Mechanical',
            str_contains($location, 'opt') => 'Operations',
            str_contains($location, 'app') => 'APP',
            default => 'APP',
        };
    }
}
