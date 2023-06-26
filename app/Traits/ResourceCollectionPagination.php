<?php

namespace App\Traits;

use Illuminate\Pagination\LengthAwarePaginator;

trait ResourceCollectionPagination
{
    /**
     * overriding static function collection inside
     * Illuminate\Http\Resources\Json\JsonResource
     * that we need to convert pagination to our format
     *
     * @param [type] $resource
     *
     * @return void
     */
    public static function collection($resource)
    {
        $resource = parent::collection($resource);
        // dd($resource->resource);
        if ($resource->resource instanceof LengthAwarePaginator) {
            $result['pagination']['total'] = $resource->total();
            $result['pagination']['offset'] = $resource->perPage();
            $result['pagination']['current'] = $resource->currentpage();
            $result['pagination']['last'] = $resource->lastPage();
            $result['pagination']['next'] = (string) $resource->nextPageUrl();
            $result['pagination']['prev'] = (string) $resource->previousPageUrl();
            $result['data'] = parent::collection($resource->all());
        } else {
            $result = parent::collection($resource);
        }
        return $result;
    }

}
