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
        'profile_photo', // Added profile_photo to fillable
    ];
    protected $attributes = [
        'role' => 'judge',
    ];
    protected $hidden = [
        'password',
        'remember_token',
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
}