<?php

namespace App\Traits;

use App\Helpers\NationalityHelper;
use Filament\Forms;
use Filament\Tables;

trait HasNationalityField
{
    /**
     * Get nationality form field
     *
     * @param string $fieldName
     * @param bool $required
     * @param string|null $defaultValue
     * @return Forms\Components\Select
     */
    public static function nationalityField(
        string $fieldName = 'nationality',
        bool $required = false,
        ?string $defaultValue = null
    ): Forms\Components\Select {
        return Forms\Components\Select::make($fieldName)
            ->label(__('general.Nationality'))
            ->searchable()
            ->options(NationalityHelper::getAllNationalities())
            ->default($defaultValue ?? NationalityHelper::getDefaultNationality())
            ->placeholder(__('general.Select nationality'))
            ->required($required);
    }

    /**
     * Get nationality form field for Arab countries only
     *
     * @param string $fieldName
     * @param bool $required
     * @param string|null $defaultValue
     * @return Forms\Components\Select
     */
    public static function arabNationalityField(
        string $fieldName = 'nationality',
        bool $required = false,
        ?string $defaultValue = null
    ): Forms\Components\Select {
        return Forms\Components\Select::make($fieldName)
            ->label(__('general.Nationality'))
            ->searchable()
            ->options(NationalityHelper::getArabNationalities())
            ->default($defaultValue ?? NationalityHelper::getDefaultNationality())
            ->placeholder(__('general.Select nationality'))
            ->required($required);
    }

    /**
     * Get nationality form field for GCC countries only
     *
     * @param string $fieldName
     * @param bool $required
     * @param string|null $defaultValue
     * @return Forms\Components\Select
     */
    public static function gccNationalityField(
        string $fieldName = 'nationality',
        bool $required = false,
        ?string $defaultValue = null
    ): Forms\Components\Select {
        return Forms\Components\Select::make($fieldName)
            ->label(__('general.Nationality'))
            ->searchable()
            ->options(NationalityHelper::getGccNationalities())
            ->default($defaultValue ?? NationalityHelper::getDefaultNationality())
            ->placeholder(__('general.Select nationality'))
            ->required($required);
    }

    /**
     * Get nationality table filter
     *
     * @param string $fieldName
     * @return Tables\Filters\SelectFilter
     */
    public static function nationalityFilter(string $fieldName = 'nationality'): Tables\Filters\SelectFilter
    {
        return Tables\Filters\SelectFilter::make($fieldName)
            ->label(__('general.Nationality'))
            ->options(NationalityHelper::getAllNationalities())
            ->searchable()
            ->multiple()
            ->placeholder(__('general.Select nationality'));
    }

    /**
     * Get nationality table column
     *
     * @param string $fieldName
     * @return Tables\Columns\TextColumn
     */
    public static function nationalityColumn(string $fieldName = 'nationality'): Tables\Columns\TextColumn
    {
        return Tables\Columns\TextColumn::make($fieldName)
            ->label(__('general.Nationality'))
            ->searchable()
            ->sortable()
            ->toggleable()
            ->formatStateUsing(function ($state) {
                $nationalities = NationalityHelper::getAllNationalities();
                return $nationalities[$state] ?? $state;
            });
    }
}
