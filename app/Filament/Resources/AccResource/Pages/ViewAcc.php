<?php

namespace App\Filament\Resources\AccResource\Pages;

use App\Filament\Resources\AccResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Support\Enums\FontWeight;

class ViewAcc extends ViewRecord
{
    protected static string $resource = AccResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label(__('general.Edit'))
                ->icon('heroicon-o-pencil-square')
                ->color('primary'),
            Actions\DeleteAction::make()
                ->label(__('general.Delete'))
                ->icon('heroicon-o-trash')
                ->requiresConfirmation(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Personal Information Section
                Section::make(__('general.Personal Information'))
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('firstname')
                                    ->label(__('general.First Name'))
                                    ->weight(FontWeight::Bold)
                                    ->color('primary')
                                    ->icon('heroicon-o-user'),
                                
                                TextEntry::make('midname')
                                    ->label(__('general.Middle Name'))
                                    ->placeholder(__('general.Not specified')),
                                
                                TextEntry::make('lastname')
                                    ->label(__('general.Last Name'))
                                    ->weight(FontWeight::Bold)
                                    ->color('primary'),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('birth_date')
                                    ->label(__('general.Birth Date'))
                                    ->date()
                                    ->icon('heroicon-o-calendar-days')
                                    ->placeholder(__('general.Not specified')),
                                
                                TextEntry::make('nationality')
                                    ->label(__('general.Nationality'))
                                    ->icon('heroicon-o-flag')
                                    ->placeholder(__('general.Not specified')),
                            ]),
                    ])
                    ->columns(1)
                    ->icon('heroicon-o-identification'),

                // Contact Information Section
                Section::make(__('general.Contact Information'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('email')
                                    ->label(__('general.Email'))
                                    ->icon('heroicon-o-envelope')
                                    ->copyable()
                                    ->copyMessage(__('general.Email copied!')),
                                
                                TextEntry::make('phone')
                                    ->label(__('general.Phone'))
                                    ->icon('heroicon-o-phone')
                                    ->copyable()
                                    ->copyMessage(__('general.Phone copied!')),
                            ]),
                        
                        TextEntry::make('address')
                            ->label(__('general.Address'))
                            ->icon('heroicon-o-map-pin')
                            ->columnSpanFull()
                            ->placeholder(__('general.Not specified')),
                    ])
                    ->columns(2)
                    ->icon('heroicon-o-phone'),

                // Profile Information Section
                Section::make(__('general.Profile Information'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                // Profile Photo - محسن للسرعة القصوى
                                ImageEntry::make('profile_photo')
                                    ->label(__('general.Profile Photo'))
                                    ->disk('uploads')
                                    ->height(100) // أصغر للسرعة القصوى
                                    ->width(100)
                                    ->circular()
                                    ->defaultImageUrl(url('data:image/svg+xml;base64,' . base64_encode('
                                        <svg width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="50" cy="50" r="48" fill="#f3f4f6" stroke="#e5e7eb" stroke-width="2"/>
                                            <circle cx="50" cy="35" r="18" fill="#9ca3af"/>
                                            <path d="M20 85 C20 70, 35 65, 50 65 C65 65, 80 70, 80 85" fill="#9ca3af"/>
                                            <text x="50" y="95" text-anchor="middle" fill="#6b7280" font-size="6">Fast</text>
                                        </svg>
                                    ')))
                                    ->extraAttributes([
                                        'loading' => 'lazy', // تحميل تدريجي
                                        'decoding' => 'async', // فك التشفير غير المتزامن
                                        'fetchpriority' => 'low', // أولوية منخفضة
                                        'class' => 'mx-auto object-cover transition-all duration-200 hover:scale-105',
                                        'style' => 'image-rendering: -webkit-optimize-contrast; will-change: transform;'
                                    ]),
                                
                                // Status and basic info
                                Grid::make(1)
                                    ->schema([
                                        TextEntry::make('status')
                                            ->label(__('general.Status'))
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'active' => 'success',
                                                'inactive' => 'gray',
                                                'suspended' => 'danger',
                                                'pending' => 'warning',
                                                default => 'gray',
                                            })
                                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                                'active' => __('general.Active'),
                                                'inactive' => __('general.Inactive'),
                                                'suspended' => __('general.Suspended'),
                                                'pending' => __('general.Pending'),
                                                default => $state,
                                            }),
                                        
                                        TextEntry::make('hired_date')
                                            ->label(__('general.Hired Date'))
                                            ->date()
                                            ->icon('heroicon-o-calendar'),
                                        
                                        TextEntry::make('hired_by')
                                            ->label(__('general.Hired By'))
                                            ->icon('heroicon-o-user-plus')
                                            ->placeholder(__('general.Not specified')),
                                    ]),
                            ]),
                    ])
                    ->columns(1)
                    ->icon('heroicon-o-user-circle'),

                // Document Information Section
                Section::make(__('general.Document Information'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('document_type')
                                    ->label(__('general.Document Type'))
                                    ->badge()
                                    ->color('info')
                                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                                        'passport' => __('general.Passport'),
                                        'id_card' => __('general.ID Card'),
                                        'driver_license' => __('general.Driver License'),
                                        'residency_permit' => __('general.Residency Permit'),
                                        'other' => __('general.Other'),
                                        default => __('general.Not specified'),
                                    }),
                                
                                TextEntry::make('document_number')
                                    ->label(__('general.Document Number'))
                                    ->icon('heroicon-o-identification')
                                    ->copyable()
                                    ->placeholder(__('general.Not specified')),
                            ]),
                        
                        // Document Photo - محسن للسرعة القصوى
                        ImageEntry::make('document_photo')
                            ->label(__('general.Document Photo'))
                            ->disk('uploads')
                            ->height(80) // أصغر للسرعة
                            ->width(120)
                            ->defaultImageUrl(url('data:image/svg+xml;base64,' . base64_encode('
                                <svg width="120" height="80" viewBox="0 0 120 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect width="120" height="80" fill="#f9fafb" stroke="#e5e7eb" stroke-width="1" rx="4"/>
                                    <rect x="30" y="15" width="60" height="35" fill="#d1d5db" rx="2"/>
                                    <rect x="35" y="20" width="10" height="8" fill="#9ca3af" rx="1"/>
                                    <rect x="50" y="20" width="25" height="2" fill="#9ca3af" rx="1"/>
                                    <rect x="50" y="25" width="20" height="2" fill="#9ca3af" rx="1"/>
                                    <text x="60" y="70" text-anchor="middle" fill="#6b7280" font-size="6">Speed</text>
                                </svg>
                            ')))
                            ->columnSpanFull()
                            ->extraAttributes([
                                'loading' => 'lazy',
                                'decoding' => 'async',
                                'fetchpriority' => 'low',
                                'class' => 'mx-auto transition-all duration-200',
                                'style' => 'image-rendering: -webkit-optimize-contrast; object-fit: contain;'
                            ]),
                    ])
                    ->columns(2)
                    ->icon('heroicon-o-document-text'),

                // System Information Section
                Section::make(__('general.System Information'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label(__('general.Created At'))
                                    ->dateTime()
                                    ->icon('heroicon-o-plus-circle')
                                    ->color('success'),
                                
                                TextEntry::make('updated_at')
                                    ->label(__('general.Updated At'))
                                    ->dateTime()
                                    ->icon('heroicon-o-pencil-square')
                                    ->color('info'),
                            ]),
                    ])
                    ->columns(2)
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(),
            ]);
    }
} 