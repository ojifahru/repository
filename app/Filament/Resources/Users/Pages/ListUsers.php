<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Tambah Pengguna')
                ->visible(fn() => auth()->user()->can('Create:User')),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            'active' => Tab::make('Pengguna Aktif')
                ->query(fn(Builder $query) => $query->whereNull('deleted_at'))
                ->badge(fn() => User::whereNull('deleted_at')->count())
                ->icon('heroicon-o-user-circle'),
        ];

        // 👑 SUPER ADMIN SAJA
        if (auth()->user()->hasRole('super_admin')) {
            // $tabs['all'] = Tab::make('Semua Pengguna')
            //     ->badge(fn() => User::withTrashed()->count())
            //     ->icon('heroicon-o-archive-box');

            $tabs['deleted'] = Tab::make('Pengguna Terhapus')
                ->query(fn(Builder $query) => $query->onlyTrashed())
                ->badge(fn() => User::onlyTrashed()->count())
                ->icon('heroicon-o-trash');
        }

        return $tabs;
    }
}
