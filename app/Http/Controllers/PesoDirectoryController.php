<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use App\Models\FieldOffice;
use App\Models\OfficeType;
use App\Models\PositionTitle;
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
    // ===================================================
    public function index(): View
    {
        $snapshot         = Cache::get('field_offices_published_snapshot', []);
        $pesoProvinceKeys = collect(array_keys($snapshot));

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
        // Eager-load positionTitle so blade can access $o->positionTitle->name
        $fieldOffices       = FieldOffice::with('positionTitle')->ordered()->get();
        $pesoInfo           = $this->buildPesoInfo();
        $directoryPublished = Cache::has('field_offices_published_snapshot');
        $lastModifiedAt     = Cache::get('field_offices_last_modified_at');
        $publishedAt        = Cache::get('field_offices_published_at');
        $directoryHasDraft  = $lastModifiedAt && (!$publishedAt || $lastModifiedAt > $publishedAt);
        $directoryChangelog = $directoryHasDraft ? Cache::get('field_offices_changelog', []) : [];

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
    // ===================================================
    public function getPesoInfo(): JsonResponse
    {
        return response()->json($this->buildPesoInfo());
    }

    // ===================================================
    // PESO INFO — PUT /admin/peso-info/{key}
    // Saves to DB (draft) — does NOT update the public snapshot
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
                $obj ? $obj->update(['content' => $value])
                     : PesoObjective::create(['content' => $value, 'is_active' => true]);
                break;

            case 'how_to_avail':
                $avail = PesoHowToAvail::where('is_active', true)->first();
                $avail ? $avail->update(['content' => $value])
                       : PesoHowToAvail::create(['content' => $value, 'is_active' => true]);
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

        $this->touchPesoInfo([
            'field' => $key,
            'time'  => now()->toIso8601String(),
        ]);
 
        return response()->json(['success' => true]);
    }

    // ===================================================
    // PESO INFO — POST /admin/peso-info/publish
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
 
    // ── Helper: sync a list table ──
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
    // POSITION TITLES
    // ===================================================
    public function getPositionTitles(): JsonResponse
    {
        return response()->json(PositionTitle::orderBy('name')->pluck('name'));
    }

    public function storePositionTitle(Request $request): JsonResponse
    {
        $request->validate(['name' => 'required|string|max:100|unique:position_titles,name']);
        $pt = PositionTitle::create(['name' => trim($request->name)]);
        return response()->json(['success' => true, 'name' => $pt->name]);
    }

    public function updatePositionTitle(Request $request, string $name): JsonResponse
    {
        $request->validate(['name' => 'required|string|max:100|unique:position_titles,name']);
        $pt      = PositionTitle::where('name', trim($name))->firstOrFail();
        $newName = trim($request->name);
        $pt->update(['name' => $newName]);
        return response()->json(['success' => true, 'name' => $newName]);
    }

    public function destroyPositionTitle(string $name): JsonResponse
    {
        PositionTitle::where('name', trim($name))->firstOrFail()->delete();
        return response()->json(['success' => true]);
    }

    // ===================================================
    // FIELD OFFICES
    // ===================================================
    public function storeFieldOffice(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'office_type'    => 'required|string|max:100',
            'province'       => 'required|string|max:255',
            'position_title' => 'nullable|string|max:255',
            'persons_name'   => 'nullable|string|max:255',
            'email'          => 'nullable|email|max:255',
            'address'        => 'nullable|string|max:500',
        ]);

        // Resolve position title name → ID
        $data['position_title_id'] = null;
        if (!empty($data['position_title'])) {
            $pt = PositionTitle::where('name', $data['position_title'])->first();
            $data['position_title_id'] = $pt?->id;
        }
        unset($data['position_title']);

        $data['sort_order'] = FieldOffice::where('province', $data['province'])->max('sort_order') + 1;
        $office = FieldOffice::create($data);
        return response()->json(['success' => true, 'id' => $office->id]);
    }
 
    public function updateFieldOffice(Request $request, FieldOffice $office): JsonResponse
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'office_type'    => 'required|string|max:100',
            'province'       => 'required|string|max:255',
            'position_title' => 'nullable|string|max:255',
            'persons_name'   => 'nullable|string|max:255',
            'email'          => 'nullable|email|max:255',
            'address'        => 'nullable|string|max:500',
        ]);

        // Resolve position title name → ID
        $data['position_title_id'] = null;
        if (!empty($data['position_title'])) {
            $pt = PositionTitle::where('name', $data['position_title'])->first();
            $data['position_title_id'] = $pt?->id;
        }
        unset($data['position_title']);

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
        $snapshot = FieldOffice::with('positionTitle')->ordered()->get()
            ->groupBy('province')
            ->map(fn($offices) => $offices->map(fn($o) => [
                'id'             => $o->id,
                'name'           => $o->name,
                'position_title' => $o->positionTitle->name ?? '',
                'persons_name'   => $o->persons_name ?? '',
                'email'          => $o->email ?? '',
                'address'        => $o->address ?? '',
                'type'           => $o->office_type,
                'province'       => $o->province,
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