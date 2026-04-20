<?php
 
namespace App\Http\Controllers\Module5;
 
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Models\Module5\FieldOffice;
use App\Models\Module5\OfficeType;
use App\Models\Module5\PositionTitle;
use App\Models\Module5\PesoInfoSection;
use App\Models\Module5\PesoObjective;
use App\Models\Module5\PesoCoreService;
use App\Models\Module5\PesoHowToAvail;
use App\Models\Module5\PesoBeneficiary;
use App\Models\Module5\PesoCarouselSlide;
use App\Models\Module5\PesoDirectoryPublish;
use Illuminate\Support\Facades\Storage;

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
        PesoDirectoryPublish::singleton()->update(['has_draft_changes' => true]);
    }

    // ===================================================
    // PUBLIC PAGE  —  GET /peso-directory
    // ===================================================
    public function index(): View
    {
        $publish  = PesoDirectoryPublish::singleton();
        $full     = $publish->published_snapshot ?? [];

        // Field offices are everything except the 'peso_info' key
        $snapshot = collect($full)->except('peso_info')->toArray();
        $pesoProvinceKeys = collect(array_keys($snapshot));

        $pesoInfo = $full['peso_info'] ?? [
            'description'   => '',
            'objective'     => '',
            'how_to_avail'  => '',
            'core_services' => [],
            'beneficiaries' => [],
        ];

        $slides = PesoCarouselSlide::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($s) => str_starts_with($s->image_path, 'images/')
        ? asset($s->image_path)
        : asset('storage/' . $s->image_path)
    )
            ->values()
            ->toArray();

        return view('Public.Module5.peso-directory', compact('snapshot', 'pesoProvinceKeys', 'pesoInfo', 'slides'));
    }

    // ===================================================
    // ADMIN EDITOR  —  GET /admin/peso-directory
    // ===================================================
    public function adminIndex(): View
    {
        // Eager-load positionTitle so blade can access $o->positionTitle->name
        $fieldOffices = FieldOffice::with('positionTitle', 'officeType')->ordered()->get();
        $pesoInfo     = $this->buildPesoInfo();

        $publish            = PesoDirectoryPublish::singleton();
        $directoryPublished = !is_null($publish->published_at);
        $publishedAt        = $publish->published_at?->toIso8601String();
        $directoryHasDraft  = (bool) $publish->has_draft_changes;
        $directoryChangelog = [];

        $pesoInfoPublished   = $directoryPublished;
        $pesoInfoPublishedAt = $publishedAt;
        $pesoInfoHasDraft    = $directoryHasDraft;
        $pesoInfoChangelog   = [];

        $slides = PesoCarouselSlide::where('is_active', true)
                        ->orderBy('sort_order')
                        ->get();

        return view('admin.Module5.pesoDirectory-editor', compact(
            'fieldOffices',
            'pesoInfo',
            'directoryPublished',
            'directoryHasDraft',
            'directoryChangelog',
            'publishedAt',
            'pesoInfoPublished',
            'pesoInfoHasDraft',
            'pesoInfoChangelog',
            'pesoInfoPublishedAt',
            'slides'
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
                    'core_services', 'beneficiaries', 'extra_sections'];
 
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
            'label' => $request->input('label'),
            'time'  => now()->toIso8601String(),
        ]);
 
        return response()->json(['success' => true]);
    }

    // ===================================================
    // PESO INFO — POST /admin/peso-info/publish
    // ===================================================
    public function publishPesoInfo(): JsonResponse
    {
        $publish  = PesoDirectoryPublish::singleton();
        $existing = $publish->published_snapshot ?? [];

        // Merge updated peso_info into the existing snapshot (keeps field offices intact)
        $existing['peso_info'] = $this->buildPesoInfo();

        $publish->update([
            'published_snapshot' => $existing,
            'published_at'       => now(),
            'has_draft_changes'  => false,
        ]);

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
        // Return only office types actually assigned to at least one field office,
        // so the admin filter buttons match what's in the directory (same as the public page).
        $usedTypeIds = FieldOffice::whereNotNull('office_type_id')
                            ->distinct()
                            ->pluck('office_type_id');

        $names = OfficeType::whereIn('id', $usedTypeIds)
                    ->orderBy('name')
                    ->pluck('name');

        return response()->json($names);
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

        // Resolve office type name → ID
        // firstOrCreate ensures PESO/JPO are auto-added to office_types if missing
        $ot = OfficeType::firstOrCreate(
            ['name' => strtoupper(trim($data['office_type']))]
        );
        $data['office_type_id'] = $ot->id;
        unset($data['office_type']);

        // Resolve position title name → ID
        $data['position_title_id'] = null;
        if (!empty($data['position_title'])) {
            $pt = PositionTitle::firstOrCreate(['name' => $data['position_title']]);
            $data['position_title_id'] = $pt->id;
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

        // Resolve office type name → ID
        // firstOrCreate ensures PESO/JPO are auto-added to office_types if missing
        $ot = OfficeType::firstOrCreate(
            ['name' => strtoupper(trim($data['office_type']))]
        );
        $data['office_type_id'] = $ot->id;
        unset($data['office_type']);

        // Resolve position title name → ID
        $data['position_title_id'] = null;
        if (!empty($data['position_title'])) {
            $pt = PositionTitle::firstOrCreate(['name' => $data['position_title']]);
            $data['position_title_id'] = $pt->id;
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
        $officesSnapshot = FieldOffice::with('positionTitle', 'officeType')->ordered()->get()
            ->groupBy('province')
            ->map(fn($offices) => $offices->map(fn($o) => [
                'id'             => $o->id,
                'name'           => $o->name,
                'position_title' => $o->positionTitle->name ?? '',
                'persons_name'   => $o->persons_name ?? '',
                'email'          => $o->email ?? '',
                'address'        => $o->address ?? '',
                'type'           => $o->officeType->name ?? '',
                'province'       => $o->province,
            ])->values())
            ->toArray();

        // Preserve any existing peso_info snapshot and merge new offices snapshot
        $publish  = PesoDirectoryPublish::singleton();
        $existing = $publish->published_snapshot ?? [];

        $fullSnapshot = array_merge($officesSnapshot, [
            'peso_info' => $existing['peso_info'] ?? $this->buildPesoInfo(),
        ]);

        $publish->update([
            'published_snapshot' => $fullSnapshot,
            'published_at'       => now(),
            'has_draft_changes'  => false,
        ]);

        return response()->json(['success' => true, 'published_at' => now()->toIso8601String()]);
    }
 
    public function touchDirectory(Request $request): JsonResponse
    {
        PesoDirectoryPublish::singleton()->update(['has_draft_changes' => true]);
        return response()->json(['success' => true]);
    }

    // ===================================================
    // PESO CAROUSEL SLIDES
    // ===================================================

    /** Helper: store an uploaded image and return its path */
    private function storeImage($file, string $folder): string
    {
        return $file->store($folder, 'public');
    }

    /** Helper: return the current active slides as the front-end expects */
    private function slidesPayload(): array
    {
        return PesoCarouselSlide::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($s) => [
                'id'         => $s->id,
                'image'      => str_starts_with($s->image_path, 'images/')
                ? asset($s->image_path)
                : asset('storage/' . $s->image_path),
            'sort_order' => $s->sort_order,
            ])
            ->values()
            ->toArray();
    }

    /** POST /admin/peso-carousel-slides */
    public function storeCarouselSlide(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|max:5120',
        ]);

        $path = $this->storeImage($request->file('image'), 'peso-carousel');

        PesoCarouselSlide::create([
            'image_path'  => $path,
            'sort_order'  => (PesoCarouselSlide::where('is_active', true)->max('sort_order') ?? -1) + 1,
            'is_active'   => true,
        ]);

        return response()->json(['success' => true, 'slides' => $this->slidesPayload()]);
    }

    /** PUT /admin/peso-carousel-slides/{slide} */
    public function updateCarouselSlide(Request $request, PesoCarouselSlide $pesoCarouselSlide): JsonResponse
    {
        $request->validate([
            'image' => 'nullable|image|max:5120',
        ]);

        $data = [];

        if ($request->hasFile('image')) {
            // Delete the old WebP from storage
            Storage::disk('public')->delete($pesoCarouselSlide->image_path);
            // Store the new upload as WebP
            $data['image_path'] = $this->storeImage($request->file('image'), 'peso-carousel');
        }

        $pesoCarouselSlide->update($data);

        return response()->json(['success' => true, 'slides' => $this->slidesPayload()]);
    }

    /** DELETE /admin/peso-carousel-slides/{slide} */
    public function destroyCarouselSlide(PesoCarouselSlide $pesoCarouselSlide): JsonResponse
    {
        Storage::disk('public')->delete($pesoCarouselSlide->image_path);
        $pesoCarouselSlide->delete();

        return response()->json(['success' => true, 'slides' => $this->slidesPayload()]);
    }
}