<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DigitalCard extends Model
{
    use HasFactory;


    protected $fillable = [
        'dcard_id',
        'user_id',
        'is_active',
        'profile_image',
        'brand_banner',
        'first_name',
        'last_name',
        'job_title',
        'company_name',
        'address',
        'contact_informations',
        'promotional_content',
        'testimonials',
        'presskit',
        'social_links',
        'cxm_link',
        'theme_setting'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'contact_informations' => 'array',
        'promotional_content' => 'array',
        'testimonials' => 'array',
        'social_links' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Get full name
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    // Get profile image URL
    public function getProfileImageUrlAttribute()
    {
        if ($this->profile_image) {
            return asset('storage/digital_cards/profile_images/' . $this->profile_image); //Storage::url('digital_cards/profile_images/' . $this->profile_image);
        }
        return null;
    }

    public function getBrandBannerUrlAttribute()
    {
        if ($this->brand_banner) {
            return asset('storage/digital_cards/brand_banners/' . $this->brand_banner); //Storage::url('digital_cards/brand_banners/' . $this->brand_banner);
        }
        return null;
    }

    // Get promotional image URL
    public function getPromotionalImageUrlAttribute()
    {
        $promotional = $this->promotional_content;
        if (!empty($promotional['promotional_image_uploaded'])) {
            return asset('storage/digital_cards/promotional_images/' . $promotional['promotional_image_uploaded']); //Storage::url('digital_cards/promotional_images/' . $promotional['promotional_image_uploaded']);
        }
        return null;
    }

    // Scope for published cards
    public function scopePublished($query)
    {
        return $query->where('is_published', true)->where('is_active', true);
    }
}
