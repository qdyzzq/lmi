<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use App\Models\FieldOffice;
use App\Models\OfficeType;
use App\Models\PesoInfoSection;
use App\Models\PesoObjective;
use App\Models\PesoCoreService;
use App\Models\PesoHowToAvail;
use App\Models\PesoDoleProgram;
use App\Models\PesoBeneficiary;
 
class PesoDirectoryController extends Controller
{
    // --------------------------------------------------
    // HELPER: build the live $pesoInfo array from DB
    // Used by the admin editor and by publishPesoInfo()
    // --------------------------------------------------
    private function buildPesoInfo(): array
    {
        return [
            'description'   => PesoInfoSection::getContent('about'),
            'objective'     => PesoObjective::getContent(),
            'how_to_avail'  => PesoHowToAvail::getContent(),
            'core_services' => PesoCoreService::active()->get()
                                ->map(fn($s) => ['id' => $s->id, 'name' => $s->name])
                                ->values()->toArray(),
            'dole_programs' => PesoDoleProgram::active()->get()
                                ->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'acronym' => $p->acronym])
                                ->values()->toArray(),
            'beneficiaries' => PesoBeneficiary::active()->get()
                                ->map(fn($b) => ['id' => $b->id, 'name' => $b->name])
                                ->values()->toArray(),
            'extra_sections' => json_decode(
                                    PesoInfoSection::where('section_key', 'extra_sections')
                                        ->value('content') ?? '[]',
                                    true
                                ) ?? [],
        ];
    }

    // --------------------------------------------------
    // HELPER: stamp a draft modification on PESO Info
    // Called after every individual Save button write
    // --------------------------------------------------
    private function touchPesoInfo(array $entry = []): void
    {
        Cache::put('peso_info_last_modified_at', now()->toIso8601String(), now()->addYears(10));

        if (!empty($entry)) {
            $log   = Cache::get('peso_info_changelog', []);
            $log[] = $entry;
            Cache::put('peso_info_changelog', $log, now()->addYears(10));
        }
    }

    // ===================================================
    // PUBLIC PAGE  —  GET /peso-directory
    // Reads ONLY from the published snapshot — never live DB
    // ===================================================
    public function index(): View
    {
        // Field offices snapshot (existing)
        $snapshot         = Cache::get('field_offices_published_snapshot', []);
        $pesoProvinceKeys = collect(array_keys($snapshot));

        // PESO Info snapshot — falls back to empty structure if never published
        $pesoInfo = Cache::get('peso_info_published_snapshot', [
            'description'   => '',
            'objective'     => '',
            'how_to_avail'  => '',
            'core_services' => [],
            'dole_programs' => [],
            'beneficiaries' => [],
        ]);

        return view('peso-directory', compact('snapshot', 'pesoProvinceKeys', 'pesoInfo'));
    }

    // ===================================================
    // ADMIN EDITOR  —  GET /admin/peso-directory
    // ===================================================
    public function adminIndex(): View
    {
        $fieldOffices       = FieldOffice::ordered()->get();
        $pesoInfo           = $this->buildPesoInfo();          // always live DB for the editor
        $directoryPublished = Cache::has('field_offices_published_snapshot');
        $lastModifiedAt     = Cache::get('field_offices_last_modified_at');
        $publishedAt        = Cache::get('field_offices_published_at');
        $directoryHasDraft  = $lastModifiedAt && (!$publishedAt || $lastModifiedAt > $publishedAt);
        $directoryChangelog = $directoryHasDraft ? Cache::get('field_offices_changelog', []) : [];

        // PESO Info draft state
        $pesoInfoPublishedAt  = Cache::get('peso_info_published_at');
        $pesoInfoLastModified = Cache::get('peso_info_last_modified_at');
        $pesoInfoPublished    = Cache::has('peso_info_published_snapshot');
        $pesoInfoHasDraft     = $pesoInfoLastModified &&
                                (!$pesoInfoPublishedAt || $pesoInfoLastModified > $pesoInfoPublishedAt);
        $pesoInfoChangelog    = $pesoInfoHasDraft ? Cache::get('peso_info_changelog', []) : [];

        return view('admin.pesoDirectory-editor', compact(
            'fieldOffices',
            'pesoInfo',
            'directoryPublished',
            'directoryHasDraft',
            'directoryChangelog',
            'publishedAt',
            'pesoInfoPublished',
            'pesoInfoHasDraft',
            'pesoInfoChangelog',
            'pesoInfoPublishedAt'
        ));
    }

    // ===================================================
    // PESO INFO — GET /admin/peso-info
    // Returns full live pesoInfo as JSON
    // ===================================================
    public function getPesoInfo(): JsonResponse
    {
        return response()->json($this->buildPesoInfo());
    }

    // ===================================================
    // PESO INFO — PUT /admin/peso-info/{key}
    // Saves to DB (draft) — does NOT update the public snapshot
    // After saving, stamps a draft modification timestamp
    // ===================================================
    public function updatePesoInfo(Request $request, string $key): JsonResponse
    {
        $allowed = ['description', 'objective', 'how_to_avail',
                    'core_services', 'dole_programs', 'beneficiaries', 'extra_sections'];
 
        if (!in_array($key, $allowed)) {
            return response()->json(['success' => false, 'message' => 'Invalid key.'], 422);
        }
 
        $request->validate(['value' => 'required|string']);
        $value = $request->value;
 
        switch ($key) {
 
            case 'description':
                PesoInfoSection::updateOrCreate(
                    ['section_key' => 'about'],
                    ['title' => 'What is PESO?', 'content' => $value, 'is_active' => true]
                );
                break;
 
            case 'objective':
                $obj = PesoObjective::where('is_active', true)->first();
                if ($obj) {
                    $obj->update(['content' => $value]);
                } else {
                    PesoObjective::create(['content' => $value, 'is_active' => true]);
                }
                break;
 
            case 'how_to_avail':
                $avail = PesoHowToAvail::where('is_active', true)->first();
                if ($avail) {
                    $avail->update(['content' => $value]);
                } else {
                    PesoHowToAvail::create(['content' => $value, 'is_active' => true]);
                }
                break;
 
            case 'core_services':
                $items = json_decode($value, true);
                if (!is_array($items)) {
                    return response()->json(['success' => false, 'message' => 'Invalid JSON for core_services.'], 422);
                }
                $this->syncList(
                    PesoCoreService::class,
                    $items,
                    fn($item) => ['name' => $item['name'], 'is_active' => true]
                );
                break;
 
            case 'dole_programs':
                $items = json_decode($value, true);
                if (!is_array($items)) {
                    return response()->json(['success' => false, 'message' => 'Invalid JSON for dole_programs.'], 422);
                }
                $this->syncList(
                    PesoDoleProgram::class,
                    $items,
                    fn($item) => ['name' => $item['name'], 'acronym' => $item['acronym'] ?? null, 'is_active' => true]
                );
                break;
 
            case 'beneficiaries':
                $items = json_decode($value, true);
                if (!is_array($items)) {
                    return response()->json(['success' => false, 'message' => 'Invalid JSON for beneficiaries.'], 422);
                }
                $this->syncList(
                    PesoBeneficiary::class,
                    $items,
                    fn($item) => ['name' => $item['name'], 'is_active' => true]
                );
                break;

            case 'extra_sections':
                // Stored as a JSON blob in the peso_info_sections table under section_key 'extra_sections'
                $items = json_decode($value, true);
                if (!is_array($items)) {
                    return response()->json(['success' => false, 'message' => 'Invalid JSON for extra_sections.'], 422);
                }
                PesoInfoSection::updateOrCreate(
                    ['section_key' => 'extra_sections'],
                    ['title' => 'Extra Sections', 'content' => $value, 'is_active' => true]
                );
                break;
        }

        // Stamp the draft — public page still shows the old snapshot until publishPesoInfo()
        $this->touchPesoInfo([
            'field' => $key,
            'time'  => now()->toIso8601String(),
        ]);
 
        return response()->json(['success' => true]);
    }

    // ===================================================
    // PESO INFO — POST /admin/peso-info/publish
    // Freezes current DB state into a Cache snapshot
    // that the public page will now read from
    // ===================================================
    public function publishPesoInfo(): JsonResponse
    {
        $snapshot = $this->buildPesoInfo();

        Cache::put('peso_info_published_snapshot', $snapshot, now()->addYears(10));
        Cache::put('peso_info_published_at', now()->toIso8601String(), now()->addYears(10));
        Cache::forget('peso_info_last_modified_at');
        Cache::forget('peso_info_changelog');

        return response()->json([
            'success'      => true,
            'published_at' => now()->toIso8601String(),
        ]);
    }
 
    // ── Helper: sync a list table (delete removed, update existing, insert new) ──
    private function syncList(string $modelClass, array $items, callable $mapper): void
    {
        $incomingIds = collect($items)->pluck('id')->filter()->values()->toArray();
 
        $modelClass::where('is_active', true)
            ->when(!empty($incomingIds), fn($q) => $q->whereNotIn('id', $incomingIds))
            ->when(empty($incomingIds),  fn($q) => $q)
            ->update(['is_active' => false]);
 
        foreach (array_values($items) as $i => $item) {
            $attrs = array_merge($mapper($item), ['sort_order' => $i + 1]);
            if (!empty($item['id'])) {
                $modelClass::where('id', $item['id'])->update($attrs);
            } else {
                $modelClass::create($attrs);
            }
        }
    }
 
    // ===================================================
    // OFFICE TYPES  
    // ===================================================
 
    public function getOfficeTypes(): JsonResponse
    {
        return response()->json(OfficeType::orderBy('name')->pluck('name'));
    }
 
    public function storeOfficeType(Request $request): JsonResponse
    {
        $request->validate(['name' => 'required|string|max:50|unique:office_types,name']);
        $type = OfficeType::create(['name' => strtoupper(trim($request->name))]);
        return response()->json(['success' => true, 'name' => $type->name]);
    }
 
    public function updateOfficeType(Request $request, string $name): JsonResponse
    {
        $request->validate(['name' => 'required|string|max:50|unique:office_types,name']);
        $type    = OfficeType::where('name', strtoupper(trim($name)))->firstOrFail();
        $newName = strtoupper(trim($request->name));
        $type->update(['name' => $newName]);
        return response()->json(['success' => true, 'name' => $newName]);
    }
 
    public function destroyOfficeType(string $name): JsonResponse
    {
        OfficeType::where('name', strtoupper(trim($name)))->firstOrFail()->delete();
        return response()->json(['success' => true]);
    }
 
    // ===================================================
    // FIELD OFFICES  
    // ===================================================
 
    public function storeFieldOffice(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'office_type'  => 'required|string|max:100',
            'province'     => 'required|string|max:255',
            'manager_name' => 'nullable|string|max:255',
            'email'        => 'nullable|email|max:255',
            'address'      => 'nullable|string|max:500',
        ]);
        $data['sort_order'] = FieldOffice::where('province', $data['province'])->max('sort_order') + 1;
        $office = FieldOffice::create($data);
        return response()->json(['success' => true, 'id' => $office->id]);
    }
 
    public function updateFieldOffice(Request $request, FieldOffice $office): JsonResponse
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'office_type'  => 'required|string|max:100',
            'province'     => 'required|string|max:255',
            'manager_name' => 'nullable|string|max:255',
            'email'        => 'nullable|email|max:255',
            'address'      => 'nullable|string|max:500',
        ]);
        $office->update($data);
        return response()->json(['success' => true]);
    }
 
    public function destroyFieldOffice(FieldOffice $office): JsonResponse
    {
        $office->delete();
        return response()->json(['success' => true]);
    }
 
    public function publishDirectory(): JsonResponse
    {
        $snapshot = FieldOffice::ordered()->get()
            ->groupBy('province')
            ->map(fn($offices) => $offices->map(fn($o) => [
                'id'       => $o->id,
                'name'     => $o->name,
                'manager'  => $o->manager_name ?? '',
                'email'    => $o->email ?? '',
                'address'  => $o->address ?? '',
                'type'     => $o->office_type,
                'province' => $o->province,
            ])->values())
            ->toArray();
 
        Cache::put('field_offices_published_snapshot', $snapshot, now()->addYears(10));
        Cache::put('field_offices_published_at', now()->toIso8601String(), now()->addYears(10));
        Cache::forget('field_offices_last_modified_at');
        Cache::forget('field_offices_changelog');
 
        return response()->json(['success' => true, 'published_at' => now()->toIso8601String()]);
    }
 
    public function touchDirectory(Request $request): JsonResponse
    {
        Cache::put('field_offices_last_modified_at', now()->toIso8601String(), now()->addYears(10));
        $entry = $request->only(['action', 'label', 'type', 'province', 'time']);
        if (!empty($entry)) {
            $log   = Cache::get('field_offices_changelog', []);
            $log[] = $entry;
            Cache::put('field_offices_changelog', $log, now()->addYears(10));
        }
        return response()->json(['success' => true]);
    }
}