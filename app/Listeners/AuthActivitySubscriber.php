<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AuthActivitySubscriber
{
    public function handleLogin(Login $event): void
    {
        $properties = array_filter([
            'guard' => $event->guard,
            'remember' => $event->remember,
            'ip_address' => $this->getIpAddress(),
            'user_agent' => $this->getUserAgent(),
        ], fn ($value) => filled($value) || $value === false);

        if ($this->isDuplicate('login', [
            'guard' => $event->guard,
            'user_id' => $this->getAuthIdentifier($event->user),
            'ip_address' => Arr::get($properties, 'ip_address'),
            'user_agent' => Arr::get($properties, 'user_agent'),
        ])) {
            return;
        }

        activity('auth')
            ->event('login')
            ->performedOn($event->user)
            ->causedBy($event->user)
            ->withProperties($properties)
            ->log('Login');
    }

    public function handleLogout(Logout $event): void
    {
        $properties = array_filter([
            'guard' => $event->guard,
            'ip_address' => $this->getIpAddress(),
            'user_agent' => $this->getUserAgent(),
        ], fn ($value) => filled($value));

        if ($this->isDuplicate('logout', [
            'guard' => $event->guard,
            'user_id' => $this->getAuthIdentifier($event->user),
            'ip_address' => Arr::get($properties, 'ip_address'),
            'user_agent' => Arr::get($properties, 'user_agent'),
        ])) {
            return;
        }

        activity('auth')
            ->event('logout')
            ->performedOn($event->user)
            ->causedBy($event->user)
            ->withProperties($properties)
            ->log('Logout');
    }

    public function handleFailed(Failed $event): void
    {
        $credentials = $this->sanitizeCredentials($event->credentials ?? []);

        $properties = array_filter([
            'guard' => $event->guard,
            'credentials' => $credentials,
            'ip_address' => $this->getIpAddress(),
            'user_agent' => $this->getUserAgent(),
        ], fn ($value) => filled($value));

        if ($this->isDuplicate('failed', [
            'guard' => $event->guard,
            'user_id' => $event->user ? $this->getAuthIdentifier($event->user) : null,
            'credentials' => $credentials,
            'ip_address' => Arr::get($properties, 'ip_address'),
            'user_agent' => Arr::get($properties, 'user_agent'),
        ])) {
            return;
        }

        $logger = activity('auth')
            ->event('failed')
            ->withProperties($properties);

        if ($event->user) {
            $logger->performedOn($event->user);
        }

        $logger->log('Login Gagal');
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            Login::class => 'handleLogin',
            Logout::class => 'handleLogout',
            Failed::class => 'handleFailed',
        ];
    }

    /**
     * @param  array<string, mixed>  $credentials
     * @return array<string, mixed>
     */
    private function sanitizeCredentials(array $credentials): array
    {
        $sanitized = Arr::except($credentials, ['password', 'password_confirmation']);

        foreach (array_keys($sanitized) as $key) {
            if (Str::contains(Str::lower((string) $key), 'password')) {
                unset($sanitized[$key]);
            }
        }

        return $sanitized;
    }

    /**
     * Prevent identical auth activity logs from being written multiple times
     * within a very short window.
     *
     * @param  array<string, mixed>  $context
     */
    private function isDuplicate(string $event, array $context): bool
    {
        $fingerprint = sha1(json_encode([
            'event' => $event,
            'context' => $context,
        ], JSON_THROW_ON_ERROR));

        $cacheKey = 'activitylog:auth-dedup:'.$fingerprint;

        try {
            return ! Cache::add($cacheKey, true, now()->addSeconds(3));
        } catch (\Throwable) {
            return false;
        }
    }

    private function getAuthIdentifier(mixed $user): string|int|null
    {
        if (! $user) {
            return null;
        }

        if (method_exists($user, 'getAuthIdentifier')) {
            return $user->getAuthIdentifier();
        }

        if (method_exists($user, 'getKey')) {
            return $user->getKey();
        }

        return null;
    }

    private function getIpAddress(): ?string
    {
        if (! app()->bound('request')) {
            return null;
        }

        $request = request();

        return method_exists($request, 'ip') ? $request->ip() : null;
    }

    private function getUserAgent(): ?string
    {
        if (! app()->bound('request')) {
            return null;
        }

        $request = request();

        return method_exists($request, 'userAgent') ? $request->userAgent() : null;
    }
}
