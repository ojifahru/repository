<?php

namespace App\Console\Commands;

use App\Models\TriDharma;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CleanupDuplicateTitles extends Command
{
    protected $signature = 'repo:dedupe-titles
        {--dry-run : Tampilkan perubahan tanpa menghapus data}
        {--keep=oldest : Pilih "oldest" atau "newest" sebagai data yang dipertahankan}
        {--yes : Lewati konfirmasi sebelum soft delete}';

    protected $description = 'Soft delete dokumen duplikat berdasarkan judul yang dinormalisasi.';

    public function handle(): int
    {
        $keepMode = strtolower((string) $this->option('keep'));
        if (! in_array($keepMode, ['oldest', 'newest'], true)) {
            $this->error('Nilai --keep harus "oldest" atau "newest".');
            return self::FAILURE;
        }

        $duplicates = $this->findDuplicateTitles();

        if ($duplicates->isEmpty()) {
            $this->info('Tidak ada judul duplikat yang ditemukan.');
            return self::SUCCESS;
        }

        $this->info('Ditemukan '.$duplicates->count().' judul duplikat.');

        if (! $this->option('dry-run') && ! $this->option('yes')) {
            if (! $this->confirm('Lanjutkan soft delete data duplikat?')) {
                $this->warn('Dibatalkan.');
                return self::SUCCESS;
            }
        }

        $deletedTotal = 0;

        foreach ($duplicates as $normalizedTitle) {
            $rows = $this->fetchRowsByNormalizedTitle($normalizedTitle, $keepMode);
            if ($rows->count() <= 1) {
                continue;
            }

            $keepRow = $rows->first();
            $deleteRows = $rows->slice(1);
            $deleteIds = $deleteRows->pluck('id')->all();

            if ($this->option('dry-run')) {
                $this->line('Judul: '.$normalizedTitle);
                $this->line(' - keep: '.$keepRow->id.' | '.$keepRow->title);
                $this->line(' - delete: '.implode(',', $deleteIds));
                continue;
            }

            TriDharma::query()->whereIn('id', $deleteIds)->delete();
            $deletedTotal += count($deleteIds);
        }

        if ($this->option('dry-run')) {
            $this->info('Dry-run selesai. Tidak ada data yang dihapus.');
            return self::SUCCESS;
        }

        $this->info('Selesai. Total soft delete: '.$deletedTotal.' dokumen.');
        return self::SUCCESS;
    }

    protected function findDuplicateTitles(): Collection
    {
        return DB::table('tri_dharmas')
            ->selectRaw("TRIM(REGEXP_REPLACE(LOWER(title), '[[:space:]]+', ' ')) AS normalized_title")
            ->where('status', 'published')
            ->whereNull('deleted_at')
            ->groupBy('normalized_title')
            ->havingRaw('COUNT(*) > 1')
            ->orderBy('normalized_title')
            ->pluck('normalized_title');
    }

    protected function fetchRowsByNormalizedTitle(string $normalizedTitle, string $keepMode): Collection
    {
        $query = DB::table('tri_dharmas')
            ->select(['id', 'title', 'publish_year', 'created_at'])
            ->where('status', 'published')
            ->whereNull('deleted_at')
            ->whereRaw("TRIM(REGEXP_REPLACE(LOWER(title), '[[:space:]]+', ' ')) = ?", [$normalizedTitle]);

        if ($keepMode === 'newest') {
            $query->orderByDesc('id');
        } else {
            $query->orderBy('id');
        }

        return $query->get();
    }
}
