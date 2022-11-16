<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Plan extends Model
{
    use HasFactory;

/** AVAILABLE PLANS */
    const ESSENTIAL_PLAN = 2;
    const ESSENTIAL_PLAN_LABEL = 'essential';

    const PREMIUM_PLAN = 3;
    const PREMIUM_PLAN_LABEL = 'premium';

/** AVAILABLE FEATURES */
    const STORE_PRODUCTS = 'storeProducts';
    const SELL_PRODUCTS = 'sellProducts';
    const IMPORT_PRODUCTS = 'importProducts';
    const ACCESS_REPORTS = 'reports';
    const CUSTOM_FIELDS = 'customFields';

    const UNLIMITED_PRODUCTS_QUANTITY = 77777;


    public static function getAvailablePlans(): array
    {
        return [
            'essential' => self::ESSENTIAL_PLAN,
            'premium' => self::PREMIUM_PLAN
        ];
    }

    public static function getPlanAttributesBasedOnType(string $planType): array
    {
        $attributesMap = [
            self::ESSENTIAL_PLAN_LABEL => [
                'name' => 'essential',
                'description' => 'Plano essencial',
                'allowed_features' => self::getAllowedFeatures(self::ESSENTIAL_PLAN_LABEL),
                'price' => self::getPlanPrice(self::ESSENTIAL_PLAN_LABEL),
                'max_products_allowed' => self::getMaxProductsQuantity(self::ESSENTIAL_PLAN_LABEL),
                'max_users_allowed' => self::getAllowedUserQuantity(self::ESSENTIAL_PLAN_LABEL)
            ],
            self::PREMIUM_PLAN_LABEL => [
                'name' => 'premium',
                'description' => 'Plano premium , com todas as features e quantidade de usuários ilimitada. ',
                'allowed_features' => self::getAllowedFeatures(self::PREMIUM_PLAN_LABEL),
                'price' => self::getPlanPrice(self::PREMIUM_PLAN_LABEL),
                'max_products_allowed' => self::getMaxProductsQuantity(self::PREMIUM_PLAN_LABEL),
                'max_users_allowed' => self::getAllowedUserQuantity(self::PREMIUM_PLAN_LABEL)
            ],
        ];

        return $attributesMap[$planType];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public static function getAllowedFeatures(string $planType): Collection
    {
        $allowedFeaturesMap = [
            self::ESSENTIAL_PLAN_LABEL => json_encode([self::IMPORT_PRODUCTS, self::ACCESS_REPORTS]),
            self::PREMIUM_PLAN_LABEL => json_encode([self::IMPORT_PRODUCTS, self::ACCESS_REPORTS, self::CUSTOM_FIELDS])
        ];

        return collect($allowedFeaturesMap[$planType]);
    }

    public static function getAllowedUserQuantity(string $planType): int
    {
        $allowedUserQuantityMap = [
            self::ESSENTIAL_PLAN_LABEL => 2,
            self::PREMIUM_PLAN_LABEL => 5
        ];

        return $allowedUserQuantityMap[$planType];
    }

    public static function getMaxProductsQuantity(string $planType): int|string
    {
        $maxProductsQuantityMap = [
            self::ESSENTIAL_PLAN_LABEL => 300,
            self::PREMIUM_PLAN_LABEL => 700
        ];

        return $maxProductsQuantityMap[$planType];
    }

    public static function getPlanPrice(string $planType): int
    {
        $planPricesMap = [
            self::ESSENTIAL_PLAN_LABEL => 6590,
            self::PREMIUM_PLAN_LABEL => 8990
        ];

        return $planPricesMap[$planType];
    }
}
