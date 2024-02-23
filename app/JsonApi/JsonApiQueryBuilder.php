<?php

namespace App\JsonApi;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class JsonApiQueryBuilder
{
    public function allowedSorts(): Closure
    {
        return function ($allowedSorts) {
            /** @var Builder $this */
            if (request()->filled('sort')) {
                $sortFields = explode(',', request()->input('sort'));

                foreach ($sortFields as $sortField) {
                    $sortDirection = Str::of($sortField)->startsWith('-') ? 'desc' : 'asc';

                    $sortField = ltrim($sortField, '-');

                    abort_unless(in_array($sortField, $allowedSorts), 400);

                    $this->orderBy($sortField, $sortDirection);
                }
            }

            return $this;
        };
    }

    public function jsonPaginate(): Closure
    {
        /** @var Builder $this */
        return function () {
            return $this->paginate(
                $perPage = request('page.size', 15),
                $columns = ['*'],
                $pageName = 'page[number]',
                $page = request('page.number', 1)
            )->appends(request()->only('sort', 'filter','page.size'));
        };
    }

    public function allowedFilters(): Closure
    {
        return function($allowedFilters){
            /** @var Builder $this */
            foreach (request('filter', []) as $filter => $value) {
                abort_unless(in_array($filter, $allowedFilters), 400, "The filter '{$filter}' is not allowed");

                $this->hasNamedScope($filter) ?
                    $this->$filter($value) :
                    $this->where($filter, 'LIKE', "%$value%");
            }

            return $this;
        };
    }
}
