<?php

namespace App\Filters;

class ContactUsFormFilter extends QueryFilter
{
    protected function filters(): array
    {
        return [
            'id' => ['eq', 'gt', 'gte', 'lt', 'lte', 'in'],
            'first_name' => ['like', 'eq'],
            'last_name' => ['like', 'eq'],
            'email' => ['like', 'eq'],
            'subject' => ['like', 'eq'],
            'message' => ['like', 'eq'],
        ];
    }
}
