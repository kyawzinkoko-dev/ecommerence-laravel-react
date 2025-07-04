<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['updated_by'] = auth()->id();
        $data['created_by'] = auth()->id();
        return parent::mutateFormDataBeforeCreate($data);
    }
protected function getRedirectUrl(): string
{

    return $this->previousUrl ?? $this->getResource()::getUrl('index');
}
}
