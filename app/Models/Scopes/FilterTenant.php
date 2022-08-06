<?php

namespace App\Models\Scopes;

use App\Traits\UsesLoggedEntityId;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class FilterTenant implements Scope
{
    use UsesLoggedEntityId;

    public function apply(Builder $builder, Model $model)
    {
        $builder->where('company_id', self::getLoggedCompanyId());
    }
}
