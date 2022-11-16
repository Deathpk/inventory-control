<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property string $name
 * @property Company $company
 * @property string $email
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id',
        'role_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function create(): self
    {
        return new self();
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function role(): HasOne
    {
        return $this->hasOne(Role::class);
    }

    public function fromArray(array $data): self
    {
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->password = bcrypt($data['password']);
        $this->company_id = $data['companyId'];
        $this->role_id = $data['roleId'];
        $this->save();

        return $this;
    }

    public function isEmailVerified(): bool
    {
        return $this->hasVerifiedEmail();
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
