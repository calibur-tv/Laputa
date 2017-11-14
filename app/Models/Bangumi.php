<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Bangumi
 * @package App\Models
 */
class Bangumi extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'summary',
        'avatar',
        'banner',
        'alias',
        'season',
        'released_at',
        'released_video_id',
        'published_at',
        'count_like',
        'count_score',
        'collection_id'
    ];

    protected $casts = [
        'released_at' => 'integer',
        'released_video_id' => 'integer',
        'published_at' => 'integer'
    ];

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function getAvatarAttribute($avatar)
    {
        return config('website.cdn').($avatar ? $avatar : 'avatar');
    }

    public function getBannerAttribute($banner)
    {
        return config('website.cdn').($banner ? $banner : 'B-banner');
    }
}
