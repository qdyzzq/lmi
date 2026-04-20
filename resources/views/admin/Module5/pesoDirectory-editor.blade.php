<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/logoIcon/dole_logo.png') }}">
    @vite('resources/css/app.css')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Quill Rich Text Editor --}}
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/fuse.js@7.0.0/dist/fuse.min.js"></script>

    {{-- Our JS must load BEFORE Alpine so all x-data functions exist when Alpine boots --}}
    @vite('resources/js/admin/Module5/peso-directory-editor.js')

    {{-- Alpine must be LAST — it reads x-data functions that must already be on window --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.8/dist/cdn.min.js"></script>
    <title>PESO / JPO Directory</title>

    <style>
        html {
            scroll-behavior: smooth;
        }

        [x-cloak] {
            display: none !important;
        }

        .plus-btn {
            transition: all 0.2s ease;
        }

        .plus-btn:hover {
            transform: scale(1.08);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.25);
        }

        .modal-backdrop {
            backdrop-filter: blur(4px);
        }

        .ql-toolbar.ql-snow {
            padding: 12px 8px;
            border-radius: 8px 8px 0 0;
        }

        .ql-toolbar.ql-snow .ql-formats {
            margin-right: 20px;
        }

        .ql-toolbar.ql-snow button {
            width: 32px !important;
            height: 32px !important;
            padding: 4px;
        }

        .ql-toolbar.ql-snow .ql-stroke {
            stroke-width: 2.5;
        }

        .ql-toolbar.ql-snow select {
            height: 32px !important;
            padding: 4px 8px;
        }

        .ql-container.ql-snow {
            border-radius: 0 0 8px 8px;
            max-height: 500px;
            overflow-y: auto;
        }

        .ql-editor {
            min-height: 120px;
            font-size: 14px;
            line-height: 1.6;
        }

        .ql-editor::-webkit-scrollbar { width: 8px; }
        .ql-editor::-webkit-scrollbar-track { background: #f1f5f9; }
        .ql-editor::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .ql-editor::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="8pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="8pt"]::before { content: '8'; }
        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="10pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="10pt"]::before { content: '10'; }
        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="11pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="11pt"]::before { content: '11'; }
        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="12pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="12pt"]::before { content: '12'; }
        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="14pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="14pt"]::before { content: '14'; }
        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="16pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="16pt"]::before { content: '16'; }
        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="18pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="18pt"]::before { content: '18'; }
        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="24pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="24pt"]::before { content: '24'; }
        .ql-snow .ql-picker.ql-size .ql-picker-label[data-value="36pt"]::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item[data-value="36pt"]::before { content: '36'; }
        .ql-snow .ql-picker.ql-size .ql-picker-label:not([data-value])::before,
        .ql-snow .ql-picker.ql-size .ql-picker-item:not([data-value])::before { content: '11'; }

        .ql-editor .ql-size-8pt  { font-size: 8pt;  }
        .ql-editor .ql-size-10pt { font-size: 10pt; }
        .ql-editor .ql-size-11pt { font-size: 11pt; }
        .ql-editor .ql-size-12pt { font-size: 12pt; }
        .ql-editor .ql-size-14pt { font-size: 14pt; }
        .ql-editor .ql-size-16pt { font-size: 16pt; }
        .ql-editor .ql-size-18pt { font-size: 18pt; }
        .ql-editor .ql-size-24pt { font-size: 24pt; }
        .ql-editor .ql-size-36pt { font-size: 36pt; }

        /* ===== CAROUSEL ===== */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .line-clamp-4 {
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* ===== DRAG & DROP UPLOAD ZONE ===== */
        .dropzone {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            background: #f8fafc;
            transition: all 0.2s ease;
            cursor: pointer;
            position: relative;
        }
        .dropzone:hover {
            border-color: #6366f1;
            background: #eef2ff;
        }
        .dropzone.drag-over {
            border-color: #6366f1;
            background: #eef2ff;
            transform: scale(1.01);
            box-shadow: 0 0 0 4px rgba(99,102,241,0.15);
        }
        .dropzone.has-preview {
            border-style: solid;
            border-color: #a5b4fc;
            background: #f5f3ff;
            padding: 0;
            overflow: hidden;
        }
        .dropzone-preview-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .dropzone-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            opacity: 0;
            transition: opacity 0.2s ease;
            border-radius: 10px;
        }
        .dropzone.has-preview:hover .dropzone-overlay {
            opacity: 1;
        }
        .dropzone-hidden-input {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }
    </style>
</head>

<body class="bg-slate-100 min-h-screen" x-data="adminPesoPage()">

    <div class="flex h-screen overflow-hidden">

        @include('partials.sidebar')

        <div class="flex-1 flex flex-col overflow-y-auto">

            <header
                class="bg-white h-16 shrink-0 border-b border-slate-200 flex items-center justify-between px-8 shadow-sm sticky top-0 z-50">
                <h2 class="text-xl font-bold text-slate-800">PESO / JPO Directory • Admin</h2>
            </header>

              {{-- ===== CAROUSEL SECTION ===== --}}
            @php
                $slidesJson = collect($slides ?? [])
                    ->map(
                        fn($s) => [
                            'id' => $s->id,
                            'image' => str_starts_with($s->image_path, 'images/')
                                ? asset($s->image_path)
                                : asset('storage/' . $s->image_path),
                            'sort_order' => $s->sort_order,
                        ],
                    )
                    ->toJson();
            @endphp

            <div id="carousel-section" class="relative w-full shrink-0 overflow-hidden"
                style="height: calc(100vh - 64px);"
                x-data="pesoCarouselSection({{ $slidesJson }})"
                x-init="startAutoplay()"
                @mouseenter="stopAutoplay()"
                @mouseleave="startAutoplay()">

                <template x-for="(slide, index) in slides" :key="index">
                    <div x-show="currentSlide === index"
                        x-transition:enter="transition ease-out duration-700"
                        x-transition:enter-start="opacity-0 transform translate-x-full"
                        x-transition:enter-end="opacity-100 transform translate-x-0"
                        x-transition:leave="transition ease-in duration-700"
                        x-transition:leave-start="opacity-100 transform translate-x-0"
                        x-transition:leave-end="opacity-0 transform -translate-x-full"
                        class="absolute inset-0">

                        {{-- Background image + overlay --}}
                        <div class="absolute inset-0">
                            <img :src="slide.image" alt="Carousel Slide"
                                class="w-full h-full object-cover object-center">
                            <div class="absolute inset-0 bg-gradient-to-b from-slate-900/40 via-slate-900/20 to-slate-900/50"></div>
                        </div>

                        {{-- Per-slide admin buttons (Edit / Delete) --}}
                        <div class="absolute top-6 right-6 z-30 flex gap-2">
                            <button @click="Alpine.$data(document.getElementById('carouselModalRoot')).handleOpen({ type: 'edit-slide', id: slide.id, data: { id: slide.id, image: slide.image, sort_order: slide.sort_order } })"
                                class="flex items-center gap-1.5 px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-lg transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit Slide
                            </button>
                            <button @click="Alpine.$data(document.getElementById('carouselModalRoot')).handleOpen({ type: 'delete-slide', id: slide.id })"
                                class="flex items-center gap-1.5 px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg shadow-lg transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete
                            </button>
                        </div>
                    </div>
                </template>

                {{-- Prev arrow --}}
                <button @click="prevSlide()"
                    class="absolute left-6 top-1/2 -translate-y-1/2 z-20 w-14 h-14 bg-white/25 hover:bg-white/40 backdrop-blur-md rounded-full flex items-center justify-center transition-all duration-300 border border-white/30">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                {{-- Next arrow --}}
                <button @click="nextSlide()"
                    class="absolute right-6 top-1/2 -translate-y-1/2 z-20 w-14 h-14 bg-white/25 hover:bg-white/40 backdrop-blur-md rounded-full flex items-center justify-center transition-all duration-300 border border-white/30">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                {{-- Dot indicators --}}
                <div class="absolute bottom-24 left-1/2 -translate-x-1/2 z-20 flex items-center gap-4">
                    <template x-for="(slide, index) in slides" :key="index">
                        <button @click="goToSlide(index)" class="transition-all duration-300"
                            :class="currentSlide === index ? 'w-16 h-4' : 'w-4 h-4'">
                            <div class="w-full h-full rounded-full backdrop-blur-md border-2"
                                :class="currentSlide === index ? 'bg-white border-white' : 'bg-white/40 border-white/60'">
                            </div>
                        </button>
                    </template>
                </div>

                {{-- Add Slide button --}}
                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-20">
                    <button @click="Alpine.$data(document.getElementById('carouselModalRoot')).handleOpen({ type: 'add-slide' })"
                        class="plus-btn flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm rounded-full shadow-2xl transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Carousel Slide
                    </button>
                </div>
            </div>
            {{-- ===== END CAROUSEL SECTION ===== --}}

            {{-- ===== CAROUSEL MODALS ===== --}}
            <div id="carouselModalRoot"
                x-data="pesoCarouselModals()"
                x-init="init()"
                @open-modal.window="handleOpen($event.detail)">

                {{-- ── ADD SLIDE MODAL ── --}}
                <div x-show="modal === 'add-slide'" x-cloak
                    class="fixed inset-0 z-[1100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
                    @keydown.escape.window="close()">
                    <div @click.stop class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100">
                        <div class="bg-gradient-to-r from-amber-500 to-amber-600 px-6 pt-7 pb-5 text-center">
                            <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-white">Add Carousel Slide</h3>
                        </div>
                        <div class="px-6 py-5 space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-2">Slide Image <span class="text-red-500">*</span></label>

                                {{-- Drag & Drop Zone --}}
                                <div class="dropzone h-44"
                                    :class="addPreview ? 'has-preview' : ''"
                                    id="add-dropzone"
                                    @dragover.prevent="$el.classList.add('drag-over')"
                                    @dragleave.prevent="$el.classList.remove('drag-over')"
                                    @drop.prevent="$el.classList.remove('drag-over'); handleDropAdd($event)">

                                    {{-- Empty state --}}
                                    <div x-show="!addPreview" class="flex flex-col items-center justify-center h-full gap-2 px-4 text-center pointer-events-none select-none">
                                        <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center mb-1">
                                            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm font-semibold text-slate-700">Drag & drop your image here</p>
                                        <p class="text-xs text-slate-400">or <span class="text-indigo-600 font-bold">click to browse</span></p>
                                        <p class="text-xs text-slate-400 mt-1">JPG, PNG, WebP, GIF · Max 5 MB</p>
                                    </div>

                                    {{-- Preview state --}}
                                    <template x-if="addPreview">
                                        <div class="w-full h-full relative">
                                            <img :src="addPreview" class="dropzone-preview-img">
                                            <div class="dropzone-overlay">
                                                <span class="text-white text-xs font-bold bg-black/50 px-3 py-1.5 rounded-lg">Click to change image</span>
                                            </div>
                                        </div>
                                    </template>

                                    {{-- Hidden file input --}}
                                    <input type="file" accept="image/jpeg,image/png,image/webp,image/gif"
                                        class="dropzone-hidden-input"
                                        @change="previewFile($event, 'add')">
                                </div>

                                {{-- File info row --}}
                                <div x-show="addFile" class="mt-2 flex items-center gap-2 text-xs text-slate-500" x-cloak>
                                    <svg class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span x-text="addFile?.name"></span>
                                    <span class="text-slate-400" x-text="addFile ? '(' + (addFile.size / 1024).toFixed(1) + ' KB)' : ''"></span>
                                    <button type="button" @click.stop="addFile = null; addPreview = null; addError = ''"
                                        class="ml-auto text-red-400 hover:text-red-600 transition font-semibold">Remove</button>
                                </div>
                            </div>
                            <p x-show="addError" x-text="addError" class="text-red-500 text-xs font-semibold" x-cloak></p>
                        </div>
                        <div class="flex gap-3 px-6 pb-6">
                            <button @click="close()" class="flex-1 px-4 py-2.5 border border-slate-300 text-slate-700 font-semibold rounded-lg text-sm hover:bg-slate-50 transition">Cancel</button>
                            <button @click="submitAdd()" :disabled="saving"
                                class="flex-1 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 disabled:opacity-50 text-white font-bold rounded-lg text-sm transition">
                                <span x-text="saving ? 'Uploading...' : 'Add Slide'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ── EDIT SLIDE MODAL ── --}}
                <div x-show="modal === 'edit-slide'" x-cloak
                    class="fixed inset-0 z-[1100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
                    @keydown.escape.window="close()">
                    <div @click.stop class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100">
                        <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 px-6 pt-7 pb-5 text-center">
                            <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-7 h-7 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-white">Edit Slide</h3>
                        </div>
                        <div class="px-6 py-5 space-y-4">
                            {{-- Current image preview --}}
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-2">Current / New Image</label>

                                {{-- Drag & Drop Zone --}}
                                <div class="dropzone h-44"
                                    :class="editPreview || editData.image ? 'has-preview' : ''"
                                    @dragover.prevent="$el.classList.add('drag-over')"
                                    @dragleave.prevent="$el.classList.remove('drag-over')"
                                    @drop.prevent="$el.classList.remove('drag-over'); handleDropEdit($event)">

                                    {{-- Empty state (no current image at all) --}}
                                    <div x-show="!editPreview && !editData.image" class="flex flex-col items-center justify-center h-full gap-2 px-4 text-center pointer-events-none select-none">
                                        <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center mb-1">
                                            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm font-semibold text-slate-700">Drag & drop your image here</p>
                                        <p class="text-xs text-slate-400">or <span class="text-indigo-600 font-bold">click to browse</span></p>
                                    </div>

                                    {{-- Image preview (new or existing) --}}
                                    <template x-if="editPreview || editData.image">
                                        <div class="w-full h-full relative">
                                            <img :src="editPreview || editData.image" class="dropzone-preview-img">
                                            <div class="dropzone-overlay">
                                                <span class="text-white text-xs font-bold bg-black/50 px-3 py-1.5 rounded-lg">
                                                    <span x-text="editFile ? 'New image selected — click to change' : 'Click or drag to replace image'"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </template>

                                    {{-- Hidden file input --}}
                                    <input type="file" accept="image/jpeg,image/png,image/webp,image/gif"
                                        class="dropzone-hidden-input"
                                        @change="previewFile($event, 'edit')">
                                </div>

                                {{-- File info row (only when a NEW file is selected) --}}
                                <div x-show="editFile" class="mt-2 flex items-center gap-2 text-xs text-slate-500" x-cloak>
                                    <svg class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span x-text="editFile?.name"></span>
                                    <span class="text-slate-400" x-text="editFile ? '(' + (editFile.size / 1024).toFixed(1) + ' KB)' : ''"></span>
                                    <button type="button" @click.stop="editFile = null; editPreview = null; editError = ''"
                                        class="ml-auto text-red-400 hover:text-red-600 transition font-semibold">Revert</button>
                                </div>

                                <p class="mt-1.5 text-xs text-slate-400">JPG, PNG, WebP, GIF · Max 5 MB · Leave unchanged to keep current image</p>
                            </div>
                            <p x-show="editError" x-text="editError" class="text-red-500 text-xs font-semibold" x-cloak></p>
                        </div>
                        <div class="flex gap-3 px-6 pb-6">
                            <button @click="close()" class="flex-1 px-4 py-2.5 border border-slate-300 text-slate-700 font-semibold rounded-lg text-sm hover:bg-slate-50 transition">Cancel</button>
                            <button @click="submitEdit()" :disabled="saving"
                                class="flex-1 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white font-bold rounded-lg text-sm transition">
                                <span x-text="saving ? 'Saving...' : 'Save Changes'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ── DELETE SLIDE MODAL ── --}}
                <div x-show="modal === 'delete-slide'" x-cloak
                    class="fixed inset-0 z-[1100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4"
                    @keydown.escape.window="close()">
                    <div @click.stop class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100">
                        <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 pt-7 pb-5 text-center">
                            <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-white">Delete Slide?</h3>
                        </div>
                        <div class="px-6 py-5 text-center">
                            <p class="text-sm text-slate-600 mb-5">This slide will be permanently removed from the carousel. This action cannot be undone.</p>
                            <div class="flex gap-3">
                                <button @click="close()" class="flex-1 px-4 py-2.5 border border-slate-300 text-slate-700 font-semibold rounded-lg text-sm hover:bg-slate-50 transition">Cancel</button>
                                <button @click="submitDelete()" :disabled="saving"
                                    class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white font-bold rounded-lg text-sm transition">
                                    <span x-text="saving ? 'Deleting...' : 'Yes, Delete'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== END CAROUSEL MODALS ===== --}}

            <div class="w-full py-16" style="padding-left: 7.5rem; padding-right: 7.5rem;">

                {{-- ===== PESO INFO CONTENT EDITOR ===== --}}
                <div id="peso-info-editor"
                    class="mb-8 bg-white rounded-2xl shadow border border-slate-200 overflow-hidden"
                    x-data="pesoInfoEditor()" x-init="init()" x-bind:data-has-draft="pesoInfoHasDraft">

                    {{-- Panel Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100"
                        style="background: linear-gradient(90deg, #f8fafc 0%, #eff6ff 100%);">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-700">PESO Info Section Editor</p>
                                <p class="text-xs text-slate-400 mt-0.5">Edit the "What is PESO?" content block shown on
                                    the public page</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">

                            {{-- Draft badge — visible when there are unpublished PESO Info changes --}}
                            <span x-show="pesoInfoHasDraft" x-cloak
                                class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1.5 rounded-lg bg-amber-50 text-amber-700 border border-amber-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                Unpublished changes
                            </span>

                            <button @click="collapsed = !collapsed" type="button"
                                class="flex items-center gap-1.5 text-xs font-semibold px-3 py-2 rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 transition">
                                <svg class="w-3.5 h-3.5 transition-transform duration-200"
                                    :class="collapsed ? '' : 'rotate-180'" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                                <span x-text="collapsed ? 'Expand' : 'Collapse'"></span>
                            </button>

                            {{-- Save All Changes: writes draft to DB only --}}
                            <button @click="openSaveAllConfirm()" :disabled="saving" type="button"
                                class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white text-xs font-bold rounded-lg transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span x-text="saving ? 'Saving...' : 'Save All Changes'"></span>
                            </button>

                            {{-- Publish PESO Info: pushes snapshot live to the public page --}}
                            <button @click="openPublishPesoInfoConfirm()" :disabled="publishing" type="button"
                                :class="pesoInfoHasDraft
                                    ?
                                    'bg-amber-500 hover:bg-amber-600' :
                                    'bg-emerald-600 hover:bg-emerald-700'"
                                class="flex items-center gap-2 px-4 py-2 disabled:opacity-50 text-white text-xs font-bold rounded-lg transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                </svg>
                                <span
                                    x-text="publishing ? 'Publishing...' : (pesoInfoHasDraft ? 'Publish Changes' : '{{ $pesoInfoPublished ? 'Update Published' : 'Publish to Public' }}')"></span>
                            </button>

                        </div>
                    </div>


                    {{-- Unpublished changes banner --}}
                    <div x-show="pesoInfoHasDraft && pesoInfoChangelog.length > 0" x-cloak
                        class="mx-6 mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse flex-shrink-0"></span>
                            <p class="text-xs font-bold text-amber-700 uppercase tracking-widest">Saved to draft — not
                                yet visible on the public page</p>
                        </div>
                        <ul class="space-y-1">
                            <template x-for="(entry, i) in pesoInfoChangelog" :key="i">
                                <li class="flex items-center gap-2 text-xs text-amber-800">
                                    <svg class="w-3 h-3 flex-shrink-0 text-amber-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <span class="font-semibold capitalize"
                                        x-text="entry.label ?? (entry.field?.replace(/_/g, ' ') ?? 'Field')"></span>
                                    <template x-if="entry.via === 'save_all'">
                                        <span
                                            class="inline-flex items-center px-1.5 py-0.5 rounded text-amber-600 bg-amber-100 border border-amber-200 font-bold uppercase tracking-wider"
                                            style="font-size:9px">via Save All</span>
                                    </template>
                                    <span class="text-amber-500">·</span>
                                    <span x-text="new Date(entry.time).toLocaleString()"></span>
                                </li>
                            </template>
                        </ul>
                        <p class="mt-2 text-xs text-amber-600 font-medium">Click <strong>Publish Changes</strong> to
                            make these live on the public page.</p>
                    </div>

                    {{-- Collapsible Body --}}
                    <div x-show="!collapsed" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0" class="p-6 space-y-6">

                        {{-- ── Description (Quill) ── --}}
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-5">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-6 h-6 rounded-lg bg-blue-600 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 6h16M4 12h16M4 18h7" />
                                        </svg>
                                    </div>
                                    <h4 class="text-xs font-bold text-slate-600 uppercase tracking-widest">What is
                                        PESO?
                                        (Description)</h4>
                                </div>
                                <button @click="saveKeyDraft('description', 'Description')" type="button"
                                    class="flex items-center gap-1 px-2.5 py-1 text-xs font-bold bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white border border-blue-200 rounded-lg transition">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Save
                                </button>
                            </div>
                            <div id="quill-peso-description" class="rounded-lg border border-slate-300 bg-white">
                            </div>
                            <div class="mt-1 text-xs text-slate-400"><span
                                    id="quill-peso-description-wordcount">0</span> words</div>
                        </div>

                        {{-- ── Objective (Quill) ── --}}
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-5">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-6 h-6 rounded-lg bg-blue-600 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h4 class="text-xs font-bold text-slate-600 uppercase tracking-widest">Objective
                                    </h4>
                                </div>
                                <button @click="saveKeyDraft('objective', 'Objective')" type="button"
                                    class="flex items-center gap-1 px-2.5 py-1 text-xs font-bold bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white border border-blue-200 rounded-lg transition">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Save
                                </button>
                            </div>
                            <div id="quill-peso-objective" class="rounded-lg border border-slate-300 bg-white"></div>
                            <div class="mt-1 text-xs text-slate-400"><span
                                    id="quill-peso-objective-wordcount">0</span> words</div>
                        </div>

                        {{-- ── How to Avail (Quill) ── --}}
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-5">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-6 h-6 rounded-lg bg-blue-600 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        </svg>
                                    </div>
                                    <h4 class="text-xs font-bold text-slate-600 uppercase tracking-widest">How to Avail
                                    </h4>
                                </div>
                                <button @click="saveKeyDraft('how_to_avail', 'How to Avail')" type="button"
                                    class="flex items-center gap-1 px-2.5 py-1 text-xs font-bold bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white border border-blue-200 rounded-lg transition">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Save
                                </button>
                            </div>
                            <div id="quill-peso-how-to-avail" class="rounded-lg border border-slate-300 bg-white">
                            </div>
                            <div class="mt-1 text-xs text-slate-400"><span
                                    id="quill-peso-how-to-avail-wordcount">0</span> words</div>
                        </div>

                        {{-- ── Lists: Core Services, Beneficiaries ── --}}
                        {{--
                            NEW DB STRUCTURE NOTES:
                            - core_services:  [{id, name}]          — edit name, track id
                            - beneficiaries:  [{id, name}]          — edit name, track id
                            On save, the payload sent to the backend is JSON of these same objects.
                            The controller's syncList() reads 'id' (optional for new rows) and 'name'/'acronym'.
                        --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                            {{-- Core Services --}}
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-5">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-6 h-6 rounded-lg bg-slate-700 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                        </div>
                                        <h4 class="text-xs font-bold text-slate-600 uppercase tracking-widest">Core
                                            Services</h4>
                                    </div>
                                    <button @click="saveKeyDraft('core_services', 'Core Services')" type="button"
                                        class="flex items-center gap-1 px-2.5 py-1 text-xs font-bold bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white border border-blue-200 rounded-lg transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        Save
                                    </button>
                                </div>
                                <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
                                    {{-- Each item is {id, name} — bind x-model to item.name --}}
                                    <template x-for="(item, index) in form.core_services" :key="'cs-' + index">
                                        <div class="flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 flex-shrink-0"></span>
                                            <input type="text" x-model="form.core_services[index].name"
                                                class="flex-1 border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300 bg-white transition" />
                                            <button
                                                @click="$dispatch('open-modal', { type: 'delete-list-item', listKey: 'core_services', listIndex: index })"
                                                type="button"
                                                class="flex-shrink-0 w-7 h-7 rounded-lg bg-red-50 hover:bg-red-500 border border-red-200 text-red-400 hover:text-white flex items-center justify-center transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                                {{-- Add new: push a fresh {id: null, name: ''} object --}}
                                <button @click="form.core_services.push({ id: null, name: '' })" type="button"
                                    class="mt-3 flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-800 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Service
                                </button>
                            </div>


                            {{-- Beneficiaries --}}
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-5">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-6 h-6 rounded-lg bg-blue-600 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                        <h4 class="text-xs font-bold text-slate-600 uppercase tracking-widest">
                                            Beneficiaries</h4>
                                    </div>
                                    <button @click="saveKeyDraft('beneficiaries', 'Beneficiaries')" type="button"
                                        class="flex items-center gap-1 px-2.5 py-1 text-xs font-bold bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white border border-blue-200 rounded-lg transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        Save
                                    </button>
                                </div>
                                <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
                                    <template x-for="(item, index) in form.beneficiaries" :key="'bn-' + index">
                                        <div class="flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-400 flex-shrink-0"></span>
                                            <input type="text" x-model="form.beneficiaries[index].name"
                                                class="flex-1 border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-300 bg-white transition" />
                                            <button
                                                @click="$dispatch('open-modal', { type: 'delete-list-item', listKey: 'beneficiaries', listIndex: index })"
                                                type="button"
                                                class="flex-shrink-0 w-7 h-7 rounded-lg bg-red-50 hover:bg-red-500 border border-red-200 text-red-400 hover:text-white flex items-center justify-center transition">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                                {{-- Add new: push a fresh {id: null, name: ''} object --}}
                                <button @click="form.beneficiaries.push({ id: null, name: '' })" type="button"
                                    class="mt-3 flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-800 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Beneficiary
                                </button>
                            </div>

                        </div>

                        {{-- ── Extra / Additional Sections ── --}}
                        <template x-for="(section, idx) in extraSections" :key="'extra-' + idx">
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-5" x-init="$nextTick(() => initExtraQuill(idx))">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2 flex-1 mr-3">
                                        <div
                                            class="w-6 h-6 rounded-lg bg-blue-600 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 6h16M4 12h16M4 18h7" />
                                            </svg>
                                        </div>
                                        <input type="text" x-model="section.title"
                                            class="flex-1 text-xs font-bold text-slate-600 uppercase tracking-widest bg-transparent border-b border-transparent hover:border-slate-300 focus:border-blue-400 focus:outline-none px-1 py-0.5 transition"
                                            placeholder="SECTION TITLE" />
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <button @click="saveKeyDraft('extra_sections', 'Additional Sections')"
                                            type="button"
                                            class="flex items-center gap-1 px-2.5 py-1 text-xs font-bold bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white border border-blue-200 rounded-lg transition">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                            Save
                                        </button>
                                        <button @click="removeExtraSection(idx)" type="button"
                                            class="flex items-center gap-1 px-2.5 py-1 text-xs font-bold bg-red-50 hover:bg-red-600 text-red-500 hover:text-white border border-red-200 rounded-lg transition">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Remove
                                        </button>
                                    </div>
                                </div>
                                <div :id="'quill-extra-' + idx" class="rounded-lg border border-slate-300 bg-white">
                                </div>
                                <div class="mt-1 text-xs text-slate-400"><span
                                        :id="'quill-extra-wordcount-' + idx">0</span> words</div>
                            </div>
                        </template>

                    </div>
                </div>
                {{-- ===== END PESO INFO CONTENT EDITOR ===== --}}

                {{-- Section Header --}}
                <div
                    class="bg-gradient-to-r from-slate-800 via-slate-700 to-slate-800 rounded-2xl px-8 py-7 shadow-2xl mb-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-white font-bold text-2xl md:text-3xl">PESO / JPO Directory</h2>
                                <p class="text-slate-300 text-sm md:text-base">
                                    @if ($directoryPublished)
                                        <span class="inline-flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                            Published · last updated
                                            {{ $publishedAt ? \Carbon\Carbon::parse($publishedAt)->diffForHumans() : 'unknown' }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                            Not yet published to public
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <button @click="$dispatch('open-modal', { type: 'add-peso' })"
                                class="plus-btn flex items-center gap-2 px-5 py-2.5 bg-indigo-500 hover:bg-indigo-400 text-white font-bold text-sm rounded-xl shadow-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Add PESO/JPO
                            </button>
                            <button x-data="{}"
                                @click="$dispatch('open-modal', { type: 'publish-directory' })"
                                :class="$store.pesoDirectory.hasDraftChanges ? 'bg-amber-500 hover:bg-amber-400' :
                                    'bg-emerald-500 hover:bg-emerald-400'"
                                class="flex items-center gap-2 px-5 py-2.5 text-white font-bold text-sm rounded-xl shadow-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                <span
                                    x-text="$store.pesoDirectory.hasDraftChanges ? 'Update Publish' : '{{ $directoryPublished ? 'Update Publish' : 'Publish Directory' }}'">
                                    {{ $directoryPublished ? 'Update Publish' : 'Publish Directory' }}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Progressive Reveal Directory --}}
                @php
                    $pesoProvinces = $fieldOffices->groupBy('province')->map(fn($items) => $items->values());
                    $pesoJson = $pesoProvinces->map(
                        fn($items) => $items
                            ->map(fn($o) => [
                                'id'             => $o->id,
                                'name'           => $o->name,
                                'persons_name'   => $o->persons_name ?? '',
                                'position_title' => $o->positionTitle?->name ?? '',
                                'email'          => $o->email ?? '',
                                'address'        => $o->address ?? '',
                                'type'           => $o->officeType?->name ?? '',
                            ])
                            // Sort using a single composite key to guarantee stability:
                            //   [0] type group   → PESO=0, JPO=1  (always dominant — no PESO ever appears after JPO)
                            //   [1] clamp bucket → ceil(name_length / 25) — shorter names first within the group
                            //   [2] id           → oldest first, latest added last within the same clamp bucket
                            ->sortBy(fn($a) => sprintf(
                                '%d-%04d-%010d',
                                $a['type'] === 'PESO' ? 0 : 1,
                                (int) ceil(mb_strlen($a['name']) / 25),
                                $a['id']
                            ))
                            ->values(),
                    );
                @endphp


                <div id="peso-ajax-container"
                    class="bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden"
                    x-data="pesoDirectory()">

                    <div class="p-6 md:p-10 space-y-8">

                        {{-- STEP 1 --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">1 ·
                                Select Province</label>
                            <div class="relative w-full">
                                <select @change="selectProvince($event.target.value)" :value="province"
                                    class="w-full appearance-none bg-white border-2 rounded-xl px-4 py-3 pr-10 text-sm font-semibold outline-none transition-all cursor-pointer"
                                    :class="province ?
                                        'border-orange-400 shadow-[0_0_0_3px_rgba(251,146,60,0.15)] text-slate-800' :
                                        'border-slate-200 text-slate-400 hover:border-slate-300'">
                                    <option value="">— Choose a province —</option>
                                    @foreach ($pesoProvinces->keys() as $province)
                                        <option value="{{ $province }}">{{ $province }}</option>
                                    @endforeach
                                </select>
                                <span
                                    class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </span>
                            </div>
                        </div>

                        {{-- STEP 2 --}}
                        <div x-ref="typeSection" x-show="showType"
                            x-transition:enter="transition ease-out duration-350"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">2 ·
                                Office Type</label>
                            <div class="flex flex-wrap gap-3 w-full">
                                <button @click="selectType('ALL')" type="button"
                                    class="rounded-xl px-4 py-3 flex items-center gap-2 border-2 transition-all duration-200 cursor-pointer"
                                    :style="officeType === 'ALL' ?
                                        'background:#eef2ff; border-color:#6366f1; box-shadow:0 0 0 3px #eef2ff; transform:translateY(-2px);' :
                                        'background:white; border-color:#e2e8f0;'">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        :style="officeType === 'ALL' ? 'color:#6366f1' : 'color:#94a3b8'">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span class="text-xs font-bold"
                                        :style="officeType === 'ALL' ? 'color:#6366f1' : 'color:#64748b'">All
                                        Offices</span>
                                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full"
                                        :style="officeType === 'ALL' ?
                                            'background:white; color:#6366f1; border:1px solid #c7d2fe' :
                                            'background:#f1f5f9; color:#94a3b8; border:1px solid #e2e8f0'"
                                        x-text="countFor(province, 'ALL') + ' offices'"></span>
                                </button>
                                <template x-for="(t, idx) in officeTypes" :key="t">
                                    <button @click="selectType(t)" type="button"
                                        class="rounded-xl px-4 py-3 flex items-center gap-2 border-2 transition-all duration-200 cursor-pointer"
                                        :style="officeType === t ?
                                            `background:${typeColor(idx,'bg')}; border-color:${typeColor(idx,'main')}; box-shadow:0 0 0 3px ${typeColor(idx,'bg')}; transform:translateY(-2px);` :
                                            'background:white; border-color:#e2e8f0;'">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24"
                                            :style="`color:${officeType === t ? typeColor(idx,'main') : '#94a3b8'}`">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-xs font-bold"
                                            :style="`color:${officeType === t ? typeColor(idx,'main') : '#64748b'}`"
                                            x-text="t + ' Only'"></span>
                                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full"
                                            :style="officeType === t ?
                                                `background:white; color:${typeColor(idx,'main')}; border:1px solid ${typeColor(idx,'border')}` :
                                                'background:#f1f5f9; color:#94a3b8; border:1px solid #e2e8f0'"
                                            x-text="countFor(province, t) + ' offices'"></span>
                                    </button>
                                </template>
                                <p x-show="officeTypes.length === 0"
                                    class="text-xs text-slate-400 italic self-center">No types found.</p>
                            </div>
                        </div>

                        {{-- STEP 3 --}}
                        <div x-ref="resultsSection" x-show="showResults"
                            x-transition:enter="transition ease-out duration-350"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest">3 ·
                                        Results</label>
                                    <p class="text-sm text-slate-500 mt-1">
                                        Showing <strong class="text-orange-500"
                                            x-text="filteredEntries().length"></strong>
                                        <span x-text="search.trim() ? 'matches' : 'offices'"></span>
                                        in <strong class="text-slate-800" x-text="province"></strong>
                                    </p>
                                </div>
                                <button
                                    @click="province=''; officeType=''; showType=false; showResults=false; search='';"
                                    class="flex items-center gap-1.5 text-xs font-semibold px-3 py-2 rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Reset
                                </button>
                            </div>

                            <div class="relative mb-4">
                                <span
                                    class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                                    </svg>
                                </span>
                                <input type="text" x-model="search"
                                    placeholder="Search by office name, manager/person name..."
                                    class="w-full border border-slate-200 rounded-xl pl-9 pr-10 py-2.5 text-sm focus:ring-2 focus:ring-orange-300 focus:border-orange-400 outline-none transition bg-slate-50 focus:bg-white" />
                                <button x-show="search.trim()" @click="search = ''"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition"
                                    x-cloak>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div x-show="filteredEntries().length === 0" class="text-center py-10 text-slate-400"
                                x-cloak>
                                <svg class="w-10 h-10 mx-auto mb-3 text-slate-300" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                                </svg>
                                <p class="text-sm font-semibold">No offices found</p>
                                <p class="text-xs mt-1">Try a different search term</p>
                            </div>

                            <div class="grid md:grid-cols-2 xl:grid-cols-3 gap-3 items-start">
                                <template x-for="entry in filteredEntries()" :key="entry.id">
                                    <div x-data="pesoCard(entry.id)"
                                        class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition-all duration-200">

                                        {{-- Card Header --}}
                                        <div class="flex items-start gap-3 px-4 py-3 cursor-pointer select-none"
                                            @click.stop="toggleCard(entry.id)">

                                            {{-- Avatar --}}
                                            <div class="w-9 h-9 rounded-lg flex-shrink-0 flex items-center justify-center text-white font-bold text-sm mt-0.5"
                                                :style="entry.type === 'JPO' ? 'background:#3b82f6' : 'background:#22c55e'"
                                                x-text="entry.name.charAt(0)"></div>

                                            {{-- Name + subtitle --}}
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-slate-800 line-clamp-2 leading-snug"
                                                    style="text-transform: capitalize;"
                                                    x-text="entry.name"></p>
                                                <p class="text-xs text-slate-400 font-semibold uppercase tracking-wide mt-0.5 line-clamp-1"
                                                    x-text="(entry.position_title || '') + (entry.persons_name ? ' · ' + entry.persons_name : '')"></p>
                                            </div>

                                            {{-- Type badge + chevron stacked so they don't fight the name --}}
                                            <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
                                                <span
                                                    class="inline-flex items-center text-xs font-bold px-2 py-0.5 rounded"
                                                    :style="entry.type === 'JPO' ?
                                                        'background:#eff6ff; color:#3b82f6; border:1px solid #bfdbfe' :
                                                        'background:#f0fdf4; color:#22c55e; border:1px solid #bbf7d0'"
                                                    x-text="entry.type"></span>
                                                <svg class="w-4 h-4 text-slate-400"
                                                    style="transition: transform 0.2s ease;"
                                                    :style="isOpen(entry.id) ? 'transform:rotate(180deg)' : 'transform:rotate(0deg)'"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                        </div>

                                        <div x-show="isOpen(entry.id)"
                                            class="border-t border-slate-100 px-4 py-4 flex flex-col gap-3 bg-slate-50">
                                            <template x-if="entry.persons_name">
                                                <div class="flex items-start gap-2">
                                                    <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0 mt-0.5"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                    <div>
                                                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5"
                                                            x-text="entry.position_title || 'Personnel'"></p>
                                                        <p class="text-xs text-slate-700 font-medium"
                                                            x-text="entry.persons_name"></p>
                                                    </div>
                                                </div>
                                            </template>
                                            <template x-if="entry.email">
                                                <div class="flex items-start gap-2">
                                                    <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0 mt-0.5"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                    </svg>
                                                    <div>
                                                        <p
                                                            class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                                                            Email Address</p>
                                                        <a :href="'mailto:' + entry.email"
                                                            class="text-xs text-blue-500 hover:underline"
                                                            x-text="entry.email" @click.stop></a>
                                                    </div>
                                                </div>
                                            </template>
                                            <template x-if="entry.address">
                                                <div class="flex items-start gap-2">
                                                    <svg class="w-3.5 h-3.5 text-slate-400 flex-shrink-0 mt-0.5"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    <div>
                                                        <p
                                                            class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                                                            Address</p>
                                                        <span class="text-xs text-slate-700 leading-relaxed"
                                                            style="text-transform: capitalize;"
                                                            x-text="entry.address.toLowerCase()"></span>
                                                    </div>
                                                </div>
                                            </template>
                                            <div class="flex gap-2 pt-1">
                                                <button
                                                    @click.stop="$dispatch('open-modal', { type: 'edit-peso', id: entry.id, data: { name: entry.name, persons_name: entry.persons_name, position_title: entry.position_title, email: entry.email, address: entry.address, type: entry.type, province: province } })"
                                                    class="flex items-center gap-1 px-2.5 py-1 text-xs font-semibold bg-indigo-50 hover:bg-indigo-500 text-indigo-500 hover:text-white border border-indigo-200 rounded-lg transition">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Edit
                                                </button>
                                                <button
                                                    @click.stop="$dispatch('open-modal', { type: 'delete-peso', id: entry.id })"
                                                    class="flex items-center gap-1 px-2.5 py-1 text-xs font-semibold bg-red-50 hover:bg-red-500 text-red-400 hover:text-white border border-red-200 rounded-lg transition">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div x-show="!province" class="text-center py-8 text-slate-400">
                                <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <p class="text-sm font-medium">Select a province above to browse offices</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            {{-- ===== MODALS ===== --}}
            <div x-show="modal.open" x-cloak
                class="fixed inset-0 z-[999] flex items-center justify-center modal-backdrop bg-slate-900/60 p-4"
                @keydown.escape.window="modal.open = false" @open-modal.window="openModal($event.detail)">

                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto" @click.stop>
                    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-200">
                        <h3 class="font-bold text-slate-900 text-lg" x-text="modal.title"></h3>
                        <button @click="modal.open = false"
                            class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition">
                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="px-6 py-6">

                        {{-- ADD / EDIT PESO --}}
                        <div x-show="modal.type === 'add-peso' || modal.type === 'edit-peso'" x-cloak
                            class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Office Name <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" x-model="form.name" placeholder="e.g. PESO MATI CITY"
                                        :class="formErrors.name ? 'border-red-500 ring-2 ring-red-200' :
                                            'border-slate-300 focus:ring-indigo-400'"
                                        @input="formErrors.name = false"
                                        class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none" />
                                    <p x-show="formErrors.name" class="mt-1 text-xs text-red-500 font-semibold"
                                        x-cloak>Office name is required.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Office Type <span
                                            class="text-red-500">*</span></label>
                                    <select x-model="form.type"
                                        :class="formErrors.type ? 'border-red-500 ring-2 ring-red-200' :
                                            'border-slate-300 focus:ring-indigo-400'"
                                        @change="formErrors.type = false; form.position_title = ''"
                                        class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none bg-white cursor-pointer">
                                        <option value="" disabled selected hidden>— Select type —</option>
                                        <option value="PESO">PESO</option>
                                        <option value="JPO">JPO</option>
                                    </select>
                                    <p x-show="formErrors.type" class="mt-1 text-xs text-red-500 font-semibold"
                                        x-cloak>Please select an office type.</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Province <span
                                            class="text-red-500">*</span></label>
                                    <select x-model="form.province"
                                        :class="formErrors.province ? 'border-red-500 ring-2 ring-red-200' :
                                            'border-slate-300 focus:ring-indigo-400'"
                                        @change="formErrors.province = false; form.position_title = ''"
                                        class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none bg-white cursor-pointer">
                                        <option value="" disabled selected hidden>— Select province —</option>
                                        <option value="DAVAO CITY">DAVAO CITY</option>
                                        <option value="DAVAO DE ORO">DAVAO DE ORO</option>
                                        <option value="DAVAO DEL NORTE">DAVAO DEL NORTE</option>
                                        <option value="DAVAO DEL SUR">DAVAO DEL SUR</option>
                                        <option value="DAVAO OCCIDENTAL">DAVAO OCCIDENTAL</option>
                                        <option value="DAVAO ORIENTAL">DAVAO ORIENTAL</option>
                                    </select>
                                    <p x-show="formErrors.province" class="mt-1 text-xs text-red-500 font-semibold"
                                        x-cloak>Province is required.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Manager / Head Name
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" x-model="form.persons_name"
                                        placeholder="e.g. Juan dela Cruz"
                                        :class="formErrors.persons_name ? 'border-red-500 ring-2 ring-red-200' :
                                            'border-slate-300 focus:ring-indigo-400'"
                                        @input="formErrors.persons_name = false"
                                        class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none" />
                                    <p x-show="formErrors.persons_name"
                                        class="mt-1 text-xs text-red-500 font-semibold" x-cloak>Manager name is
                                        required.</p>
                                </div>
                            </div>

                            {{-- Position Title row --}}
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Position Title <span
                                        class="text-red-500">*</span></label>
                                <select x-model="form.position_title"
                                    :class="formErrors.position_title ? 'border-red-500 ring-2 ring-red-200' :
                                        'border-slate-300 focus:ring-indigo-400'"
                                    @change="formErrors.position_title = false"
                                    class="w-full border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none bg-white cursor-pointer">
                                    <option value="" disabled selected hidden>— Select position —</option>
                                    <template x-for="t in filteredPositionTitles" :key="t">
                                        <option :value="t" x-text="t" :selected="form.position_title === t"></option>
                                    </template>
                                </select>
                                <p x-show="formErrors.position_title" class="mt-1 text-xs text-red-500 font-semibold"
                                    x-cloak>Please select a position title.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Email Address <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </span>
                                    <input type="email" x-model="form.email" placeholder="office@gmail.com"
                                        :class="formErrors.email ? 'border-red-500 ring-2 ring-red-200' :
                                            'border-slate-300 focus:ring-indigo-400'"
                                        @input="formErrors.email = false"
                                        class="w-full border rounded-lg pl-10 pr-4 py-2.5 text-sm focus:ring-2 outline-none" />
                                </div>
                                <p x-show="formErrors.email" class="mt-1 text-xs text-red-500 font-semibold" x-cloak>
                                    Email address is required.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Address <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-3 top-3 text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </span>
                                    <textarea x-model="form.address" rows="2" placeholder="Full address..."
                                        :class="formErrors.address ? 'border-red-500 ring-2 ring-red-200' :
                                            'border-slate-300 focus:ring-indigo-400'"
                                        @input="formErrors.address = false"
                                        class="w-full border rounded-lg pl-10 pr-4 py-2.5 text-sm focus:ring-2 outline-none resize-none"></textarea>
                                </div>
                                <p x-show="formErrors.address" class="mt-1 text-xs text-red-500 font-semibold"
                                    x-cloak>Address is required.</p>
                            </div>
                            <div class="flex gap-3 justify-end pt-4 border-t border-slate-200">
                                <button type="button" @click="modal.open = false"
                                    class="px-5 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Cancel</button>
                                <button type="button" @click="submitFieldOffice()" :disabled="modal.loading"
                                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                    <span x-show="!modal.loading">Save</span>
                                    <span x-show="modal.loading" x-cloak>Saving…</span>
                                </button>
                            </div>
                        </div>

                        {{-- DELETE PESO --}}
                        <template x-if="modal.type === 'delete-peso'">
                            <div class="text-center space-y-4">
                                <div
                                    class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto">
                                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <p class="text-slate-700 font-semibold">Delete this PESO/JPO office?</p>
                                <p class="text-slate-500 text-sm">This action cannot be undone.</p>
                                <div class="flex gap-3 justify-center mt-4">
                                    <button type="button" @click="modal.open = false"
                                        class="px-6 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Cancel</button>
                                    <button type="button" @click="destroyFieldOffice()" :disabled="modal.loading"
                                        class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                        <span x-show="!modal.loading">Yes, Delete</span>
                                        <span x-show="modal.loading" x-cloak>Deleting…</span>
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- SAVE ALL CONFIRM --}}
                        <template x-if="modal.type === 'save-all-confirm'">
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-slate-800 font-bold text-lg leading-tight">Save All Changes?</p>
                                        <p class="text-slate-500 text-xs mt-0.5">These changes will be saved to the
                                            PESO Info section.</p>
                                    </div>
                                </div>
                                <div class="bg-slate-50 rounded-xl border border-slate-200 px-4 py-3 space-y-2">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">What's
                                        changing</p>
                                    <template x-for="(change, i) in (window._pesoInfoPendingChanges || [])"
                                        :key="i">
                                        <div class="flex items-center gap-2.5">
                                            <template x-if="change.icon === 'doc'">
                                                <span
                                                    class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-3 h-3 text-blue-500" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </span>
                                            </template>
                                            <template x-if="change.icon === 'list'">
                                                <span
                                                    class="w-5 h-5 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-3 h-3 text-indigo-500" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                                    </svg>
                                                </span>
                                            </template>
                                            <span class="text-sm text-slate-700" x-text="change.text"></span>
                                        </div>
                                    </template>
                                </div>
                                <div class="flex gap-3 justify-end pt-1">
                                    <button type="button" @click="modal.open = false"
                                        class="px-5 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Cancel</button>
                                    <button type="button"
                                        @click="modal.open = false; $nextTick(() => { const ed = document.querySelector('[x-data=\'pesoInfoEditor()\']')?._x_dataStack?.[0]; if(ed) ed.saveAll(); })"
                                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold text-sm transition">
                                        Yes, Save All
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- DELETE LIST ITEM --}}
                        <template x-if="modal.type === 'delete-list-item'">
                            <div class="text-center space-y-4">
                                <div
                                    class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto">
                                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <p class="text-slate-700 font-semibold">Are you sure you want to delete this?</p>
                                <p class="text-slate-500 text-sm">This action cannot be undone.</p>
                                <div class="flex gap-3 justify-center mt-4">
                                    <button type="button" @click="modal.open = false"
                                        class="px-6 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Cancel</button>
                                    <button type="button" @click="confirmListItemDelete()"
                                        class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold text-sm transition">Yes,
                                        Delete</button>
                                </div>
                            </div>
                        </template>

                        {{-- PUBLISH PESO INFO --}}
                        <template x-if="modal.type === 'publish-peso-info'">
                            <div class="space-y-4">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-slate-800 font-bold text-lg">Publish PESO Info to Public?</p>
                                        <p class="text-slate-500 text-sm">This will push all saved draft changes live
                                            to the public page.</p>
                                    </div>
                                </div>

                                {{-- Changelog of pending changes --}}
                                <div class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-3">
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2.5">
                                        Changes to be published</p>
                                    <div x-data="{ getChangelog() { const ed = document.querySelector('[x-data=\'pesoInfoEditor()\']')?._x_dataStack?.[0]; return ed?.pesoInfoChangelog ?? []; } }">
                                        <template x-if="getChangelog().length === 0">
                                            <p class="text-xs text-slate-500 italic">All current draft fields will be
                                                published to the public page.</p>
                                        </template>
                                        <ul class="space-y-1.5">
                                            <template x-for="(entry, i) in getChangelog()" :key="i">
                                                <li class="flex items-center gap-2 text-xs">
                                                    <span
                                                        class="w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                                        <svg class="w-3 h-3 text-blue-500" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </span>
                                                    <span class="font-semibold text-slate-700 capitalize"
                                                        x-text="entry.label ?? (entry.field ?? 'field').replace(/_/g, ' ')"></span>
                                                    <template x-if="entry.via === 'save_all'">
                                                        <span
                                                            class="inline-flex items-center px-1.5 py-0.5 rounded text-slate-500 bg-slate-100 border border-slate-200 font-bold uppercase tracking-wider"
                                                            style="font-size:9px">via Save All</span>
                                                    </template>
                                                    <span class="text-slate-400">·</span>
                                                    <span class="text-slate-500"
                                                        x-text="new Date(entry.time).toLocaleString()"></span>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>

                                <div class="flex gap-3 justify-end pt-1 border-t border-slate-100">
                                    <button type="button" @click="modal.open = false"
                                        class="px-5 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Cancel</button>
                                    <button type="button"
                                        @click="modal.open = false; $nextTick(() => { const ed = document.querySelector('[x-data=\'pesoInfoEditor()\']')?._x_dataStack?.[0]; if(ed) ed.publishPesoInfo(); })"
                                        :disabled="modal.loading"
                                        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                        <span x-show="!modal.loading">✓ Yes, Publish Now</span>
                                        <span x-show="modal.loading" x-cloak>Publishing…</span>
                                    </button>
                                </div>
                            </div>
                        </template>

                        {{-- PUBLISH DIRECTORY --}}
                        <template x-if="modal.type === 'publish-directory'">
                            <div class="space-y-4">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-slate-800 font-bold text-lg">Publish PESO / JPO Directory?</p>
                                        <p class="text-slate-500 text-sm">This will push all your changes live to the
                                            public page.</p>
                                    </div>
                                </div>
                                <div x-show="$store.pesoDirectory.changeLog.length > 0"
                                    class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-3">
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2.5">
                                        Changes to be published</p>
                                    <ul class="space-y-2">
                                        <template x-for="(entry, i) in $store.pesoDirectory.changeLog"
                                            :key="i">
                                            <li class="flex items-center gap-2 text-xs">
                                                <span
                                                    class="flex-shrink-0 w-5 h-5 rounded-full flex items-center justify-center"
                                                    :class="{ 'bg-emerald-100 text-emerald-600': entry.action==='added', 'bg-blue-100 text-blue-600': entry.action==='edited', 'bg-red-100 text-red-500': entry.action==='deleted' }">
                                                    <template x-if="entry.action === 'added'"><svg class="w-3 h-3"
                                                            fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2.5" d="M12 4v16m8-8H4" />
                                                        </svg></template>
                                                    <template x-if="entry.action === 'edited'"><svg class="w-3 h-3"
                                                            fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg></template>
                                                    <template x-if="entry.action === 'deleted'"><svg class="w-3 h-3"
                                                            fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg></template>
                                                </span>
                                                <span class="font-semibold"
                                                    :class="{ 'text-emerald-700': entry.action==='added', 'text-blue-700': entry.action==='edited', 'text-red-600': entry.action==='deleted' }"
                                                    x-text="entry.action.charAt(0).toUpperCase() + entry.action.slice(1)"></span>
                                                <span class="text-slate-400">·</span>
                                                <span class="text-slate-700 font-medium"
                                                    x-text="entry.label"></span>
                                                <span class="text-slate-400"
                                                    x-text="'(' + entry.type + ', ' + entry.province + ')'"></span>
                                                <span class="text-slate-400 ml-auto flex-shrink-0"
                                                    x-text="entry.time"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                                <div class="flex gap-3 justify-end pt-1 border-t border-slate-100">
                                    <button type="button" @click="modal.open = false"
                                        class="px-5 py-2.5 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm hover:bg-slate-50 transition">Cancel</button>
                                    <button type="button" @click="submitPublishDirectory()"
                                        :disabled="modal.loading"
                                        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold text-sm transition disabled:opacity-60">
                                        <span x-show="!modal.loading">✓ Publish Now</span>
                                        <span x-show="modal.loading" x-cloak>Publishing…</span>
                                    </button>
                                </div>
                            </div>
                        </template>

                    </div>
                </div>
            </div>

            {{-- SUCCESS MODAL --}}
            <div id="successModal" x-data="{ open: false, title: 'Success!', message: '' }" x-show="open" x-cloak
                @keydown.escape.window="open = false"
                @show-success-modal.window="open = true; title = $event.detail.title || 'Success!'; message = $event.detail.message"
                class="fixed inset-0 z-[1300] flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
                <div class="absolute inset-0" @click="open = false"></div>
                <div @click.stop class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                    <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 px-6 pt-8 pb-6 text-center">
                        <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-9 h-9 text-emerald-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white" x-text="title"></h3>
                    </div>
                    <div class="px-6 py-6 text-center">
                        <p class="text-slate-700 text-sm font-medium mb-6" x-text="message"></p>
                        <button @click="open = false"
                            class="w-full px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition text-sm">Done</button>
                    </div>
                </div>
            </div>

            <div id="toastContainer" class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none"
                style="min-width: 340px;"></div>

        </div>
    </div>



    {{-- Blade data passed to JS (PHP values that cannot live in .js files) --}}
    <script>
        window._pesoData = @json($pesoJson);

        window._pesoInitData = {
            pesoInfoHasDraft:   @json($pesoInfoHasDraft),
            pesoInfoChangelog:  @json($pesoInfoChangelog),
            directoryHasDraft:  @json($directoryHasDraft),
            directoryChangelog: @json($directoryChangelog),
            pesoInfo: {
                description:    @json($pesoInfo['description']    ?? ''),
                objective:      @json($pesoInfo['objective']      ?? ''),
                how_to_avail:   @json($pesoInfo['how_to_avail']   ?? ''),
                core_services:  @json($pesoInfo['core_services']  ?? []),
                beneficiaries:  @json($pesoInfo['beneficiaries']  ?? []),
                extra_sections: @json($pesoInfo['extra_sections'] ?? []),
            },
        };
    </script>

</body>

</html>