<?php

namespace App\Filters;

class RoomFilter extends QueryFilter
{
    protected function filters(): array
    {
        return [
            'id' => ['eq', 'gt', 'gte', 'lt', 'lte', 'in'],
            'room_name' => ['like', 'eq'],
            'is_already_check_in' => ['eq', 'ne'],
        ];
    }
}
