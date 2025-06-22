<?php

namespace App\Filters;

class InclusionFilter extends QueryFilter
{
    protected function filters(): array
    {
        return [
            'id' => ['eq', 'gt', 'gte', 'lt', 'lte', 'in'],
            'inclusion_name' => ['like', 'eq'],
        ];
    }
}
