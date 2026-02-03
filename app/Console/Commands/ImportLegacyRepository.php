<?php

namespace App\Console\Commands;

use App\Models\Author;
use App\Models\Categories;
use App\Models\Degree;
use App\Models\DocumentType;
use App\Models\Faculty;
use App\Models\ProgramType;
use App\Models\StudyProgram;
use App\Models\TriDharma;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportLegacyRepository extends Command
{
    protected $signature = 'etl:legacy
        {--file=repository.sql : Path ke file SQL legacy}
        {--truncate : Hapus data target sebelum import}
        {--dry-run : Hanya parsing tanpa menyimpan ke DB}
        {--created-by= : Override user_id untuk created_by}';

    protected $description = 'Import repository.sql legacy ke skema database baru.';

    public function handle(): int
    {
        $path = base_path((string) $this->option('file'));

        if (! is_file($path)) {
            $this->error("File tidak ditemukan: {$path}");
            return self::FAILURE;
        }

        $legacy = $this->parseLegacyFile($path);

        $this->info('Legacy parsed.');
        $this->line(' - faculties: '.count($legacy['faculties']));
        $this->line(' - categories: '.count($legacy['categories']));
        $this->line(' - authors: '.count($legacy['authors']));
        $this->line(' - research: '.count($legacy['research']));
        $this->line(' - author_has_research: '.count($legacy['author_has_research']));

        if ($this->option('dry-run')) {
            $this->info('Dry-run selesai (tanpa write).');
            return self::SUCCESS;
        }

        $createdById = $this->resolveCreatedById();
        if (! $createdById) {
            return self::FAILURE;
        }

        if ($this->option('truncate')) {
            $this->truncateTargetTables();
        }

        DB::transaction(function () use ($legacy, $createdById): void {
            [$degreeIdByCode, $degreeSuffixByCode] = $this->ensureDegrees();
            $programTypeIdByCode = $this->ensureProgramTypes();

            $facultyIdByName = $this->ensureFaculties($legacy['faculties']);
            $categoryIdBySlug = $this->ensureTriDharmaCategories();
            $documentTypeIdByStatus = $this->ensureDocumentTypes($legacy['research']);

            $studyProgramIdByName = $this->ensureStudyPrograms(
                $legacy['categories'],
                $legacy['research'],
                $facultyIdByName,
                $degreeIdByCode,
                $degreeSuffixByCode,
                $programTypeIdByCode
            );

            $authorIdByLegacy = $this->ensureAuthors($legacy['authors']);

            $triDharmaIdByLegacy = $this->ensureTriDharmas(
                $legacy['research'],
                $studyProgramIdByName,
                $documentTypeIdByStatus,
                $categoryIdBySlug,
                $createdById
            );

            $this->attachAuthors(
                $legacy['author_has_research'],
                $authorIdByLegacy,
                $triDharmaIdByLegacy
            );
        });

        $this->info('ETL selesai.');
        return self::SUCCESS;
    }

    private function resolveCreatedById(): ?int
    {
        $override = $this->option('created-by');
        if (is_numeric($override)) {
            return (int) $override;
        }

        $superAdminId = User::role('super_admin')->orderBy('id')->value('id');
        if (! $superAdminId) {
            $this->error('User dengan role super_admin tidak ditemukan. Buat user super_admin terlebih dahulu atau pakai --created-by=');
            return null;
        }

        return (int) $superAdminId;
    }

    private function truncateTargetTables(): void
    {
        $this->warn('Truncate target tables...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('author_tri_dharma')->truncate();
        DB::table('tri_dharmas')->truncate();
        DB::table('authors')->truncate();
        DB::table('study_programs')->truncate();
        DB::table('faculties')->truncate();
        DB::table('document_types')->truncate();
        DB::table('categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function ensureDegrees(): array
    {
        $now = now();

        $degrees = [
            ['name' => 'Sarjana (S1)', 'code' => 'Bachelor', 'slug_suffix' => 's1'],
            ['name' => 'Magister (S2)', 'code' => 'Master', 'slug_suffix' => 's2'],
            ['name' => 'Doktor (S3)', 'code' => 'Doctorate', 'slug_suffix' => 's3'],
            ['name' => 'Diploma Tiga (D3)', 'code' => 'Diploma3', 'slug_suffix' => 'd3'],
            ['name' => 'Diploma Empat (D4)', 'code' => 'Diploma4', 'slug_suffix' => 'd4'],
        ];

        foreach ($degrees as $degree) {
            Degree::updateOrCreate(
                ['code' => $degree['code']],
                [
                    'name' => $degree['name'],
                    'slug_suffix' => $degree['slug_suffix'],
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        return [
            Degree::query()->pluck('id', 'code')->all(),
            Degree::query()->pluck('slug_suffix', 'code')->all(),
        ];
    }

    private function ensureProgramTypes(): array
    {
        $now = now();

        $programTypes = [
            ['name' => 'Akademik', 'code' => 'Academic'],
            ['name' => 'Vokasional', 'code' => 'Vocational'],
            ['name' => 'Profesi', 'code' => 'Profession'],
            ['name' => 'Spesialis', 'code' => 'Specialist'],
        ];

        foreach ($programTypes as $type) {
            ProgramType::updateOrCreate(
                ['code' => $type['code']],
                [
                    'name' => $type['name'],
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        return ProgramType::query()->pluck('id', 'code')->all();
    }

    private function ensureFaculties(array $legacyFaculties): array
    {
        $now = now();
        $facultyNames = [];

        foreach ($legacyFaculties as $faculty) {
            $name = $this->normalizeText($faculty['name']);
            if ($name === '' || $name === 'Pasca Sarjana') {
                continue;
            }

            $facultyNames[$name] = true;
        }

        // tambahan sesuai requirement
        $facultyNames['Ekonomi dan Bisnis'] = true;

        $existingSlugs = Faculty::query()->pluck('slug', 'name')->all();
        $usedSlugs = array_filter($existingSlugs);

        $idByName = [];

        foreach (array_keys($facultyNames) as $name) {
            $faculty = Faculty::withTrashed()->where('name', $name)->first();
            if ($faculty) {
                if ($faculty->trashed()) {
                    $faculty->restore();
                }
                $idByName[$name] = $faculty->id;
                continue;
            }

            $slug = $this->uniqueSlug(Str::slug($name), $usedSlugs);
            $usedSlugs[] = $slug;

            $faculty = Faculty::create([
                'name' => $name,
                'slug' => $slug,
                'kode' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $idByName[$name] = $faculty->id;
        }

        return $idByName;
    }

    private function ensureTriDharmaCategories(): array
    {
        $items = [
            ['name' => 'Pendidikan', 'slug' => 'pendidikan'],
            ['name' => 'Penelitian', 'slug' => 'penelitian'],
            ['name' => 'Pengabdian', 'slug' => 'pengabdian'],
        ];

        $now = now();

        foreach ($items as $item) {
            Categories::withTrashed()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'name' => $item['name'],
                    'deleted_at' => null,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }

        return Categories::query()->pluck('id', 'slug')->all();
    }

    private function ensureDocumentTypes(array $legacyResearch): array
    {
        $names = [];
        foreach ($legacyResearch as $row) {
            $status = $this->normalizeText($row['status']);

            if ($status === '' || $this->looksLikeHtml($status) || mb_strlen($status) > 100) {
                $status = 'Lainnya';
            }

            $names[$status] = true;
        }

        $now = now();
        $idByStatus = [];

        foreach (array_keys($names) as $status) {
            $name = Str::limit($status, 250, '');
            $slugBase = Str::limit($status, 150, '');
            $slug = Str::slug($slugBase);
            if ($slug === '') {
                $slug = 'lainnya';
            }
            $type = DocumentType::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );

            $idByStatus[$status] = $type->id;
        }

        return $idByStatus;
    }

    private function ensureStudyPrograms(
        array $legacyCategories,
        array $legacyResearch,
        array $facultyIdByName,
        array $degreeIdByCode,
        array $degreeSuffixByCode,
        array $programTypeIdByCode
    ): array {
        $programNames = [];

        foreach ($legacyCategories as $row) {
            $name = $this->normalizeText($row['name']);
            if ($name !== '') {
                $programNames[$name] = true;
            }
        }

        foreach ($legacyResearch as $row) {
            $name = $this->normalizeText($row['category']);
            if ($name !== '') {
                $programNames[$name] = true;
            }
        }

        $usedSlugs = StudyProgram::query()->pluck('slug')->filter()->values()->all();
        $idByName = [];
        $now = now();

        foreach (array_keys($programNames) as $name) {
            [$degreeId, $programTypeId, $degreeSuffix] = $this->resolveDegreeAndProgramType(
                $name,
                $degreeIdByCode,
                $degreeSuffixByCode,
                $programTypeIdByCode
            );

            $facultyName = $this->resolveFacultyNameForProgram($name);
            $facultyId = $facultyIdByName[$facultyName] ?? null;
            if (! $facultyId) {
                $facultyId = $facultyIdByName['Ekonomi'] ?? (array_values($facultyIdByName)[0] ?? null);
            }
            if (! $facultyId) {
                throw new \RuntimeException("Faculty tidak ditemukan untuk program: {$name}");
            }

            $existing = StudyProgram::withTrashed()
                ->where('name', $name)
                ->where('faculty_id', $facultyId)
                ->first();

            if ($existing) {
                if ($existing->trashed()) {
                    $existing->restore();
                }

                $existing->update([
                    'degree_id' => $degreeId,
                    'program_type_id' => $programTypeId,
                ]);

                $idByName[$name] = $existing->id;
                continue;
            }

            $baseSlug = Str::slug(trim($name.' '.$degreeSuffix));
            $slug = $this->uniqueSlug($baseSlug !== '' ? $baseSlug : 'program', $usedSlugs);
            $usedSlugs[] = $slug;

            $program = StudyProgram::create([
                'name' => $name,
                'slug' => $slug,
                'kode' => null,
                'faculty_id' => $facultyId,
                'degree_id' => $degreeId,
                'program_type_id' => $programTypeId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $idByName[$name] = $program->id;
        }

        return $idByName;
    }

    private function ensureAuthors(array $legacyAuthors): array
    {
        $now = now();
        $usedSlugs = Author::query()->pluck('slug')->filter()->values()->all();
        $idByLegacy = [];

        foreach ($legacyAuthors as $row) {
            $legacyId = (int) $row['id'];
            $name = $this->normalizeText(trim($row['firstname'].' '.$row['middlename'].' '.$row['lastname']));
            $email = strtolower(trim((string) $row['email']));

            if ($name === '') {
                $name = $email !== '' ? $email : 'Author '.$legacyId;
            }

            $author = Author::withTrashed()->where('email', $email)->first();
            if (! $author) {
                $slug = $this->uniqueSlug(Str::slug($name) ?: 'author', $usedSlugs);
                $usedSlugs[] = $slug;

                $author = Author::create([
                    'name' => $name,
                    'slug' => $slug,
                    'email' => $email,
                    'bio' => $row['bio'],
                    'image_url' => $row['image'],
                    'identifier' => 'legacy:'.$legacyId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            } else {
                if ($author->trashed()) {
                    $author->restore();
                }

                $updates = [
                    'name' => $name,
                    'bio' => $row['bio'],
                    'image_url' => $row['image'],
                    'identifier' => $author->identifier ?: 'legacy:'.$legacyId,
                    'updated_at' => $now,
                ];

                if (! $author->slug) {
                    $slug = $this->uniqueSlug(Str::slug($name) ?: 'author', $usedSlugs);
                    $usedSlugs[] = $slug;
                    $updates['slug'] = $slug;
                }

                $author->update($updates);
            }

            $idByLegacy[$legacyId] = $author->id;
        }

        return $idByLegacy;
    }

    private function ensureTriDharmas(
        array $legacyResearch,
        array $studyProgramIdByName,
        array $documentTypeIdByStatus,
        array $categoryIdBySlug,
        int $createdById
    ): array {
        $now = now();
        $usedSlugs = TriDharma::query()->pluck('slug')->filter()->values()->all();
        $idByLegacy = [];

        TriDharma::withoutEvents(function () use (
            $legacyResearch,
            $studyProgramIdByName,
            $documentTypeIdByStatus,
            $categoryIdBySlug,
            $createdById,
            $now,
            &$usedSlugs,
            &$idByLegacy
        ): void {
            foreach ($legacyResearch as $row) {
                $legacyId = (int) $row['id'];
                $title = $this->normalizeText($row['title']);
                if ($title === '') {
                    $title = 'Dokumen '.$legacyId;
                }

                $programName = $this->normalizeText($row['category']);
                $studyProgramId = $studyProgramIdByName[$programName] ?? null;
                $facultyId = null;

                if (! $studyProgramId) {
                    throw new \RuntimeException("Study program tidak ditemukan untuk kategori: {$programName}");
                }

                if ($studyProgramId) {
                    $facultyId = StudyProgram::query()->whereKey($studyProgramId)->value('faculty_id');
                }

                $status = $this->normalizeText($row['status']);
                if ($status === '') {
                    $status = 'Lainnya';
                }

                $documentTypeId = $documentTypeIdByStatus[$status]
                    ?? ($documentTypeIdByStatus['Lainnya'] ?? null);
                if (! $documentTypeId) {
                    throw new \RuntimeException("Document type tidak ditemukan untuk status: {$status}");
                }
                $triCategoryId = $this->resolveTriDharmaCategoryId($status, $categoryIdBySlug);

                $abstract = $row['summary'] !== '' ? $row['summary'] : $row['description'];

                $publishYear = $this->normalizeYear($row['year'], $row['updated_at']);
                $fileName = $this->normalizeFilename($row['document']);
                $filePath = $fileName !== '' ? 'tri_dharmas/'.ltrim($fileName, '/') : '';

                $updatedAt = $this->parseLegacyTimestamp($row['updated_at'], $now);

                $slugBase = Str::slug($title);
                $slug = $slugBase !== '' ? $slugBase : 'dokumen';
                if (in_array($slug, $usedSlugs, true)) {
                    $slug = $slugBase !== '' ? $slugBase.'-'.$legacyId : 'dokumen-'.$legacyId;
                }
                $slug = $this->uniqueSlug($slug, $usedSlugs);
                $usedSlugs[] = $slug;

                $triDharma = TriDharma::withTrashed()->where('slug', $slug)->first();

                $payload = [
                    'title' => $title,
                    'slug' => $slug,
                    'abstract' => $abstract !== '' ? $abstract : null,
                    'category_id' => $triCategoryId,
                    'document_type_id' => $documentTypeId,
                    'faculty_id' => $facultyId,
                    'study_program_id' => $studyProgramId,
                    'publish_year' => $publishYear,
                    'status' => 'published',
                    'file_path' => $filePath,
                    'file_size' => null,
                    'download_count' => 0,
                    'created_by' => $createdById,
                    'updated_at' => $updatedAt,
                    'created_at' => $updatedAt,
                ];

                if ($triDharma) {
                    if ($triDharma->trashed()) {
                        $triDharma->restore();
                    }
                    $triDharma->update($payload);
                } else {
                    $triDharma = TriDharma::create($payload);
                }

                $idByLegacy[$legacyId] = $triDharma->id;
            }
        });

        return $idByLegacy;
    }

    private function attachAuthors(array $legacyPivot, array $authorIdByLegacy, array $triDharmaIdByLegacy): void
    {
        $rows = [];
        $now = now();

        foreach ($legacyPivot as $row) {
            $legacyAuthorId = (int) $row['author_id'];
            $legacyResearchId = (int) $row['research_id'];

            if (! isset($authorIdByLegacy[$legacyAuthorId], $triDharmaIdByLegacy[$legacyResearchId])) {
                continue;
            }

            $rows[] = [
                'author_id' => $authorIdByLegacy[$legacyAuthorId],
                'tri_dharma_id' => $triDharmaIdByLegacy[$legacyResearchId],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($rows === []) {
            return;
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('author_tri_dharma')->insertOrIgnore($chunk);
        }
    }

    private function resolveDegreeAndProgramType(
        string $programName,
        array $degreeIdByCode,
        array $degreeSuffixByCode,
        array $programTypeIdByCode
    ): array {
        $lower = strtolower($programName);

        if (str_starts_with($lower, 'profesi')) {
            return [
                null,
                $programTypeIdByCode['Profession'] ?? null,
                '',
            ];
        }

        if (str_starts_with($lower, 'd3')) {
            return [
                $degreeIdByCode['Diploma3'] ?? null,
                $programTypeIdByCode['Vocational'] ?? null,
                $degreeSuffixByCode['Diploma3'] ?? 'd3',
            ];
        }

        if (str_starts_with($lower, 'd4')) {
            return [
                $degreeIdByCode['Diploma4'] ?? null,
                $programTypeIdByCode['Vocational'] ?? null,
                $degreeSuffixByCode['Diploma4'] ?? 'd4',
            ];
        }

        if (str_starts_with($lower, 's1')) {
            return [
                $degreeIdByCode['Bachelor'] ?? null,
                $programTypeIdByCode['Academic'] ?? null,
                $degreeSuffixByCode['Bachelor'] ?? 's1',
            ];
        }

        if (str_starts_with($lower, 's2') || str_contains($lower, 'magister')) {
            return [
                $degreeIdByCode['Master'] ?? null,
                $programTypeIdByCode['Academic'] ?? null,
                $degreeSuffixByCode['Master'] ?? 's2',
            ];
        }

        if (str_starts_with($lower, 's3')) {
            return [
                $degreeIdByCode['Doctorate'] ?? null,
                $programTypeIdByCode['Academic'] ?? null,
                $degreeSuffixByCode['Doctorate'] ?? 's3',
            ];
        }

        return [
            $degreeIdByCode['Bachelor'] ?? null,
            $programTypeIdByCode['Academic'] ?? null,
            $degreeSuffixByCode['Bachelor'] ?? 's1',
        ];
    }

    private function resolveFacultyNameForProgram(string $programName): string
    {
        $map = [
            'S1 Farmasi' => 'Kedokteran',
            'S1 Kedokteran' => 'Kedokteran',
            'S1 Ilmu Keperawatan' => 'Kedokteran',
            'Profesi Ners' => 'Kedokteran',
            'Profesi Bidan' => 'Kedokteran',
            'S1 Kebidanan' => 'Kedokteran',
            'D3 Kebidanan' => 'Kedokteran',
            'D4 Kebidanan' => 'Kedokteran',
            'S1 Ners' => 'Kedokteran',
            'S1 Psikologi' => 'Kedokteran',
            'S1 Teknik Sipil' => 'Teknik',
            'S1 Teknik Mesin' => 'Teknik',
            'S1 Teknik Elektro' => 'Teknik',
            'S1 Sistem Informasi' => 'Teknik',
            'S2 Perencanaan Wilayah' => 'Teknik',
            'S1 Manajemen' => 'Ekonomi',
            'S1 Akuntansi' => 'Ekonomi',
            'Magister Manajemen Sains' => 'Ekonomi',
            'Magister Akuntansi' => 'Ekonomi',
            'S3 Manajemen' => 'Ekonomi dan Bisnis',
            'Ilmu Hukum' => 'Hukum',
            'Magister Hukum' => 'Hukum',
            'Magister Kenotariatan' => 'Hukum',
        ];

        if (isset($map[$programName])) {
            return $map[$programName];
        }

        $lower = strtolower($programName);

        if (str_contains($lower, 'hukum')) {
            return 'Hukum';
        }

        if (str_contains($lower, 'teknik') || str_contains($lower, 'sistem informasi') || str_contains($lower, 'perencanaan wilayah')) {
            return 'Teknik';
        }

        if (
            str_contains($lower, 'kedokteran') ||
            str_contains($lower, 'kebidanan') ||
            str_contains($lower, 'keperawatan') ||
            str_contains($lower, 'ners') ||
            str_contains($lower, 'farmasi') ||
            str_contains($lower, 'bidan')
        ) {
            return 'Kedokteran';
        }

        if (str_contains($lower, 'manajemen') || str_contains($lower, 'akuntansi')) {
            return 'Ekonomi';
        }

        return 'Ekonomi';
    }

    private function resolveTriDharmaCategoryId(string $status, array $categoryIdBySlug): int
    {
        $upper = strtoupper($status);

        if (str_contains($upper, 'PENGAJARAN')) {
            return (int) ($categoryIdBySlug['pendidikan'] ?? 0);
        }

        if (str_contains($upper, 'PENGABDIAN')) {
            return (int) ($categoryIdBySlug['pengabdian'] ?? 0);
        }

        return (int) ($categoryIdBySlug['penelitian'] ?? 0);
    }

    private function normalizeYear(?string $year, ?string $fallbackDate): int
    {
        $year = $this->normalizeText($year ?? '');
        if (preg_match('/\\d{4}/', $year, $matches)) {
            return (int) $matches[0];
        }

        if ($fallbackDate) {
            try {
                return (int) Carbon::parse($fallbackDate)->format('Y');
            } catch (\Throwable $e) {
                // ignore
            }
        }

        return (int) now()->format('Y');
    }

    private function parseLegacyTimestamp(?string $value, Carbon $fallback): Carbon
    {
        $value = trim((string) $value);
        if ($value === '') {
            return $fallback;
        }

        if (! preg_match('/^\\d{4}-\\d{2}-\\d{2}/', $value)) {
            return $fallback;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            return $fallback;
        }
    }

    private function uniqueSlug(string $base, array $usedSlugs): string
    {
        $slug = $base !== '' ? $base : 'item';
        $suffix = 2;

        while (in_array($slug, $usedSlugs, true)) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    private function normalizeText(?string $text): string
    {
        $text = trim((string) $text);
        $text = preg_replace('/\\s+/', ' ', $text) ?? $text;
        return $text;
    }

    private function looksLikeHtml(string $value): bool
    {
        return str_contains($value, '<') || str_contains($value, '</');
    }

    private function normalizeFilename(?string $text): string
    {
        return trim((string) $text);
    }

    private function parseLegacyFile(string $path): array
    {
        $data = [
            'faculties' => [],
            'categories' => [],
            'authors' => [],
            'author_has_research' => [],
            'research' => [],
        ];

        $file = new \SplFileObject($path);

        while (! $file->eof()) {
            $line = trim((string) $file->fgets());
            if ($line === '') {
                continue;
            }

            if ($row = $this->parseInsertLine($line, 'fakultas')) {
                $data['faculties'][] = [
                    'id' => $row[0],
                    'name' => $row[1] ?? '',
                ];
                continue;
            }

            if ($row = $this->parseInsertLine($line, 'category')) {
                $data['categories'][] = [
                    'id' => $row[0],
                    'name' => $row[1] ?? '',
                ];
                continue;
            }

            if ($row = $this->parseInsertLine($line, 'author')) {
                $data['authors'][] = [
                    'id' => $row[0],
                    'firstname' => $row[1] ?? '',
                    'middlename' => $row[2] ?? '',
                    'lastname' => $row[3] ?? '',
                    'email' => $row[4] ?? '',
                    'image' => $row[5] ?? '',
                    'bio' => $row[6] ?? '',
                ];
                continue;
            }

            if ($row = $this->parseInsertLine($line, 'author_has_research')) {
                $data['author_has_research'][] = [
                    'author_id' => $row[1] ?? null,
                    'research_id' => $row[2] ?? null,
                ];
                continue;
            }

            if ($row = $this->parseInsertLine($line, 'research')) {
                $data['research'][] = [
                    'id' => $row[0],
                    'title' => $row[1] ?? '',
                    'peneliti' => $row[2] ?? '',
                    'category' => $row[3] ?? '',
                    'fakultas' => $row[4] ?? '',
                    'document' => $row[5] ?? '',
                    'location' => $row[6] ?? '',
                    'year' => $row[7] ?? '',
                    'description' => $row[8] ?? '',
                    'summary' => $row[9] ?? '',
                    'status' => $row[10] ?? '',
                    'quantity' => $row[11] ?? null,
                    'user' => $row[12] ?? null,
                    'updated_at' => $row[13] ?? null,
                ];
                continue;
            }
        }

        return $data;
    }

    private function parseInsertLine(string $line, string $table): ?array
    {
        $prefix = "INSERT INTO `{$table}` VALUES ";
        if (! str_starts_with($line, $prefix)) {
            return null;
        }

        $start = strpos($line, '(');
        $end = strrpos($line, ')');
        if ($start === false || $end === false || $end <= $start) {
            return null;
        }

        $payload = substr($line, $start + 1, $end - $start - 1);
        return $this->parseValues($payload);
    }

    private function parseValues(string $payload): array
    {
        $values = [];
        $length = strlen($payload);
        $i = 0;

        while ($i < $length) {
            while ($i < $length && (ctype_space($payload[$i]) || $payload[$i] === ',')) {
                $i++;
            }

            if ($i >= $length) {
                break;
            }

            if ($payload[$i] === "'") {
                $i++;
                $buffer = '';
                while ($i < $length) {
                    $ch = $payload[$i];
                    if ($ch === '\\\\') {
                        if ($i + 1 < $length) {
                            $buffer .= $payload[$i + 1];
                            $i += 2;
                            continue;
                        }
                    }

                    if ($ch === "'" && ($i + 1 < $length && $payload[$i + 1] === "'")) {
                        $buffer .= "'";
                        $i += 2;
                        continue;
                    }

                    if ($ch === "'") {
                        $i++;
                        break;
                    }

                    $buffer .= $ch;
                    $i++;
                }

                $values[] = $buffer;
                continue;
            }

            $j = $i;
            while ($j < $length && $payload[$j] !== ',') {
                $j++;
            }

            $token = trim(substr($payload, $i, $j - $i));
            if (strcasecmp($token, 'NULL') === 0 || $token === '') {
                $values[] = null;
            } else {
                $values[] = $token;
            }

            $i = $j + 1;
        }

        return $values;
    }
}
