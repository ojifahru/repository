<?php

namespace App\Http\Controllers;

use App\Models\TriDharma;
use App\Support\OaiPmh\OaiDcMapper;
use App\Support\OaiPmh\OaiPmh;
use App\Support\OaiPmh\ResumptionToken;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class OaiPmhController
{
    public function __invoke(Request $request, OaiDcMapper $dcMapper): Response
    {
        $oaiParams = Arr::only($request->all(), [
            'verb',
            'identifier',
            'metadataPrefix',
            'from',
            'until',
            'set',
            'resumptionToken',
        ]);

        $verb = $request->input('verb');

        if (! is_string($verb) || trim($verb) === '') {
            return $this->renderError(
                request: $oaiParams,
                code: 'badVerb',
                message: 'The request does not provide a valid verb.'
            );
        }

        $verb = trim($verb);

        return match ($verb) {
            'Identify' => $this->identify($oaiParams),
            'ListMetadataFormats' => $this->listMetadataFormats($oaiParams),
            'GetRecord' => $this->getRecord($oaiParams, $dcMapper),
            'ListRecords' => $this->listRecords($oaiParams, $dcMapper),
            default => $this->renderError(
                request: $oaiParams,
                code: 'badVerb',
                message: 'The verb \''.$verb.'\' is not supported.'
            ),
        };
    }

    private function identify(array $params): Response
    {
        if (! $this->hasOnlyAllowedArguments($params, ['verb'])) {
            return $this->renderError($params, 'badArgument', 'Identify does not accept additional arguments.');
        }

        try {
            $earliest = TriDharma::query()
                ->where('status', 'published')
                ->min('updated_at');
        } catch (\Throwable) {
            $earliest = null;
        }

        $earliestDatestamp = $earliest
            ? OaiPmh::datestamp(CarbonImmutable::parse((string) $earliest, 'UTC'))
            : OaiPmh::responseDate();

        return $this->render(
            request: $params,
            verb: 'Identify',
            data: [
                'repositoryName' => (string) config('oai.repository_name'),
                'baseURL' => route('oai', [], true),
                'protocolVersion' => '2.0',
                'adminEmail' => (string) config('oai.admin_email'),
                'earliestDatestamp' => $earliestDatestamp,
                'deletedRecord' => (string) config('oai.deleted_record', 'no'),
                'granularity' => (string) config('oai.granularity', 'YYYY-MM-DDThh:mm:ssZ'),
                'repositoryIdentifier' => (string) config('oai.repository_identifier'),
                'resourceType' => (string) config('oai.resource_type', 'article'),
            ]
        );
    }

    private function listMetadataFormats(array $params): Response
    {
        if (! $this->hasOnlyAllowedArguments($params, ['verb', 'identifier'])) {
            return $this->renderError($params, 'badArgument', 'ListMetadataFormats only accepts the optional identifier argument.');
        }

        if (is_string($params['identifier'] ?? null) && trim((string) $params['identifier']) !== '') {
            $id = OaiPmh::idFromOaiIdentifier((string) $params['identifier']);

            if ($id === null) {
                return $this->renderError($params, 'idDoesNotExist', 'The identifier does not match this repository identifier format.');
            }

            $exists = TriDharma::query()
                ->whereKey($id)
                ->where('status', 'published')
                ->exists();

            if (! $exists) {
                return $this->renderError($params, 'idDoesNotExist', 'No record exists for the given identifier.');
            }
        }

        return $this->render(
            request: $params,
            verb: 'ListMetadataFormats',
            data: [
                'formats' => [
                    [
                        'metadataPrefix' => 'oai_dc',
                        'schema' => 'http://www.openarchives.org/OAI/2.0/oai_dc.xsd',
                        'metadataNamespace' => 'http://www.openarchives.org/OAI/2.0/oai_dc/',
                    ],
                ],
            ]
        );
    }

    private function getRecord(array $params, OaiDcMapper $dcMapper): Response
    {
        if (! $this->hasOnlyAllowedArguments($params, ['verb', 'identifier', 'metadataPrefix'])) {
            return $this->renderError($params, 'badArgument', 'GetRecord requires identifier and metadataPrefix only.');
        }

        $identifier = $params['identifier'] ?? null;
        $metadataPrefix = $params['metadataPrefix'] ?? null;

        if (! is_string($identifier) || trim($identifier) === '') {
            return $this->renderError($params, 'badArgument', 'GetRecord requires identifier.');
        }

        if (! is_string($metadataPrefix) || trim($metadataPrefix) === '') {
            return $this->renderError($params, 'badArgument', 'GetRecord requires metadataPrefix.');
        }

        if (trim($metadataPrefix) !== 'oai_dc') {
            return $this->renderError($params, 'cannotDisseminateFormat', 'Only metadataPrefix=oai_dc is supported.');
        }

        $id = OaiPmh::idFromOaiIdentifier($identifier);

        if ($id === null) {
            return $this->renderError($params, 'idDoesNotExist', 'The identifier does not match this repository identifier format.');
        }

        $document = TriDharma::query()
            ->whereKey($id)
            ->where('status', 'published')
            ->first();

        if ($document === null) {
            return $this->renderError($params, 'idDoesNotExist', 'No record exists for the given identifier.');
        }

        $record = $this->buildRecord($document, $dcMapper);

        return $this->render(
            request: $params,
            verb: 'GetRecord',
            data: [
                'record' => $record,
            ]
        );
    }

    private function listRecords(array $params, OaiDcMapper $dcMapper): Response
    {
        if (($params['set'] ?? null) !== null && trim((string) ($params['set'] ?? '')) !== '') {
            return $this->renderError($params, 'noSetHierarchy', 'This repository does not support sets.');
        }

        $resumptionToken = $params['resumptionToken'] ?? null;

        if (is_string($resumptionToken) && trim($resumptionToken) !== '') {
            if (! $this->hasOnlyAllowedArguments($params, ['verb', 'resumptionToken'])) {
                return $this->renderError($params, 'badArgument', 'When resumptionToken is provided, no other arguments are allowed.');
            }

            $decoded = ResumptionToken::decode($resumptionToken);

            if ($decoded === null) {
                return $this->renderError($params, 'badResumptionToken', 'The resumptionToken is invalid.');
            }

            $metadataPrefix = $decoded['metadataPrefix'] ?? null;

            if (! is_string($metadataPrefix) || $metadataPrefix !== 'oai_dc') {
                return $this->renderError($params, 'cannotDisseminateFormat', 'Only metadataPrefix=oai_dc is supported.');
            }

            $from = is_string($decoded['from'] ?? null) ? OaiPmh::parseDatestamp((string) $decoded['from']) : null;
            $until = is_string($decoded['until'] ?? null) ? OaiPmh::parseDatestamp((string) $decoded['until']) : null;

            $cursorUpdatedAt = is_string($decoded['cursorUpdatedAt'] ?? null) ? OaiPmh::parseDatestamp((string) $decoded['cursorUpdatedAt']) : null;
            $cursorId = is_numeric($decoded['cursorId'] ?? null) ? (int) $decoded['cursorId'] : null;

            if ($cursorUpdatedAt === null || $cursorId === null) {
                return $this->renderError($params, 'badResumptionToken', 'The resumptionToken payload is incomplete.');
            }

            return $this->listRecordsPage(
                requestParams: $params,
                dcMapper: $dcMapper,
                from: $from,
                until: $until,
                cursorUpdatedAt: $cursorUpdatedAt,
                cursorId: $cursorId
            );
        }

        if (! $this->hasOnlyAllowedArguments($params, ['verb', 'metadataPrefix', 'from', 'until'])) {
            return $this->renderError($params, 'badArgument', 'ListRecords accepts metadataPrefix, from, until (and optional resumptionToken).');
        }

        $metadataPrefix = $params['metadataPrefix'] ?? null;

        if (! is_string($metadataPrefix) || trim($metadataPrefix) === '') {
            return $this->renderError($params, 'badArgument', 'ListRecords requires metadataPrefix.');
        }

        if (trim($metadataPrefix) !== 'oai_dc') {
            return $this->renderError($params, 'cannotDisseminateFormat', 'Only metadataPrefix=oai_dc is supported.');
        }

        [$from, $until, $dateError] = $this->parseFromUntil($params);

        if ($dateError !== null) {
            return $this->renderError($params, 'badArgument', $dateError);
        }

        return $this->listRecordsPage(
            requestParams: $params,
            dcMapper: $dcMapper,
            from: $from,
            until: $until,
            cursorUpdatedAt: null,
            cursorId: null
        );
    }

    /**
     * @return array{0:CarbonImmutable|null,1:CarbonImmutable|null,2:string|null}
     */
    private function parseFromUntil(array $params): array
    {
        $fromRaw = $params['from'] ?? null;
        $untilRaw = $params['until'] ?? null;

        $from = null;
        $until = null;

        if (is_string($fromRaw) && trim($fromRaw) !== '') {
            $from = OaiPmh::parseDatestamp($fromRaw);

            if ($from === null) {
                return [null, null, 'Invalid from datestamp. Use YYYY-MM-DD or YYYY-MM-DDThh:mm:ssZ.'];
            }
        }

        if (is_string($untilRaw) && trim($untilRaw) !== '') {
            $until = OaiPmh::parseDatestamp($untilRaw);

            if ($until === null) {
                return [null, null, 'Invalid until datestamp. Use YYYY-MM-DD or YYYY-MM-DDThh:mm:ssZ.'];
            }

            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', trim($untilRaw)) === 1) {
                $until = $until->endOfDay();
            }
        }

        if ($from !== null && $until !== null && $from->greaterThan($until)) {
            return [null, null, 'The from argument must be less than or equal to until.'];
        }

        return [$from, $until, null];
    }

    private function listRecordsPage(
        array $requestParams,
        OaiDcMapper $dcMapper,
        ?CarbonImmutable $from,
        ?CarbonImmutable $until,
        ?CarbonImmutable $cursorUpdatedAt,
        ?int $cursorId
    ): Response {
        $max = (int) config('oai.max_records', 200);
        $max = $max > 0 ? $max : 200;

        $query = TriDharma::query()
            ->where('status', 'published');

        if ($from !== null) {
            $query->where('updated_at', '>=', $from);
        }

        if ($until !== null) {
            $query->where('updated_at', '<=', $until);
        }

        if ($cursorUpdatedAt !== null && $cursorId !== null) {
            $query->where(function ($q) use ($cursorUpdatedAt, $cursorId): void {
                $q->where('updated_at', '>', $cursorUpdatedAt)
                    ->orWhere(function ($q2) use ($cursorUpdatedAt, $cursorId): void {
                        $q2->where('updated_at', '=', $cursorUpdatedAt)
                            ->where('id', '>', $cursorId);
                    });
            });
        }

        $documents = $query
            ->orderBy('updated_at')
            ->orderBy('id')
            ->with([
                'authors:id,name,deleted_at,slug',
                'category:id,name,slug,deleted_at',
                'documentType:id,name,slug',
                'faculty:id,name,slug,deleted_at',
                'studyProgram:id,name,slug,deleted_at',
            ])
            ->limit($max + 1)
            ->get();

        if ($documents->isEmpty()) {
            return $this->renderError($requestParams, 'noRecordsMatch', 'No records match the request.');
        }

        $hasMore = $documents->count() > $max;
        $page = $hasMore ? $documents->take($max) : $documents;

        $records = $page
            ->map(fn (TriDharma $doc) => $this->buildRecord($doc, $dcMapper))
            ->values()
            ->all();

        $token = null;

        if ($hasMore) {
            $last = $page->last();

            $cursorUpdatedAt = CarbonImmutable::instance($last->updated_at ?? $last->created_at)->utc();

            $tokenPayload = [
                'metadataPrefix' => 'oai_dc',
                'from' => $from?->format('Y-m-d\\TH:i:s\\Z'),
                'until' => $until?->format('Y-m-d\\TH:i:s\\Z'),
                'cursorUpdatedAt' => $cursorUpdatedAt->format('Y-m-d\\TH:i:s\\Z'),
                'cursorId' => (int) $last->id,
            ];

            $token = ResumptionToken::encode($tokenPayload);
        }

        return $this->render(
            request: $requestParams,
            verb: 'ListRecords',
            data: [
                'records' => $records,
                'resumptionToken' => $token,
            ]
        );
    }

    /**
     * @return array{header: array{identifier:string, datestamp:string}, metadata: array{dc: array}}
     */
    private function buildRecord(TriDharma $document, OaiDcMapper $dcMapper): array
    {
        $datestamp = OaiPmh::datestamp($document->updated_at ?? $document->created_at);

        return [
            'header' => [
                'identifier' => OaiPmh::oaiIdentifierForId((int) $document->id),
                'datestamp' => $datestamp,
            ],
            'metadata' => [
                'dc' => $dcMapper->map($document),
            ],
        ];
    }

    private function hasOnlyAllowedArguments(array $params, array $allowedKeys): bool
    {
        $allowed = array_fill_keys($allowedKeys, true);

        foreach ($params as $key => $value) {
            if (! array_key_exists($key, $allowed)) {
                if ($value === null || (is_string($value) && trim($value) === '')) {
                    continue;
                }

                return false;
            }

            if ($key !== 'verb' && $value !== null && is_string($value) && trim($value) === '') {
                continue;
            }
        }

        return true;
    }

    private function render(array $request, string $verb, array $data): Response
    {
        return response()
            ->view('oai-pmh.response', [
                'responseDate' => OaiPmh::responseDate(),
                'requestUrl' => route('oai', [], true),
                'requestAttributes' => $this->requestAttributes($request),
                'verb' => $verb,
                'error' => null,
                'data' => $data,
            ])
            ->header('Content-Type', 'text/xml; charset=UTF-8');
    }

    private function renderError(array $request, string $code, string $message): Response
    {
        return response()
            ->view('oai-pmh.response', [
                'responseDate' => OaiPmh::responseDate(),
                'requestUrl' => route('oai', [], true),
                'requestAttributes' => $this->requestAttributes($request),
                'verb' => is_string($request['verb'] ?? null) ? (string) $request['verb'] : null,
                'error' => [
                    'code' => $code,
                    'message' => OaiPmh::xmlSafe($message),
                ],
                'data' => [],
            ])
            ->header('Content-Type', 'text/xml; charset=UTF-8');
    }

    /**
     * @return array<string, string>
     */
    private function requestAttributes(array $request): array
    {
        $keys = ['verb', 'identifier', 'metadataPrefix', 'from', 'until', 'set', 'resumptionToken'];

        $attributes = [];

        foreach ($keys as $key) {
            $value = $request[$key] ?? null;

            if (! is_string($value) || trim($value) === '') {
                continue;
            }

            $attributes[$key] = OaiPmh::xmlSafe(trim($value));
        }

        return $attributes;
    }
}
