<?php

namespace App\Filters;

class MyBookingFilter extends QueryFilter
{
    protected function filters(): array
    {
        return [
            'id' => ['eq', 'gt', 'gte', 'lt', 'lte', 'in'],
            'user_id' => ['eq', 'gt', 'gte', 'lt', 'lte', 'in'],
        ];
    }
}
