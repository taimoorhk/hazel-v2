<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElderlyProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'temporary_role',
        'status',
        'associated_account_email',
        'preferred_voice',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the associated user account.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'associated_account_email', 'email');
    }

    /**
     * Get the temporary role for this profile.
     * Always returns 'elderly user' as specified.
     */
    public function getTemporaryRoleAttribute($value)
    {
        return 'elderly user';
    }
}
