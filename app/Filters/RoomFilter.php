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
            'deleted_at' => ['eq', 'ne'],
        ];
    }

    /**
     * Handle deleted_at filter manually.
     */
    protected function deleted_at($operator, $value)
    {
        if ($operator === 'eq' && $value == 0) {
            return $this->builder->whereNull('deleted_at');
        }

        if ($operator === 'ne' && $value == 0) {
            return $this->builder->whereNotNull('deleted_at');
        }

        // fallback to generic behavior if another operator is used
        return $this->builder->where('deleted_at', $operator, $value);
    }
}
