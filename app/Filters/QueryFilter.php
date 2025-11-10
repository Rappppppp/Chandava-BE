<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

abstract class QueryFilter
{
    protected Request $request;
    protected $builder;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    abstract protected function filters(): array;

    protected function isFilterable(string $field): bool
    {
        return array_key_exists($field, $this->filters());
    }

    protected function isAllowedOperator(string $field, string $operator): bool
    {
        return in_array($operator, $this->filters()[$field] ?? []);
    }

    public function apply($builder)
    {
        $this->builder = $builder;

        $invalidFields = [];

        foreach ($this->request->except('or') as $field => $condition) {
            if (!$this->isFilterable($field)) {
                $invalidFields[] = $field;
                continue;
            }

            if (is_array($condition)) {
                foreach ($condition as $operator => $value) {
                    if (!$this->isAllowedOperator($field, $operator)) {
                        $invalidFields[] = "{$field}[{$operator}]";
                    } else {
                        $this->applyOperator($field, $operator, $value, 'and');
                    }
                }
            } else {
                $this->builder->where($field, $condition);
            }
        }

        if (!empty($invalidFields)) {
            throw ValidationException::withMessages([
                'filters' => ["Invalid filters: " . implode(', ', $invalidFields)],
            ]);
        }

        return $this->builder;
    }

    protected function applyOperator($field, $operator, $value, $boolean = 'and', $query = null)
    {
        $query = $query ?: $this->builder;
        $method = $boolean === 'or' ? 'orWhere' : 'where';

        // 🔧 Special handling for soft deletes
        if ($field === 'deleted_at') {
            if ($operator === 'eq' && $value == 0) {
                return $query->{$boolean === 'or' ? 'orWhereNull' : 'whereNull'}('deleted_at');
            }

            if ($operator === 'ne' && $value == 0) {
                return $query->{$boolean === 'or' ? 'orWhereNotNull' : 'whereNotNull'}('deleted_at');
            }
        }

        $operatorMap = [
            'eq' => '=',
            'ne' => '!=',
            'gt' => '>',
            'gte' => '>=',
            'lt' => '<',
            'lte' => '<=',
        ];

        if (isset($operatorMap[$operator])) {
            return $query->{$method}($field, $operatorMap[$operator], $value);
        }

        if ($operator === 'between') {
            $values = explode(',', $value);
            if (count($values) === 2) {
                return $query->{$boolean === 'or' ? 'orWhereBetween' : 'whereBetween'}($field, $values);
            }
        }

        if ($operator === 'like') {
            return $query->{$method}($field, 'like', "%{$value}%");
        }

        if ($operator === 'in') {
            $values = is_array($value) ? $value : explode(',', $value);
            return $query->{$boolean === 'or' ? 'orWhereIn' : 'whereIn'}($field, $values);
        }

        if ($operator === 'not_in') {
            $values = is_array($value) ? $value : explode(',', $value);
            return $query->{$boolean === 'or' ? 'orWhereNotIn' : 'whereNotIn'}($field, $values);
        }

        if ($operator === 'is_null') {
            return $query->{$boolean === 'or' ? 'orWhereNull' : 'whereNull'}($field);
        }

        if ($operator === 'not_null') {
            return $query->{$boolean === 'or' ? 'orWhereNotNull' : 'whereNotNull'}($field);
        }

        return $query;
    }
}
