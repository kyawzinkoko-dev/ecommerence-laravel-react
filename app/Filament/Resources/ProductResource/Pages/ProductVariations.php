<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class ProductVariations extends EditRecord
{
    protected static string $resource = ProductResource::class;
    protected static ?string $title = 'Variations';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public function form(Form $form): Form
    {
        $types = $this->record->variationTypes;
        $fields = [];
        foreach ($types as $i => $type) {
            $fields[] = TextInput::make('variation_type_' . ($type->id) . '.id')
                ->hidden();
            $fields[] = TextInput::make('variation_type_' . ($type->id) . '.name')
                ->label($type->name);

        }
        return $form->schema([
            Repeater::make('variations')
                ->collapsible()
                ->label(false)
                ->addable(false)
                ->defaultItems(1)
                ->schema([
                    Section::make($fields),
                    TextInput::make('quantity')
                        ->label('Quantity')
                        ->numeric(),
                    TextInput::make('price')
                        ->label('Price')
                        ->numeric(),
                ])
                ->columns(2)
                ->columnSpan(2)
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $variations = $this->record->variations->toArray();

        $data['variations'] = $this->mergeCartesianWithExisting($this->record->variationTypes, $variations);
        return $data;
    }

    private function mergeCartesianWithExisting($variationTypes, $exitingData): array
    {
        $defaultQuantity = $this->record->quantity;
        $defaultPrice = $this->record->price;
        $cartesianProduct = $this->cartesianProduct($variationTypes, $defaultQuantity, $defaultPrice);
        $mergedResult = [];
        foreach ($cartesianProduct as $product) {
            //Extract optionId from current product combination as an array
            $optionId = collect($product)
                ->filter(fn($value, $key) => str_starts_with($key, 'variation_type_'))
                ->map(fn($option) => $option['id'])
                ->values()
                ->toArray();
            //find matching entry in exiting data
            $match = array_filter($exitingData, function ($existingOption) use ($optionId) {

                return $existingOption['variations_type_option_ids'] === $optionId;
            });
            //if match is found , overwrite price and quantity
            if (!empty($match)) {
                $existingEntry = reset($match);
                $product['id'] = $existingEntry['id'];
                $product['quantity'] = $existingEntry['quantity'];
                $product['price'] = $existingEntry['price'];
            } else {
                $product['quantity'] = $defaultQuantity;
                $product['price'] = $defaultPrice;
            }
            $mergedResult[] = $product;
        }
        return $mergedResult;

    }

    private function cartesianProduct($variationTypes, $defaultQuantity = null, $defaultPrice = null): array
    {
        $result = [[]];

        foreach ($variationTypes as $index => $variationType) {

            $temp = [];
            foreach ($variationType->options as $option) {
                //add current option to and exiting combination
                foreach ($result as $combination) {
                    $newCombination = $combination + [
                            'variation_type_' . $variationType->id => [
                                'id' => $option->id,
                                'name' => $option->name,
                                'label' => $variationType->name,
                            ]
                        ];
                    $temp[] = $newCombination;
                }
            }
            $result = $temp;
        }
        foreach ($result as $combination) {
            if (count($combination) === $variationTypes) {
                $combination['quantity'] = $defaultQuantity;
                $combination['price'] = $defaultPrice;
            }
        }
        return $result;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $formattedData = [];
        foreach ($data['variations'] as $option) {

            $variationTypeOptionIds = [];
            foreach ($this->record->variationTypes as $type) {

                $variationTypeOptionIds[] = $option['variation_type_' . $type->id]['id'];
            }
            $quantity = $option['quantity'];
            $price = $option['price'];
            $formattedData[] = [
                'variations_type_option_ids' => $variationTypeOptionIds,
                'quantity' => $quantity,
                'price' => $price,
            ];
        }
        $data['variations'] = $formattedData;
        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $variations = $data['variations'];

        unset($data['variations']);
        $variations = collect($variations)->map(function ($variation) {
            return [
                'variations_type_option_ids' => json_encode($variation['variations_type_option_ids']),
                'quantity' => $variation['quantity'],
                'price' => $variation['price'],
            ];
        })->toArray();

        $record->variations()->upsert($variations, ['id'], ['variations_type_option_ids', 'quantity', 'price']);
        return $record;
    }

}
