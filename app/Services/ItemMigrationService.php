<?php

namespace App\Services;

use App\Models\TempItemInfo;
use App\Models\MainItemInfos;
use App\Models\ItemUniqueInfo;
use App\Models\ItemPrice;
use App\Models\ItemMedia;
use Illuminate\Support\Facades\DB;

class ItemMigrationService
{
    /**
     * Move an item from temp_item_infos to main_item_infos
     *
     * @param  int  $tempId
     * @return MainItemInfos|null
     */
    public function migrate(int $tempId): ?MainItemInfos
    {
        return DB::transaction(function () use ($tempId) {
            // 1. Find temp item
            $tempItem = TempItemInfo::find($tempId);
            if (!$tempItem) {
                return null;
            }

            // 2. Validation: must have price + media for this item_code
            $hasPrice = ItemPrice::where('item_code', $tempItem->item_code)->exists();
            $hasMedia = ItemMedia::where('item_code', $tempItem->item_code)->exists();

            if (!$hasPrice || !$hasMedia) {
                throw new \Exception("Please fill the price and media info before migration.");
            }

            // 3. Check if same item_code already exists in main_item_infos
            $exists = MainItemInfos::where('item_code', $tempItem->item_code)->exists();

            // 4. Insert into main_item_infos
            $mainItem = MainItemInfos::create($tempItem->toArray());

            // 5. If this is the first record, set item_primary_code in ItemUniqueInfo
            if (!$exists) {
                ItemUniqueInfo::where('item_unique_id', $tempItem->item_unique_id)
                    ->update([
                        'item_primary_code' => $tempItem->item_code
                    ]);
            }

            // 6. Delete temp record
            $tempItem->delete();

            return $mainItem;
        });
    }
}
