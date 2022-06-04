<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    const FREE_PLAN = 1;
    const ESSENTIAL_PLAN = 2;
    const PREMIUM_PLAN = 3;

    public static function getAvailablePlans(): array
    {
        return [
            'free' => self::FREE_PLAN,
            'essential' => self::ESSENTIAL_PLAN,
            'premium' => self::PREMIUM_PLAN
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }
}
