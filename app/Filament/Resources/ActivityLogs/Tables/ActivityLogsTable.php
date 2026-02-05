<?php

namespace App\Filament\Resources\ActivityLogs\Tables;

use App\Filament\Resources\Authors\AuthorResource;
use App\Filament\Resources\Categories\CategoriesResource;
use App\Filament\Resources\Degrees\DegreeResource;
use App\Filament\Resources\DocumentTypes\DocumentTypeResource;
use App\Filament\Resources\Faculties\FacultyResource;
use App\Filament\Resources\ProgramTypes\ProgramTypeResource;
use App\Filament\Resources\StudyPrograms\StudyProgramResource;
use App\Filament\Resources\TriDharmas\TriDharmaResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\Author;
use App\Models\Categories;
use App\Models\Degree;
use App\Models\DocumentType;
use App\Models\Faculty;
use App\Models\ProgramType;
use App\Models\StudyProgram;
use App\Models\TriDharma;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Activity;

class ActivityLogsTable
{
    private const EVENT_LABELS = [
        'created' => 'Dibuat',
        'updated' => 'Diubah',
        'deleted' => 'Dihapus',
        'restored' => 'Dipulihkan',
        'login' => 'Login',
        'logout' => 'Logout',
        'failed' => 'Login Gagal',
    ];

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->since()
                    ->color('gray')
                    ->tooltip(fn (Activity $record): ?string => $record->created_at?->format('d M Y H:i:s'))
                    ->sortable(),

                TextColumn::make('event')
                    ->label('Event')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => self::EVENT_LABELS[$state] ?? (string) Str::headline((string) $state))
                    ->color(fn (?string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        'restored' => 'info',
                        'login' => 'success',
                        'logout' => 'gray',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Deskripsi')
                    ->wrap()
                    ->limit(70)
                    ->tooltip(fn (Activity $record): ?string => filled($record->description) ? (string) $record->description : null)
                    ->searchable(),

                TextColumn::make('log_name')
                    ->label('Log')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('subject')
                    ->label('Target')
                    ->getStateUsing(fn (Activity $record): string => self::formatSubjectLabel($record))
                    ->tooltip(fn (Activity $record): ?string => self::formatSubjectTooltip($record))
                    ->url(fn (Activity $record): ?string => self::getSubjectUrl($record))
                    ->openUrlInNewTab()
                    ->toggleable(),

                TextColumn::make('causer')
                    ->label('User')
                    ->getStateUsing(fn (Activity $record): string => self::formatCauserLabel($record))
                    ->description(fn (Activity $record): ?string => self::formatCauserDescription($record))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHasMorph(
                            'causer',
                            ['*'],
                            fn (Builder $morphQuery) => $morphQuery->where('name', 'like', "%{$search}%")
                        );
                    })
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('event')
                    ->label('Event')
                    ->options([
                        'created' => 'Dibuat',
                        'updated' => 'Diubah',
                        'deleted' => 'Dihapus',
                        'restored' => 'Dipulihkan',
                        'login' => 'Login',
                        'logout' => 'Logout',
                        'failed' => 'Login Gagal',
                    ]),

                SelectFilter::make('log_name')
                    ->label('Log')
                    ->options(fn (): array => Activity::query()
                        ->select('log_name')
                        ->distinct()
                        ->orderBy('log_name')
                        ->pluck('log_name', 'log_name')
                        ->filter()
                        ->all()),

                SelectFilter::make('causer_id')
                    ->label('User')
                    ->searchable()
                    ->options(fn (): array => User::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->query(function (Builder $query, array $data): Builder {
                        $value = Arr::get($data, 'value');

                        return $query
                            ->when(
                                filled($value),
                                fn (Builder $query): Builder => $query
                                    ->where('causer_type', User::class)
                                    ->where('causer_id', $value)
                            );
                    }),

                Filter::make('created_at')
                    ->label('Periode')
                    ->form([
                        DatePicker::make('from')->label('Dari'),
                        DatePicker::make('until')->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $from = Arr::get($data, 'from');
                        $until = Arr::get($data, 'until');

                        return $query
                            ->when(
                                filled($from),
                                fn (Builder $query): Builder => $query->where('created_at', '>=', Carbon::parse($from)->startOfDay())
                            )
                            ->when(
                                filled($until),
                                fn (Builder $query): Builder => $query->where('created_at', '<=', Carbon::parse($until)->endOfDay())
                            );
                    }),
            ])
            ->recordActions([
                Action::make('recordActivities')
                    ->label('')
                    ->icon('heroicon-o-clock')
                    ->tooltip('Lihat aktivitas record')
                    ->iconButton()
                    ->url(fn (Activity $record): ?string => self::getSubjectActivitiesUrl($record))
                    ->visible(fn (Activity $record): bool => filled(self::getSubjectActivitiesUrl($record))),
            ])
            ->toolbarActions([])
            ->defaultSort('created_at', 'desc');
    }

    private static function formatSubjectLabel(Activity $record): string
    {
        if (blank($record->subject_type) || blank($record->subject_id)) {
            return '—';
        }

        $type = class_basename((string) $record->subject_type);
        $id = (string) $record->subject_id;

        $display = self::guessModelDisplayValue($record->subject);

        return filled($display)
            ? $type.' • '.$display
            : $type.' #'.$id;
    }

    private static function formatSubjectTooltip(Activity $record): ?string
    {
        if (blank($record->subject_type) || blank($record->subject_id)) {
            return null;
        }

        $type = class_basename((string) $record->subject_type);
        $id = (string) $record->subject_id;
        $display = self::guessModelDisplayValue($record->subject);

        return filled($display)
            ? $type.' #'.$id.' — '.$display
            : $type.' #'.$id;
    }

    private static function formatCauserLabel(Activity $record): string
    {
        if (! $record->causer) {
            return 'System';
        }

        $name = data_get($record->causer, 'name');

        return filled($name)
            ? (string) $name
            : class_basename((string) $record->causer_type).' #'.$record->causer_id;
    }

    private static function formatCauserDescription(Activity $record): ?string
    {
        if (! $record->causer) {
            return null;
        }

        $email = data_get($record->causer, 'email');

        return filled($email) ? (string) $email : null;
    }

    private static function guessModelDisplayValue(?Model $model): ?string
    {
        if (! $model) {
            return null;
        }

        foreach (['name', 'title', 'email', 'code', 'slug'] as $attribute) {
            $value = data_get($model, $attribute);

            if (filled($value)) {
                return (string) $value;
            }
        }

        return null;
    }

    private static function getSubjectUrl(Activity $record): ?string
    {
        if (! $record->subject) {
            return null;
        }

        return match ($record->subject_type) {
            Author::class => AuthorResource::getUrl('edit', ['record' => $record->subject]),
            Categories::class => CategoriesResource::getUrl('edit', ['record' => $record->subject]),
            Degree::class => DegreeResource::getUrl('edit', ['record' => $record->subject]),
            DocumentType::class => DocumentTypeResource::getUrl('edit', ['record' => $record->subject]),
            Faculty::class => FacultyResource::getUrl('edit', ['record' => $record->subject]),
            ProgramType::class => ProgramTypeResource::getUrl('edit', ['record' => $record->subject]),
            StudyProgram::class => StudyProgramResource::getUrl('edit', ['record' => $record->subject]),
            TriDharma::class => TriDharmaResource::getUrl('edit', ['record' => $record->subject]),
            User::class => UserResource::getUrl('edit', ['record' => $record->subject]),
            default => null,
        };
    }

    private static function getSubjectActivitiesUrl(Activity $record): ?string
    {
        if (! $record->subject) {
            return null;
        }

        return match ($record->subject_type) {
            Author::class => AuthorResource::getUrl('activities', ['record' => $record->subject]),
            Categories::class => CategoriesResource::getUrl('activities', ['record' => $record->subject]),
            Degree::class => DegreeResource::getUrl('activities', ['record' => $record->subject]),
            DocumentType::class => DocumentTypeResource::getUrl('activities', ['record' => $record->subject]),
            Faculty::class => FacultyResource::getUrl('activities', ['record' => $record->subject]),
            ProgramType::class => ProgramTypeResource::getUrl('activities', ['record' => $record->subject]),
            StudyProgram::class => StudyProgramResource::getUrl('activities', ['record' => $record->subject]),
            TriDharma::class => TriDharmaResource::getUrl('activities', ['record' => $record->subject]),
            User::class => UserResource::getUrl('activities', ['record' => $record->subject]),
            default => null,
        };
    }
}
