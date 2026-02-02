<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tri_dharmas', function (Blueprint $table): void {
            $table->string('slug')->nullable()->after('title');
        });

        Schema::table('authors', function (Blueprint $table): void {
            $table->string('slug')->nullable()->after('name');
        });

        $this->backfillTriDharmaSlugs();
        $this->backfillAuthorSlugs();

        Schema::table('tri_dharmas', function (Blueprint $table): void {
            $table->unique('slug');
        });

        Schema::table('authors', function (Blueprint $table): void {
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('tri_dharmas', function (Blueprint $table): void {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });

        Schema::table('authors', function (Blueprint $table): void {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }

    protected function backfillTriDharmaSlugs(): void
    {
        DB::table('tri_dharmas')
            ->select(['id', 'title', 'slug'])
            ->orderBy('id')
            ->chunkById(200, function ($rows): void {
                foreach ($rows as $row) {
                    if (is_string($row->slug) && trim($row->slug) !== '') {
                        continue;
                    }

                    $base = Str::slug((string) $row->title);
                    $base = $base !== '' ? $base : 'dokumen';

                    $slug = $base;

                    if (DB::table('tri_dharmas')->where('slug', $slug)->where('id', '<>', $row->id)->exists()) {
                        $slug = $base.'-'.$row->id;
                    }

                    if ($slug === '') {
                        $slug = 'dokumen-'.$row->id;
                    }

                    DB::table('tri_dharmas')->where('id', $row->id)->update([
                        'slug' => $slug,
                    ]);
                }
            });
    }

    protected function backfillAuthorSlugs(): void
    {
        DB::table('authors')
            ->select(['id', 'name', 'slug'])
            ->orderBy('id')
            ->chunkById(200, function ($rows): void {
                foreach ($rows as $row) {
                    if (is_string($row->slug) && trim($row->slug) !== '') {
                        continue;
                    }

                    $base = Str::slug((string) $row->name);
                    $base = $base !== '' ? $base : 'author';

                    $slug = $base;

                    if (DB::table('authors')->where('slug', $slug)->where('id', '<>', $row->id)->exists()) {
                        $slug = $base.'-'.$row->id;
                    }

                    if ($slug === '') {
                        $slug = 'author-'.$row->id;
                    }

                    DB::table('authors')->where('id', $row->id)->update([
                        'slug' => $slug,
                    ]);
                }
            });
    }
};
