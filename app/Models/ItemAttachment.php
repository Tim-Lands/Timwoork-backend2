<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemAttachment extends Model
{
    use HasFactory;
    protected $table = "item_attachments";
    protected $appends = ['full_path'];



    /* --------------------------------accesor and mutators */

    public function getFullPathAttribute()
    {
        //return url("resources_files/{$this->name}");
        return 'https://timwoork-space.ams3.digitaloceanspaces.com/resources_files/' . $this->name;
    }
    /* -------------------------------- Relations ------------------------------- */

    /**
     * item
     *
     * @return BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
