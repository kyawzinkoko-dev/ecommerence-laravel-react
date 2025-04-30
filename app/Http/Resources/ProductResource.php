<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public static $wrap = false;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'image' => $this->getFirstMediaUrl('images'),
            'images' => $this->getMedia('images')->map(function ($image) {
                return [
                    'id' => $image->id,
                    'thumb' => $image->getUrl('thumb'),
                    'small' => $image->getUrl('small'),
                    'large' => $image->getUrl('large'),
                ];
            }),
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name
            ],
            'department' => [
                'id' => $this->department->id,
                'name' => $this->department->name
            ],
            'variationTypes' => $this->variationTypes->map(function ($type) {
                return [
                    'id' => $type->id,
                    'name' => $type->name,
                    'type' => $type->type,

                    'options' => $type->options->map(function ($option) {
                        return [
                            'id' => $option->id,
                            'name' => $option->name,
                            'images' => $option->getMedia('images')->map(function ($image) {
                                return [
                                    'id' => $image->id,
                                    'thumb' => $image->getUrl('thumb'),
                                    'small' => $image->getUrl('small'),
                                    'large' => $image->getUrl('large'),
                                ];
                            })
                        ];
                    })
                ];
            }),

            'variations' => $this->variations->map(function ($variation) {
                return [
                    'id' => $variation->id,
                    'variations_type_option_ids' => $variation->variations_type_option_ids,
                    'quantity' => $variation->quantity,
                    'price' => $variation->price,
                ];
            })

        ];
    }
}
