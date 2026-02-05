<?php

use App\Models\User;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Activitylog\Models\Activity;

beforeEach(function (): void {
    Schema::dropIfExists('activity_log');
    Schema::dropIfExists('users');

    Schema::create('users', function (Blueprint $table): void {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->rememberToken();
        $table->timestamps();
    });

    Schema::create('activity_log', function (Blueprint $table): void {
        $table->bigIncrements('id');
        $table->string('log_name')->nullable();
        $table->text('description');
        $table->string('subject_type')->nullable();
        $table->unsignedBigInteger('subject_id')->nullable();
        $table->string('event')->nullable();
        $table->string('causer_type')->nullable();
        $table->unsignedBigInteger('causer_id')->nullable();
        $table->json('properties')->nullable();
        $table->uuid('batch_uuid')->nullable();
        $table->timestamps();
        $table->index('log_name');
    });
});

it('logs user login', function (): void {
    $user = User::factory()->create();

    event(new Login('web', $user, false));

    expect(Activity::query()
        ->where('log_name', 'auth')
        ->where('event', 'login')
        ->where('causer_type', User::class)
        ->where('causer_id', $user->getKey())
        ->exists())->toBeTrue();
});

it('logs user logout', function (): void {
    $user = User::factory()->create();

    event(new Logout('web', $user));

    expect(Activity::query()
        ->where('log_name', 'auth')
        ->where('event', 'logout')
        ->where('causer_type', User::class)
        ->where('causer_id', $user->getKey())
        ->exists())->toBeTrue();
});

it('logs failed login without storing password', function (): void {
    $user = User::factory()->create();

    event(new Failed('web', $user, [
        'email' => $user->email,
        'password' => 'super-secret',
    ]));

    $activity = Activity::query()
        ->where('log_name', 'auth')
        ->where('event', 'failed')
        ->latest('id')
        ->first();

    expect($activity)->not->toBeNull();

    $properties = $activity->properties?->toArray() ?? [];

    expect(data_get($properties, 'credentials.email'))->toBe($user->email);
    expect(data_get($properties, 'credentials.password'))->toBeNull();
});
