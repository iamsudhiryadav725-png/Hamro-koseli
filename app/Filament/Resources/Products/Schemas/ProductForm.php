<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('vendor_id')
                    ->relationship('vendor', 'vendor_name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('category_id')
                    ->relationship('category', 'cat_name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(100)
                    ->minLength(3)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, $state, callable $set) {
                        if ($operation === 'create') {
                            $set('slug', Str::slug($state));
                        }
                    }),

                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->alphaDash(),
                TextInput::make('sku')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(100),
                RichEditor::make('description')
                    ->toolbarButtons([
                        'bold',
                        'italic',
                        'underline',
                        'bulletList',
                        'orderedList',
                        'h2',
                        'h3',
                        'link',
                        'blockquote',
                        'undo',
                        'redo',
                    ])
                    ->extraAttributes(['style' => 'min-height: 160px'])
                    ->default(null)
                    ->columnSpanFull(),
                // Replaces the old FileUpload::make('image')->multiple(), which wrote
                // multiple paths into a single plain string column on `products` that
                // was never read anywhere (see Product::primaryImageUrl()). Images
                // actually live in the polymorphic `images` table via Product::images().
                Repeater::make('images')
                    ->relationship('images')
                    ->schema([
                        FileUpload::make('path')
                            ->image()
                            ->imageEditor()
                            ->maxSize(2048)
                            ->directory('products')
                            ->disk('public')
                            ->required()
                            ->columnSpanFull(),
                        Select::make('type')
                            ->options([
                                'thumbnail' => 'Thumbnail',
                                'gallery' => 'Gallery',
                                'banner' => 'Banner',
                            ])
                            ->native(false)
                            ->placeholder('None'),
                        Toggle::make('is_primary')
                            ->label('Primary image')
                            ->helperText('Shown in listings and as the default product image.')
                            ->inline(false),
                    ])
                    ->addActionLabel('Add image')
                    ->collapsible()
                    ->collapsed(fn (?array $state): bool => filled($state))
                    ->itemLabel(fn (array $state): ?string => $state['type'] ?? 'Image')
                    ->defaultItems(0)
                    ->maxItems(5)
                    ->reorderable(false)
                    ->helperText('Max 5 images, 2MB each. JPG, PNG, or WebP. Mark one as primary -it\'s used in listings and as the product\'s default image.')
                    ->columnSpanFull(),

                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('NPR')
                    ->minValue(1)
                    ->maxValue(9999999)
                    ->step(0.01)
                    ->rules(['regex:/^\d+(\.\d{1,2})?$/']),
                TextInput::make('discount_price')
                    ->label('Discount (%)')
                    ->numeric()
                    ->default(null)
                    ->suffix('%')
                    ->minValue(0)
                    ->maxValue(99)
                    ->dehydrateStateUsing(fn ($state, $get) => $state ? ((float) $get('price') - ((float) $get('price') * (float) $state / 100)) : null)
                    ->formatStateUsing(fn ($state, $get) => ($state && (float) $get('price') > 0) ? round((((float) $get('price') - (float) $state) / (float) $get('price')) * 100) : null),
                TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(99999)
                    ->step(1)
                    ->suffix('units'),

                Select::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive', 'draft' => 'Draft'])
                    ->default('active')
                    ->required(),
            ]);
    }
}
