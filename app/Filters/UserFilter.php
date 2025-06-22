<?php

namespace App\Filters;

class UserFilter extends QueryFilter
{
    protected function filters(): array
    {
        return [
            'id' => ['eq', 'gt', 'gte', 'lt', 'lte', 'in'],
            'first_name' => ['like', 'eq'],
            'last_name' => ['like', 'eq'],
            'email' => ['like', 'eq'],
            'role' => ['eq', 'in'],
        ];
    }
}
