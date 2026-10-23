<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Sprs\Office;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'office_id',
        'is_active',
        'name',
        'email',
        'avatar',
        'phone_number',
        'password',
        'role',        // ← Add this
        'department',  // ← Add this
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
        ];
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isStatistician()
    {
        return $this->role === 'statistician';
    }

    // ==================== SPRS ====================

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    /**
     * Single source of truth for "what can this account do", used by
     * middleware instead of querying role_types directly.
     *
     * - Super Admin / LMI's admin / statistician -> comes straight from
     *   the `role` column (explicit values, not part of the office hierarchy)
     * - JPO / Peso_City / Peso_Municipality / Peso_Province / FO / RO ->
     *   derived through office_id -> offices.office_type -> role_types.name
     *
     * Never store this on the users table — it's always computed live,
     * so it can never drift out of sync with the actual office assignment.
     */
    public function getAccessRoleAttribute(): ?string
    {
        if ($this->role) {
            return $this->role;
        }

        return $this->office?->officeType?->name;
    }
}