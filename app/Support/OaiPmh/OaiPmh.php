<?php

namespace App\Support\OaiPmh;

use Carbon\CarbonImmutable;
use Illuminate\Support\Str;

final class OaiPmh
{
    public const OAI_NS = 'http://www.openarchives.org/OAI/2.0/';

    public static function responseDate(): string
    {
        return CarbonImmutable::now('UTC')->format('Y-m-d\\TH:i:s\\Z');
    }

    public static function datestamp(?\DateTimeInterface $dateTime): string
    {
        $dt = $dateTime
            ? CarbonImmutable::instance($dateTime)->utc()
            : CarbonImmutable::now('UTC');

        return $dt->format('Y-m-d\\TH:i:s\\Z');
    }

    /**
     * Parses OAI-PMH datestamp formats:
     * - YYYY-MM-DD
     * - YYYY-MM-DDThh:mm:ssZ
     */
    public static function parseDatestamp(string $value): ?CarbonImmutable
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) === 1) {
            $dt = CarbonImmutable::createFromFormat('Y-m-d', $value, 'UTC');

            return $dt instanceof CarbonImmutable ? $dt->startOfDay() : null;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/', $value) === 1) {
            $dt = CarbonImmutable::createFromFormat('Y-m-d\\TH:i:s\\Z', $value, 'UTC');

            return $dt instanceof CarbonImmutable ? $dt : null;
        }

        return null;
    }

    public static function oaiIdentifierForId(int $id): string
    {
        $repositoryIdentifier = (string) config('oai.repository_identifier');
        $resourceType = (string) config('oai.resource_type', 'article');

        return 'oai:'.$repositoryIdentifier.':'.$resourceType.'/'.$id;
    }

    public static function idFromOaiIdentifier(string $identifier): ?int
    {
        $identifier = trim($identifier);

        $repositoryIdentifier = (string) config('oai.repository_identifier');
        $resourceType = (string) config('oai.resource_type', 'article');

        $prefix = 'oai:'.$repositoryIdentifier.':'.$resourceType.'/';

        if (! Str::startsWith($identifier, $prefix)) {
            return null;
        }

        $idPart = Str::after($identifier, $prefix);

        if ($idPart === '' || preg_match('/^\d+$/', $idPart) !== 1) {
            return null;
        }

        return (int) $idPart;
    }

    public static function xmlSafe(?string $value): string
    {
        $value = (string) ($value ?? '');
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value) ?? '';

        return trim($value);
    }
}
