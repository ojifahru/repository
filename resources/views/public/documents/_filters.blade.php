@php
    $scope = $scope ?? 'sidebar';
    $idPrefix = $scope === 'drawer' ? 'drawer' : 'sidebar';
@endphp

<div class="space-y-4" data-doc-filters data-doc-filters-scope="{{ $scope }}">
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
            <label class="text-sm font-medium text-gray-800" for="{{ $idPrefix }}_author_id">Author</label>
            <select id="{{ $idPrefix }}_author_id"
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
            <label class="text-sm font-medium text-gray-800" for="{{ $idPrefix }}_faculty_id">Fakultas</label>
            <select id="{{ $idPrefix }}_faculty_id"
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
            <label class="text-sm font-medium text-gray-800" for="{{ $idPrefix }}_study_program_id">Program Studi</label>
            <select id="{{ $idPrefix }}_study_program_id"
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
            <label class="text-sm font-medium text-gray-800" for="{{ $idPrefix }}_category_id">Kategori</label>
            <select id="{{ $idPrefix }}_category_id"
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
            <label class="text-sm font-medium text-gray-800" for="{{ $idPrefix }}_document_type_id">Jenis Dokumen</label>
            <select id="{{ $idPrefix }}_document_type_id"
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
            <label class="text-sm font-medium text-gray-800" for="{{ $idPrefix }}_publish_year">Tahun</label>
            <select id="{{ $idPrefix }}_publish_year"
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

    <script>
        (function() {
            var root = document.currentScript && document.currentScript.closest('[data-doc-filters]');
            if (!root) {
                return;
            }

            var scope = root.getAttribute('data-doc-filters-scope');
            var facultySelect = root.querySelector('select[name="faculty_id"]');
            var studyProgramSelect = root.querySelector('select[name="study_program_id"]');

            if (!facultySelect || !studyProgramSelect) {
                return;
            }

            var allStudyPrograms = @json($filterOptions['studyProgramsAll'] ?? []);

            function isActiveScope() {
                var isDesktop = window.matchMedia('(min-width: 768px)').matches;
                return scope === 'sidebar' ? isDesktop : !isDesktop;
            }

            function setControlsEnabled(enabled) {
                var controls = root.querySelectorAll('select, button, input, textarea');
                for (var i = 0; i < controls.length; i++) {
                    controls[i].disabled = !enabled;
                }
            }

            function buildOption(value, label, selected) {
                var option = document.createElement('option');
                option.value = value;
                option.textContent = label;
                if (selected) {
                    option.selected = true;
                }
                return option;
            }

            function refreshStudyProgramsOptions() {
                var selectedFacultyId = facultySelect.value;
                var currentStudyProgramId = studyProgramSelect.value;

                var filtered = allStudyPrograms;
                if (selectedFacultyId) {
                    filtered = allStudyPrograms.filter(function(sp) {
                        return String(sp.faculty_id) === String(selectedFacultyId);
                    });
                }

                var isCurrentStillValid = !currentStudyProgramId
                    || filtered.some(function(sp) {
                        return String(sp.id) === String(currentStudyProgramId);
                    });

                studyProgramSelect.innerHTML = '';
                studyProgramSelect.appendChild(buildOption('', 'Semua', !isCurrentStillValid));

                for (var i = 0; i < filtered.length; i++) {
                    var sp = filtered[i];
                    var selected = isCurrentStillValid && String(sp.id) === String(currentStudyProgramId);
                    studyProgramSelect.appendChild(buildOption(String(sp.id), sp.name, selected));
                }

                if (!isCurrentStillValid) {
                    studyProgramSelect.value = '';
                }
            }

            function syncScopeState() {
                var active = isActiveScope();
                setControlsEnabled(active);

                if (active) {
                    refreshStudyProgramsOptions();
                }
            }

            facultySelect.addEventListener('change', function() {
                refreshStudyProgramsOptions();
            });

            syncScopeState();
            window.addEventListener('resize', syncScopeState);
        })();
    </script>
</div>
