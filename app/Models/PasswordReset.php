<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $email
 * @property string $token
 */
class PasswordReset extends Model
{
    use HasFactory;
    public $timestamps = false;

    public static function create(string $recoveryEmail, string $randomPassword): void
    {
        $newModel = new self();
        $newModel->email = $recoveryEmail;
        $newModel->token = $randomPassword;
        $newModel->save();
    }

    public function tokenExists(string $randomPassword): bool
    {
        $token = self::query()->where('token', $randomPassword)->first();
        return !empty($token);
    }
}
