<?php

namespace App\Http\Controllers\Api\Concerns;

trait AppliesInsensitiveSearch
{
    /**
     * @param  \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>  $query
     */
    protected function whereInsensitive($query, string $column, string $value): void
    {
        $pattern = '%'.$value.'%';

        if ($query->getConnection()->getDriverName() === 'pgsql') {
            $query->where($column, 'ilike', $pattern);

            return;
        }

        $query->whereRaw('LOWER('.$column.') LIKE ?', [mb_strtolower($pattern)]);
    }
}
