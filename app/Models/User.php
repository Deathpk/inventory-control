<?php

namespace App\Models;

use App\Models\Scopes\FilterTenant;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * @property string $name
 * @property Company $company
 * @property string $email
 * @property bool $mustChangePassword
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

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

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function checkRolePermission(string $permission): bool
    {
        return in_array($permission, $this->role->getRolePermissions());
    }

    public function fromArray(array $data): self
    {
        $this->name = $data['name'] ?? $this->name;
        $this->email = $data['email'] ?? $this->email;
        $this->password = bcrypt($data['password']) ?? $this->password;
        $this->mustChangePassword = $data['mustChangePassword'] ?? false;
        $this->company_id = $data['companyId'] ?? $this->company_id;
        $this->role_id = $data['roleId'] ?? $this->role_id;
        $this->save();

        return $this;
    }

    public function updateFromArray(array $data): void
    {
        $this->name = $data['name'] ?? $this->name;
        $this->email = $data['email'] ?? $this->email;
        $this->role_id = $data['roleId'] ?? $this->role_id;
        $this->save();
    }

    public function isEmailVerified(): bool
    {
        return $this->hasVerifiedEmail();
    }

    public function mustChangePassword(): bool
    {
        return $this->mustChangePassword;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getRoleId(): int
    {
        return $this->role_id;
    }

    public function revokeLogedToken(): void
    {
        PersonalAccessToken::query()
        ->where('tokenable_id', '=', $this->getId())
        ->where('tokenable_type', User::class)
        ->delete();
    }

    public function changePassword(string $newPassword, bool $isRecoveryPasswordRequest = false): void
    {
        $this->password = bcrypt($newPassword);
        $this->mustChangePassword = $isRecoveryPasswordRequest;
        $this->save();
    }

    public function getGeneralInfo(): array
    {
        return [
            'user' => $this->only(['id', 'name', 'email', 'mustChangePassword', 'role_id']),
            'company' => $this->company->only(['id', 'name', 'cnpj', 'email', 'active', 'plan_id'])
        ];
    }

    public function getRoleLabel(): string
    {
        return $this->role()
        ->first()
        ->getLabel();
    }
}
