<?php

namespace HolartWeb\AxoraCMS\Models\SEO;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TPageVisit extends Model
{
    protected $table = 't_page_visits';

    public $timestamps = false;

    protected $fillable = [
        'page_id',
        'url',
        'route_name',
        'ip_address',
        'user_agent',
        'referer',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    /**
     * Relationship: belongs to page
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(TPage::class, 'page_id');
    }

    /**
     * Record a page visit
     */
    public static function record(
        ?int $pageId,
        string $url,
        ?string $routeName = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
        ?string $referer = null
    ): self {
        return static::create([
            'page_id' => $pageId,
            'url' => $url,
            'route_name' => $routeName,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'referer' => $referer,
            'visited_at' => now(),
        ]);
    }
}
