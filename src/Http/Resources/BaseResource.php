<?php

namespace Dbaeka\StripePayment\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->addDefaultValues([
            "data" => $this->resource,
        ]);
    }

    /**
     * @param array<string, mixed> $values
     * @return array<string, mixed>
     */
    protected function addDefaultValues(array $values): array
    {
        return array_merge([
            "success" => 1,
            "error" => null,
            "errors" => [],
        ], $values);
    }
}
