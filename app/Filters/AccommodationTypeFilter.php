<?php

namespace App\Filters;

class AccommodationTypeFilter extends QueryFilter
{
    protected function filters(): array
    {
        return [
            'id' => ['eq', 'gt', 'gte', 'lt', 'lte', 'in'],
            'accommodation_type_name' => ['like', 'eq'],
        ];
    }
}
