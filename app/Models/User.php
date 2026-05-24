<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path',
        'country',
        'target_band_score',
        'exam_type',
        'exam_date',
        'first_name',
        'last_name',
        'gemini_api_key',   // User's personal Gemini API key for AI evaluation
    ];

    /**
     * The attributes that should be hidden for serialization.
     * gemini_api_key is hidden so it is never exposed in JSON responses.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'gemini_api_key',   // Never expose API keys in API responses
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'exam_date' => 'date',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function testAttempts()
    {
        return $this->hasMany(TestAttempt::class);
    }

    public function isAdmin(): bool
    {
        return $this->roles()->where('name', 'Admin')->exists();
    }

    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function writingAnswers()
    {
        return $this->hasMany(WritingAnswer::class);
    }
}
