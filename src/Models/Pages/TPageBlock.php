<?php

namespace HolartWeb\AxoraCMS\Models\Pages;

use Illuminate\Database\Eloquent\Model;

class TPageBlock extends Model
{
    protected $table = 't_page_blocks';

    protected $fillable = [
        'page_id',
        'block_type_id',
        'parent_block_id',
        'container_column',
        'settings',
        'sort',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the page that owns this block
     */
    public function page()
    {
        return $this->belongsTo(TPage::class, 'page_id');
    }

    /**
     * Get the block type
     */
    public function blockType()
    {
        return $this->belongsTo(TPageBlockType::class, 'block_type_id');
    }

    /**
     * Get the parent block (for nested blocks)
     */
    public function parentBlock()
    {
        return $this->belongsTo(TPageBlock::class, 'parent_block_id');
    }

    /**
     * Get child blocks (for container blocks)
     */
    public function childBlocks()
    {
        return $this->hasMany(TPageBlock::class, 'parent_block_id')->orderBy('sort');
    }
}
