<?php

namespace App\Filament\Resources;

use App\Enums\ProductStatusEnum;
use App\Enums\RoleEnum;
use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->live(onBlur: true)
                    ->required()
                    ->afterStateUpdated(function (string $operation, $state, callable $set) {
                        $set('slug', Str::slug($state));
                    }),
                TextInput::make('slug')
                ->required(),
                Select::make('department_id')
                ->relationship('department', 'name')
                ->label(__('Department'))
                ->preload()
                ->reactive() //make the field reactive to change
                ->required()
                ->afterStateUpdated(function (string $operation, $state, callable $set) {
                    $set('category_id',null); //reset the category when the product change
                }),
                Select::make('category_id')
                ->relationship(name:'category',titleAttribute: 'name',modifyQueryUsing: function(Builder $query,callable $get)  {
                    $departmentId= $get('department_id');
                    if($departmentId){
                        $query->where('department_id',$departmentId);
                    }
                })
                ->label(__('Category'))
                ->preload()
                ->searchable()
                ->required(),
                Forms\Components\RichEditor::make('description')
                ->required()
                ->toolbarButtons([
                    'blockquote',
                    'bold',
                    'bulletList',
                    'h2',
                    'h3',
                    'italic',
                    'link',
                    'orderList',
                    'redo',
                    'strike',
                    'underline',
                    'undo',
                    'table'
                ])
                ->columnSpan(2),
                Forms\Components\TextInput::make('price')
                ->numeric()
                ->required(),
                Forms\Components\TextInput::make('quantity')
                ->integer(),
                Forms\Components\Select::make('status')
                ->options(ProductStatusEnum::labels()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                    TextColumn::make('name')
                ->words(10),
                    TextColumn::make('status')
                ->badge()
                ->colors(ProductStatusEnum::colors()),
                TextColumn::make('department.name'),
                TextColumn::make('category.name'),
                TextColumn::make('created_at')
                ->dateTime(),
            ])
            ->searchable()
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
//    public static function canViewAny(): bool
//    {
//       $user = auth()->user();
//       return $user && $user->hasRole(RoleEnum::Vendor->value);
//    }
}
