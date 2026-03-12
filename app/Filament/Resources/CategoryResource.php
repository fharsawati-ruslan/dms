<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    // Icon menu di samping (Sidebar) kita ganti jadi folder agar lebih pas
    protected static ?string $navigationIcon = 'heroicon-o-folder-open';
    
    protected static ?string $navigationLabel = 'Folder Dokumen';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Folder')
                    ->description('Tentukan nama dan hierarki folder di sini.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Folder')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                        
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(Category::class, 'slug', ignoreRecord: true)
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\Select::make('parent_id')
                            ->label('Induk Folder (Parent)')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->placeholder('Utama (Tanpa Induk)'),

                        Forms\Components\Select::make('icon')
                            ->label('Icon Folder')
                            ->options([
                                'heroicon-o-code-bracket' => 'Code (Aplikasi)',
                                'heroicon-o-shield-check' => 'Shield (Security)',
                                'heroicon-o-cpu-chip' => 'Chip (Digital)',
                                'heroicon-o-briefcase' => 'Briefcase (Umum)',
                                'heroicon-o-document-magnifying-glass' => 'Doc (Tender)',
                                'heroicon-o-archive-box' => 'Box (Lain-lain)',
                            ])
                            ->default('heroicon-o-folder'),

                        Forms\Components\ColorPicker::make('color')
                            ->label('Warna Folder')
                            ->default('#0077be'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom Visual: Menampilkan Icon dengan warna aslinya
                Tables\Columns\TextColumn::make('icon')
                    ->label('')
                    ->icon(fn ($record) => $record->icon ?? 'heroicon-o-folder')
                    ->color(fn ($record) => $record->color)
                    ->formatStateUsing(fn () => ''), // Mengosongkan teks agar hanya icon yang muncul

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Folder')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Berada di Dalam')
                    ->placeholder('Folder Utama')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label('Lihat Level')
                    ->relationship('parent', 'name')
                    ->placeholder('Semua Folder'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}