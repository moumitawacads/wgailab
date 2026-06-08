<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessPlan extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'description', 'media_url', 'media_type'];

    public function sessionLinks()
    {
        return $this->hasMany(SessionDomeworkBusinessPlan::class, 'businessplan_id');
    }

    public function detectMediaType()
    {
        if ($this->media_url) {
            $extension = pathinfo($this->media_url, PATHINFO_EXTENSION);
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];

            if (in_array(strtolower($extension), $imageExtensions)) {
                return 'image';
            } elseif (
                strpos($this->media_url, 'youtube.com') !== false ||
                strpos($this->media_url, 'youtu.be') !== false ||
                strpos($this->media_url, 'vimeo.com') !== false ||
                strtolower($extension) == 'mp4'
            ) {
                return 'video';
            }
        }
        return null;
    }

    // Get YouTube video ID
    public function getYoutubeId()
    {
        if (strpos($this->media_url, 'youtube.com') !== false) {
            parse_str(parse_url($this->media_url, PHP_URL_QUERY), $params);
            return $params['v'] ?? null;
        } elseif (strpos($this->media_url, 'youtu.be') !== false) {
            return trim(parse_url($this->media_url, PHP_URL_PATH), '/');
        }
        return null;
    }
}
