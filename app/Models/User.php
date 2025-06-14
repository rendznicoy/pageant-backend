<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'google_id',
        'password',
        'role',
        'email_verified_at',
        'profile_photo', // Keep for backward compatibility
        'profile_photo_url', // New Cloudinary URL
        'profile_photo_public_id', // New Cloudinary public ID
    ];
    
    protected $attributes = [
        'role' => 'judge',
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
        'profile_photo_public_id', // Hide sensitive Cloudinary data
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function events()
    {
        return $this->hasMany(Event::class, 'created_by', 'user_id');
    }

    public function judge()
    {
        return $this->hasOne(Judge::class, 'user_id', 'user_id');
    }

    public function getAuthIdentifierName()
    {
        return 'user_id';
    }

    public function getAuthIdentifier()
    {
        return $this->user_id;
    }

    /**
     * Check if user has a Cloudinary profile photo
     */
    public function hasCloudinaryPhoto(): bool
    {
        return !empty($this->profile_photo_url) && !empty($this->profile_photo_public_id);
    }

    /**
     * Get the best available profile photo URL
     */
    public function getProfilePhotoUrlAttribute(): ?string
    {
        return $this->attributes['profile_photo_url'] ?? null;
    }
}