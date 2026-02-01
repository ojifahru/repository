<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div>
            <div class="text-sm font-semibold text-gray-900">Filter</div>
            <p class="mt-1 text-xs text-gray-600">Persempit hasil berdasarkan metadata.</p>
        </div>
        <a class="text-xs font-semibold text-gray-600 hover:text-gray-900"
            href="{{ route('public.documents.index') }}">Reset</a>
    </div>

    <div class="grid gap-3">
        <div>
            <label class="text-sm font-medium text-gray-800" for="author_id">Author</label>
            <select id="author_id"
                class="mt-1 w-full rounded-xl bg-white px-3 py-2 text-sm text-gray-900 shadow-sm ring-1 ring-gray-900/5 focus:outline-none focus:ring-4 focus:ring-indigo-500/15"
                name="author_id">
                <option value="">Semua</option>
                @foreach ($filterOptions['authors'] as $author)
                    <option value="{{ $author->id }}" @selected(($filters['author_id'] ?? null) == $author->id)>
                        {{ $author->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm font-medium text-gray-800" for="faculty_id">Fakultas</label>
            <select id="faculty_id"
                class="mt-1 w-full rounded-xl bg-white px-3 py-2 text-sm text-gray-900 shadow-sm ring-1 ring-gray-900/5 focus:outline-none focus:ring-4 focus:ring-indigo-500/15"
                name="faculty_id">
                <option value="">Semua</option>
                @foreach ($filterOptions['faculties'] as $faculty)
                    <option value="{{ $faculty->id }}" @selected(($filters['faculty_id'] ?? null) == $faculty->id)>
                        {{ $faculty->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm font-medium text-gray-800" for="study_program_id">Program Studi</label>
            <select id="study_program_id"
                class="mt-1 w-full rounded-xl bg-white px-3 py-2 text-sm text-gray-900 shadow-sm ring-1 ring-gray-900/5 focus:outline-none focus:ring-4 focus:ring-indigo-500/15"
                name="study_program_id">
                <option value="">Semua</option>
                @foreach ($filterOptions['studyPrograms'] as $studyProgram)
                    <option value="{{ $studyProgram->id }}" @selected(($filters['study_program_id'] ?? null) == $studyProgram->id)>
                        {{ $studyProgram->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm font-medium text-gray-800" for="category_id">Kategori</label>
            <select id="category_id"
                class="mt-1 w-full rounded-xl bg-white px-3 py-2 text-sm text-gray-900 shadow-sm ring-1 ring-gray-900/5 focus:outline-none focus:ring-4 focus:ring-indigo-500/15"
                name="category_id">
                <option value="">Semua</option>
                @foreach ($filterOptions['categories'] as $category)
                    <option value="{{ $category->id }}" @selected(($filters['category_id'] ?? null) == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm font-medium text-gray-800" for="document_type_id">Jenis Dokumen</label>
            <select id="document_type_id"
                class="mt-1 w-full rounded-xl bg-white px-3 py-2 text-sm text-gray-900 shadow-sm ring-1 ring-gray-900/5 focus:outline-none focus:ring-4 focus:ring-indigo-500/15"
                name="document_type_id">
                <option value="">Semua</option>
                @foreach ($filterOptions['documentTypes'] as $documentType)
                    <option value="{{ $documentType->id }}" @selected(($filters['document_type_id'] ?? null) == $documentType->id)>
                        {{ $documentType->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="text-sm font-medium text-gray-800" for="publish_year">Tahun</label>
            <select id="publish_year"
                class="mt-1 w-full rounded-xl bg-white px-3 py-2 text-sm text-gray-900 shadow-sm ring-1 ring-gray-900/5 focus:outline-none focus:ring-4 focus:ring-indigo-500/15"
                name="publish_year">
                <option value="">Semua</option>
                @foreach ($filterOptions['publishYears'] as $year)
                    <option value="{{ $year }}" @selected(($filters['publish_year'] ?? null) == $year)>
                        {{ $year }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="pt-1">
        <button
            class="inline-flex w-full items-center justify-center rounded-2xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/20"
            type="submit">
            Terapkan Filter
        </button>
    </div>
</div>
