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
        }

        .ql-editor {
            min-height: 120px;
            font-size: 14px;
            line-height: 1.6;
        }

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
                    ->map(fn($s) => [
                        'id'         => $s->id,
                        'image'      => asset('storage/' . $s->image_path),
                        'sort_order' => $s->sort_order,
                    ])
                    ->toJson();
            @endphp
            <script>
                function pesoCarouselSection(slides) {
                    return {
                        currentSlide: 0,
                        slides: slides,
                        autoplayInterval: null,
                        nextSlide() {
                            this.currentSlide = (this.currentSlide + 1) % this.slides.length;
                        },
                        prevSlide() {
                            this.currentSlide = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
                        },
                        goToSlide(index) {
                            this.currentSlide = index;
                        },
                        startAutoplay() {
                            this.autoplayInterval = setInterval(() => this.nextSlide(), 5000);
                        },
                        stopAutoplay() {
                            clearInterval(this.autoplayInterval);
                        },
                    };
                }
            </script>

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

            <script>
                function pesoCarouselModals() {
                    return {
                        modal: null,
                        saving: false,
                        // add
                        addFile: null,
                        addPreview: null,
                        addError: '',
                        // edit
                        editData: { id: null, image: null, sort_order: 0 },
                        editFile: null,
                        editPreview: null,
                        editError: '',
                        // delete
                        deleteId: null,

                        init() {},

                        handleOpen(detail) {
                            this.modal = detail.type;
                            if (detail.type === 'edit-slide') {
                                this.editData    = { ...detail.data };
                                this.editFile    = null;
                                this.editPreview = null;
                                this.editError   = '';
                            }
                            if (detail.type === 'delete-slide') {
                                this.deleteId = detail.id;
                            }
                            if (detail.type === 'add-slide') {
                                this.addFile       = null;
                                this.addPreview    = null;
                                this.addError      = '';
                            }
                        },

                        close() { this.modal = null; },

                        handleDropAdd(event) {
                            const file = event.dataTransfer.files[0];
                            if (file) this._processFile(file, 'add');
                        },

                        handleDropEdit(event) {
                            const file = event.dataTransfer.files[0];
                            if (file) this._processFile(file, 'edit');
                        },

                        _processFile(file, mode) {
                            const ALLOWED = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

                            if (!ALLOWED.includes(file.type)) {
                                const err = 'Invalid file type. Please upload a JPG, PNG, WebP, or GIF.';
                                if (mode === 'add') this.addError = err;
                                else this.editError = err;
                                return;
                            }

                            // 20MB hard cap before compression
                            if (file.size > 20 * 1024 * 1024) {
                                const err = 'Image is too large (max 20 MB).';
                                if (mode === 'add') this.addError = err;
                                else this.editError = err;
                                return;
                            }

                            // Clear any previous errors
                            if (mode === 'add') this.addError = '';
                            else this.editError = '';

                            const img = new Image();
                            const objectUrl = URL.createObjectURL(file);

                            img.onload = () => {
                                URL.revokeObjectURL(objectUrl);

                                const MAX_WIDTH  = 1920;
                                const MAX_HEIGHT = 1080;
                                let w = img.width;
                                let h = img.height;

                                // Scale down if needed, preserve aspect ratio
                                if (w > MAX_WIDTH || h > MAX_HEIGHT) {
                                    const ratio = Math.min(MAX_WIDTH / w, MAX_HEIGHT / h);
                                    w = Math.round(w * ratio);
                                    h = Math.round(h * ratio);
                                }

                                const canvas = document.createElement('canvas');
                                canvas.width  = w;
                                canvas.height = h;
                                canvas.getContext('2d').drawImage(img, 0, 0, w, h);

                                // GIFs can't be compressed via canvas, keep as-is
                                const outputType    = file.type === 'image/gif' ? 'image/gif' : 'image/webp';
                                const outputQuality = 0.85;

                                canvas.toBlob(blob => {
                                    const compressed = new File([blob], file.name.replace(/\.[^.]+$/, '.webp'), { type: outputType });
                                    const reader = new FileReader();
                                    reader.onload = e => {
                                        if (mode === 'add') {
                                            this.addFile    = compressed;
                                            this.addPreview = e.target.result;
                                            this.addError   = '';
                                        } else {
                                            this.editFile    = compressed;
                                            this.editPreview = e.target.result;
                                            this.editError   = '';
                                        }
                                    };
                                    reader.readAsDataURL(compressed);
                                }, outputType, outputQuality);
                            };

                            img.onerror = () => {
                                URL.revokeObjectURL(objectUrl);
                                const err = 'Could not read image. Please try a different file.';
                                if (mode === 'add') this.addError = err;
                                else this.editError = err;
                            };

                            img.src = objectUrl;
                        },

                        previewFile(event, mode) {
                            const file = event.target.files[0];
                            if (!file) return;
                            this._processFile(file, mode);
                        },

                        csrf() {
                            return document.querySelector('meta[name="csrf-token"]').content;
                        },

                        reloadCarousel(slides) {
                            const el = document.getElementById('carousel-section');
                            if (el) {
                                const data = Alpine.$data(el);
                                data.slides.splice(0, data.slides.length, ...slides);
                                data.currentSlide = 0;
                            }
                        },

                        async submitAdd() {
                        if (!this.addFile) { this.addError = 'Please select an image.'; return; }
                        this.saving = true;
                        this.addError = '';
                        const fd = new FormData();
                        fd.append('image', this.addFile);
                        try {
                            const res  = await fetch('/admin/peso-carousel-slides', { method: 'POST', headers: { 'X-CSRF-TOKEN': this.csrf() }, body: fd });
                            const data = await res.json();
                            if (data.success) { this.reloadCarousel(data.slides); this.close(); }
                            else { this.addError = data.message ?? 'Upload failed.'; }
                        } catch (e) {
                            this.addError = 'Error: ' + e.message; // ← change this temporarily
                            console.error(e);
                        }
                        this.saving = false;
                    },

                        async submitEdit() {
                            this.saving = true;
                            this.editError = '';
                            const fd = new FormData();
                            fd.append('_method',    'PUT');
                            if (this.editFile) fd.append('image', this.editFile);
                            try {
                                const res  = await fetch('/admin/peso-carousel-slides/' + this.editData.id, { method: 'POST', headers: { 'X-CSRF-TOKEN': this.csrf() }, body: fd });
                                const data = await res.json();
                                if (data.success) { this.reloadCarousel(data.slides); this.close(); }
                                else { this.editError = data.message ?? 'Update failed.'; }
                            } catch { this.editError = 'Network error. Please try again.'; }
                            this.saving = false;
                        },

                        async submitDelete() {
                            this.saving = true;
                            try {
                                const res  = await fetch('/admin/peso-carousel-slides/' + this.deleteId, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': this.csrf(), 'Content-Type': 'application/json' } });
                                const data = await res.json();
                                if (data.success) { this.reloadCarousel(data.slides); this.close(); }
                            } catch {}
                            this.saving = false;
                        },
                    };
                }
            </script>
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
                        fn($items) => $items->map(
                            fn($o) => [
                                'id' => $o->id,
                                'name' => $o->name,
                                'persons_name' => $o->persons_name ?? '',
                                'position_title' => $o->positionTitle?->name ?? '',
                                'email' => $o->email ?? '',
                                'address' => $o->address ?? '',
                                'type' => $o->officeType?->name ?? '',
                            ],
                        ),
                    );
                @endphp

                <script>
                    window._pesoData = @json($pesoJson);

                    {{-- ══ PESO INFO EDITOR ══ --}}
                    {{--
                        NEW DB STRUCTURE:
                        form.core_services  is [{id, name}]
                        form.beneficiaries  is [{id, name}]

                        When saving, we send the full object arrays as JSON strings.
                        The controller's syncList() reads item['id'] (omit = new row) and item['name'] / item['acronym'].
                        Empty-name items are filtered out before sending.
                    --}}

                    function pesoInfoEditor() {
                        return {
                            collapsed: false,
                            saving: false,
                            publishing: false,
                            pesoInfoHasDraft: @json($pesoInfoHasDraft),
                            pesoInfoChangelog: @json($pesoInfoChangelog),

                            form: {
                                description: @json($pesoInfo['description'] ?? ''),
                                objective: @json($pesoInfo['objective'] ?? ''),
                                how_to_avail: @json($pesoInfo['how_to_avail'] ?? ''),
                                core_services: @json($pesoInfo['core_services'] ?? []),
                                beneficiaries: @json($pesoInfo['beneficiaries'] ?? []),
                            },

                            extraSections: @json($pesoInfo['extra_sections'] ?? []),

                            {{-- Saved snapshots for change-detection --}}
                            _saved: {
                                description: @json($pesoInfo['description'] ?? ''),
                                objective: @json($pesoInfo['objective'] ?? ''),
                                how_to_avail: @json($pesoInfo['how_to_avail'] ?? ''),
                                core_services: JSON.stringify(@json($pesoInfo['core_services'] ?? [])),
                                beneficiaries: JSON.stringify(@json($pesoInfo['beneficiaries'] ?? [])),
                                extra_sections: JSON.stringify(@json($pesoInfo['extra_sections'] ?? [])),
                            },

                            init() {
                                this.$nextTick(() => {
                                    this.initQuill('quill-peso-description', 'quill-peso-description-wordcount', 'description');
                                    this.initQuill('quill-peso-objective', 'quill-peso-objective-wordcount', 'objective');
                                    this.initQuill('quill-peso-how-to-avail', 'quill-peso-how-to-avail-wordcount',
                                        'how_to_avail');
                                    this.extraSections.forEach((_, idx) => this.initExtraQuill(idx));
                                });
                            },

                            initQuill(editorId, wordCountId, formField) {
                                if (!window._quillSizeRegistered) {
                                    const SizeStyle = Quill.import('attributors/style/size');
                                    SizeStyle.whitelist = ['8pt', '10pt', '11pt', '12pt', '14pt', '16pt', '18pt', '24pt', '36pt'];
                                    Quill.register(SizeStyle, true);
                                    window._quillSizeRegistered = true;
                                }
                                const el = document.getElementById(editorId);
                                if (!el) return;
                                if (!window._quillInstances) window._quillInstances = {};
                                if (window._quillInstances[editorId]) return;

                                const quill = new Quill('#' + editorId, {
                                    theme: 'snow',
                                    placeholder: 'Enter text...',
                                    modules: {
                                        toolbar: [
                                            [{
                                                font: []
                                            }, {
                                                size: ['8pt', '10pt', '11pt', '12pt', '14pt', '16pt', '18pt', '24pt',
                                                    '36pt'
                                                ]
                                            }],
                                            ['bold', 'italic', 'underline', 'strike'],
                                            [{
                                                color: []
                                            }, {
                                                background: []
                                            }],
                                            [{
                                                header: [1, 2, 3, false]
                                            }],
                                            [{
                                                align: []
                                            }],
                                            [{
                                                list: 'ordered'
                                            }, {
                                                list: 'bullet'
                                            }],
                                            ['link', 'clean'],
                                        ]
                                    }
                                });
                                window._quillInstances[editorId] = quill;
                                quill.root.innerHTML = this.form[formField] || '';

                                const updateWordCount = () => {
                                    const text = quill.root.innerText.trim();
                                    const count = text ? text.split(/\s+/).filter(w => w.length > 0).length : 0;
                                    const wc = document.getElementById(wordCountId);
                                    if (wc) wc.textContent = count;
                                };
                                updateWordCount();
                                quill.on('text-change', () => {
                                    this.form[formField] = quill.root.innerHTML;
                                    updateWordCount();
                                });
                            },

                            initExtraQuill(idx) {
                                const editorId = 'quill-extra-' + idx;
                                const wordCntId = 'quill-extra-wordcount-' + idx;
                                if (!window._quillInstances) window._quillInstances = {};
                                if (window._quillInstances[editorId]) return;
                                const el = document.getElementById(editorId);
                                if (!el) return;

                                const quill = new Quill('#' + editorId, {
                                    theme: 'snow',
                                    placeholder: 'Enter section content...',
                                    modules: {
                                        toolbar: [
                                            ['bold', 'italic', 'underline'],
                                            ['link', 'clean']
                                        ]
                                    }
                                });
                                window._quillInstances[editorId] = quill;
                                quill.root.innerHTML = this.extraSections[idx]?.content || '';

                                const updateWc = () => {
                                    const text = quill.root.innerText.trim();
                                    const wc = document.getElementById(wordCntId);
                                    if (wc) wc.textContent = text ? text.split(/\s+/).filter(w => w.length > 0).length : 0;
                                };
                                updateWc();
                                quill.on('text-change', () => {
                                    if (this.extraSections[idx]) this.extraSections[idx].content = quill.root.innerHTML;
                                    updateWc();
                                });
                            },

                            addExtraSection() {
                                this.extraSections.push({
                                    title: '',
                                    content: ''
                                });
                                this.$nextTick(() => this.initExtraQuill(this.extraSections.length - 1));
                            },

                            removeExtraSection(idx) {
                                const editorId = 'quill-extra-' + idx;
                                if (window._quillInstances?.[editorId]) delete window._quillInstances[editorId];
                                this.extraSections.splice(idx, 1);
                            },

                            showToast(success, message) {
                                showToast(message, success ? 'success' : 'error');
                            },

                            // ── Resolve the correct payload value for a given key ──
                            getValueForKey(key) {
                                if (key === 'core_services')
                                    return JSON.stringify(this.form.core_services.filter(v => v.name?.trim()));
                                if (key === 'beneficiaries')
                                    return JSON.stringify(this.form.beneficiaries.filter(v => v.name?.trim()));
                                if (key === 'extra_sections')
                                    return JSON.stringify(this.extraSections.map(s => ({
                                        title: s.title,
                                        content: s.content
                                    })));
                                return this.form[key] ?? '';
                            },

                            // ── Low-level PUT to /admin/peso-info/{key} ──
                            async saveKey(key, value, label = null) {
                                const res = await fetch(`/admin/peso-info/${key}`, {
                                    method: 'PUT',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                        'Accept': 'application/json',
                                    },
                                    body: JSON.stringify({
                                        value: String(value),
                                        label,
                                    }),
                                });
                                const data = await res.json();
                                if (!res.ok || !data.success) throw new Error(data.message ?? 'Failed to save.');
                            },

                            // ── Individual Save button handler ──
                            // Writes to DB (draft) and stamps a changelog entry.
                            // Does NOT update the public page — that requires publishPesoInfo().
                            async saveKeyDraft(key, label) {
                                this.saving = true;
                                try {
                                    const value = this.getValueForKey(key);

                                    // ── Early exit: nothing changed, skip save & changelog ──
                                    const stripHtmlText = html => {
                                        const t = document.createElement('div');
                                        t.innerHTML = html || '';
                                        return t.innerText.trim();
                                    };
                                    const isTextKey = ['description', 'objective', 'how_to_avail'].includes(key);
                                    const hasChanged = isTextKey
                                        ? stripHtmlText(value) !== stripHtmlText(this._saved[key] ?? '')
                                        : value !== (this._saved[key] ?? '');
                                    if (!hasChanged) {
                                        this.showToast(true, `No changes to save for "${label}".`);
                                        return;
                                    }

                                    // Build detailed label BEFORE updating _saved mirror so diff is accurate
                                    const wordCount = str => str.trim().split(/\s+/).filter(Boolean).length;
                                    const listDiffDetail = (nowJson, savedJson, lbl) => {
                                        const nowArr   = JSON.parse(nowJson   || '[]');
                                        const savedArr = JSON.parse(savedJson || '[]');
                                        if (nowArr.length > savedArr.length)
                                            return `${lbl}: ${nowArr.length - savedArr.length} item(s) added (${nowArr.length} total)`;
                                        if (nowArr.length < savedArr.length)
                                            return `${lbl}: ${savedArr.length - nowArr.length} item(s) removed (${nowArr.length} total)`;
                                        return `${lbl}: item(s) edited (${nowArr.length} total)`;
                                    };
                                    const textDetail = (nowHtml, savedHtml, lbl) => {
                                        const t = document.createElement('div');
                                        t.innerHTML = nowHtml || ''; const wNow = wordCount(t.innerText.trim());
                                        t.innerHTML = savedHtml || ''; const wSaved = wordCount(t.innerText.trim());
                                        const diff = wNow - wSaved;
                                        const detail = diff > 0 ? `+${diff} words added` : diff < 0 ? `${Math.abs(diff)} words removed` : 'text edited';
                                        return `${lbl} — ${detail} (${wNow} words total)`;
                                    };
                                    let detailLabel = label;
                                    if (key === 'description') detailLabel = textDetail(this.form.description, this._saved.description, label);
                                    else if (key === 'objective') detailLabel = textDetail(this.form.objective, this._saved.objective, label);
                                    else if (key === 'how_to_avail') detailLabel = textDetail(this.form.how_to_avail, this._saved.how_to_avail, label);
                                    else if (key === 'core_services') detailLabel = listDiffDetail(value, this._saved.core_services, label);
                                    else if (key === 'beneficiaries') detailLabel = listDiffDetail(value, this._saved.beneficiaries, label);
                                    else if (key === 'extra_sections') detailLabel = listDiffDetail(value, this._saved.extra_sections, label);

                                    // Send value + detailLabel to server so it's persisted in the changelog cache
                                    await this.saveKey(key, value, detailLabel);

                                    // Update local _saved mirror so openSaveAllConfirm detects correctly
                                    if (['core_services', 'beneficiaries'].includes(key)) {
                                        this._saved[key] = value;
                                    } else if (key === 'extra_sections') {
                                        this._saved.extra_sections = value;
                                    } else {
                                        this._saved[key] = value;
                                    }

                                    // Mark draft state locally (server side already stamped via touchPesoInfo)
                                    this.pesoInfoHasDraft = true;
                                    this.pesoInfoChangelog.push({
                                        field: key,
                                        label: detailLabel,
                                        time: new Date().toISOString()
                                    });

                                    this.showToast(true, `"${detailLabel}" saved as draft. Click "Publish Changes" to go live.`);
                                } catch (e) {
                                    this.showToast(false, e.message ?? 'Failed to save.');
                                } finally {
                                    this.saving = false;
                                }
                            },

                            // ── Build the change list for the Save All confirm modal ──
                            openSaveAllConfirm() {
                                const changes = [];
                                const stripHtml = html => {
                                    const t = document.createElement('div');
                                    t.innerHTML = html || '';
                                    return t.innerText.trim();
                                };

                                const wordCount = str => str.trim().split(/\s+/).filter(Boolean).length;
                                const listDiff = (nowJson, savedJson, label) => {
                                    const nowArr  = JSON.parse(nowJson   || '[]');
                                    const savedArr = JSON.parse(savedJson || '[]');
                                    if (nowArr.length > savedArr.length)
                                        return `${label}: ${nowArr.length - savedArr.length} item(s) added (${nowArr.length} total)`;
                                    if (nowArr.length < savedArr.length)
                                        return `${label}: ${savedArr.length - nowArr.length} item(s) removed (${nowArr.length} total)`;
                                    return `${label}: item(s) edited (${nowArr.length} total)`;
                                };

                                const descNow  = stripHtml(this.form.description);
                                const descSaved = stripHtml(this._saved.description);
                                if (descNow !== descSaved) {
                                    const wNow = wordCount(descNow), wSaved = wordCount(descSaved);
                                    const diff = wNow - wSaved;
                                    const detail = diff > 0 ? `+${diff} words added` : diff < 0 ? `${Math.abs(diff)} words removed` : 'text edited';
                                    changes.push({ icon: 'doc', text: `Description updated — ${detail} (${wNow} words total)` });
                                }

                                const objNow  = stripHtml(this.form.objective);
                                const objSaved = stripHtml(this._saved.objective);
                                if (objNow !== objSaved) {
                                    const wNow = wordCount(objNow), wSaved = wordCount(objSaved);
                                    const diff = wNow - wSaved;
                                    const detail = diff > 0 ? `+${diff} words added` : diff < 0 ? `${Math.abs(diff)} words removed` : 'text edited';
                                    changes.push({ icon: 'doc', text: `Objective updated — ${detail} (${wNow} words total)` });
                                }

                                const htaNow  = stripHtml(this.form.how_to_avail);
                                const htaSaved = stripHtml(this._saved.how_to_avail);
                                if (htaNow !== htaSaved) {
                                    const wNow = wordCount(htaNow), wSaved = wordCount(htaSaved);
                                    const diff = wNow - wSaved;
                                    const detail = diff > 0 ? `+${diff} words added` : diff < 0 ? `${Math.abs(diff)} words removed` : 'text edited';
                                    changes.push({ icon: 'doc', text: `How to Avail updated — ${detail} (${wNow} words total)` });
                                }

                                const csNow = JSON.stringify(this.form.core_services.filter(v => v.name?.trim()));
                                if (csNow !== this._saved.core_services)
                                    changes.push({ icon: 'list', text: listDiff(csNow, this._saved.core_services, 'Core Services') });

                                const bnNow = JSON.stringify(this.form.beneficiaries.filter(v => v.name?.trim()));
                                if (bnNow !== this._saved.beneficiaries)
                                    changes.push({ icon: 'list', text: listDiff(bnNow, this._saved.beneficiaries, 'Beneficiaries') });

                                const exNow = JSON.stringify(this.extraSections.map(s => ({
                                    title: s.title,
                                    content: s.content
                                })));
                                if (exNow !== this._saved.extra_sections)
                                    changes.push({ icon: 'list', text: listDiff(exNow, this._saved.extra_sections, 'Additional Sections') });

                                if (changes.length === 0) {
                                    this.showToast(true, 'No changes detected — all fields are already up to date.');
                                    return;
                                }

                                window._pesoInfoPendingChanges = changes;
                                window.dispatchEvent(new CustomEvent('open-modal', {
                                    detail: {
                                        type: 'save-all-confirm'
                                    }
                                }));
                            },

                            // ── Save All: writes every field to DB as a draft ──
                            // Detects which fields actually changed and records each one
                            // individually in the changelog so the draft banner is granular.
                            // Public page is NOT updated until publishPesoInfo() is called.
                            async saveAll() {
                                this.saving = true;
                                try {
                                    // ── Helpers ──
                                    const stripHtml = html => {
                                        const t = document.createElement('div');
                                        t.innerHTML = html || '';
                                        return t.innerText.trim();
                                    };
                                    const now = new Date().toISOString();

                                    // ── Compute current serialised values ──
                                    const csNow = JSON.stringify(this.form.core_services.filter(v => v.name?.trim()));
                                    const bnNow = JSON.stringify(this.form.beneficiaries.filter(v => v.name?.trim()));
                                    const exNow = JSON.stringify(this.extraSections.map(s => ({
                                        title: s.title,
                                        content: s.content
                                    })));

                                    // ── Detect which fields have actually changed ──
                                    const fieldLabels = {
                                        description: 'Description',
                                        objective: 'Objective',
                                        how_to_avail: 'How to Avail',
                                        core_services: 'Core Services',
                                        beneficiaries: 'Beneficiaries',
                                        extra_sections: 'Additional Sections',
                                    };

                                    const changedFields = [];

                                    if (stripHtml(this.form.description) !== stripHtml(this._saved.description))
                                        changedFields.push('description');
                                    if (stripHtml(this.form.objective) !== stripHtml(this._saved.objective))
                                        changedFields.push('objective');
                                    if (stripHtml(this.form.how_to_avail) !== stripHtml(this._saved.how_to_avail))
                                        changedFields.push('how_to_avail');
                                    if (csNow !== this._saved.core_services)
                                        changedFields.push('core_services');
                                    if (bnNow !== this._saved.beneficiaries)
                                        changedFields.push('beneficiaries');
                                    if (exNow !== this._saved.extra_sections)
                                        changedFields.push('extra_sections');

                                    // ── Always save every field to DB (ensures full consistency) ──
                                    await Promise.all([
                                        this.saveKey('description', this.form.description),
                                        this.saveKey('objective', this.form.objective),
                                        this.saveKey('how_to_avail', this.form.how_to_avail),
                                        this.saveKey('core_services', csNow),
                                        this.saveKey('beneficiaries', bnNow),
                                        this.saveKey('extra_sections', exNow),
                                    ]);

                                    // ── Update _saved mirrors ──
                                    this._saved.description = this.form.description;
                                    this._saved.objective = this.form.objective;
                                    this._saved.how_to_avail = this.form.how_to_avail;
                                    this._saved.core_services = csNow;
                                    this._saved.beneficiaries = bnNow;
                                    this._saved.extra_sections = exNow;

                                    // ── Append granular changelog entries for each changed field ──
                                    // If nothing changed we still record a "re-saved" entry so the
                                    // draft banner is never left empty after clicking Save All.
                                    this.pesoInfoHasDraft = true;

                                    if (changedFields.length > 0) {
                                        const wordCount = str => str.trim().split(/\s+/).filter(Boolean).length;
                                        const listDiff = (nowJson, savedJson, label) => {
                                            const nowArr   = JSON.parse(nowJson   || '[]');
                                            const savedArr = JSON.parse(savedJson || '[]');
                                            if (nowArr.length > savedArr.length)
                                                return `${label}: ${nowArr.length - savedArr.length} item(s) added (${nowArr.length} total)`;
                                            if (nowArr.length < savedArr.length)
                                                return `${label}: ${savedArr.length - nowArr.length} item(s) removed (${nowArr.length} total)`;
                                            return `${label}: item(s) edited (${nowArr.length} total)`;
                                        };
                                        const textDetail = (nowHtml, savedHtml, label) => {
                                            const t = document.createElement('div');
                                            t.innerHTML = nowHtml || ''; const wNow = wordCount(t.innerText.trim());
                                            t.innerHTML = savedHtml || ''; const wSaved = wordCount(t.innerText.trim());
                                            const diff = wNow - wSaved;
                                            const detail = diff > 0 ? `+${diff} words added` : diff < 0 ? `${Math.abs(diff)} words removed` : 'text edited';
                                            return `${label} — ${detail} (${wNow} words total)`;
                                        };
                                        changedFields.forEach(field => {
                                            let detail = fieldLabels[field] ?? field;
                                            if (field === 'description') detail = textDetail(this.form.description, this._saved.description, 'Description');
                                            else if (field === 'objective') detail = textDetail(this.form.objective, this._saved.objective, 'Objective');
                                            else if (field === 'how_to_avail') detail = textDetail(this.form.how_to_avail, this._saved.how_to_avail, 'How to Avail');
                                            else if (field === 'core_services') detail = listDiff(csNow, this._saved.core_services, 'Core Services');
                                            else if (field === 'beneficiaries') detail = listDiff(bnNow, this._saved.beneficiaries, 'Beneficiaries');
                                            else if (field === 'extra_sections') detail = listDiff(exNow, this._saved.extra_sections, 'Additional Sections');
                                            this.pesoInfoChangelog.push({
                                                field,
                                                label: detail,
                                                time: now,
                                                via: 'save_all',
                                            });
                                        });
                                    }

                                    const count = changedFields.length;
                                    const summary = count > 0 ?
                                        `${count} field${count > 1 ? 's' : ''} saved as draft (${changedFields.map(f => fieldLabels[f]).join(', ')}). Click "Publish Changes" to go live.` :
                                        'No changes detected — all fields re-saved as draft.';

                                    this.showToast(true, summary);
                                } catch (e) {
                                    this.showToast(false, e.message ?? 'Something went wrong saving all fields.');
                                } finally {
                                    this.saving = false;
                                }
                            },

                            // ── Opens the Publish PESO Info confirm modal ──
                            openPublishPesoInfoConfirm() {
                                window.dispatchEvent(new CustomEvent('open-modal', {
                                    detail: {
                                        type: 'publish-peso-info'
                                    }
                                }));
                            },

                            // ── Pushes current DB state into the public Cache snapshot ──
                            async publishPesoInfo() {
                                this.publishing = true;
                                try {
                                    const res = await fetch('/admin/peso-info/publish', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                            'Accept': 'application/json',
                                        },
                                    });
                                    const data = await res.json();
                                    if (data.success) {
                                        this.pesoInfoHasDraft = false;
                                        this.pesoInfoChangelog = [];
                                        window.dispatchEvent(new CustomEvent('show-success-modal', {
                                            detail: {
                                                title: 'PESO Info Published!',
                                                message: 'The PESO Info section is now live and visible to the public.'
                                            }
                                        }));
                                    } else {
                                        showToast(data.message ?? 'Publish failed. Please try again.', 'error');
                                    }
                                } catch (e) {
                                    showToast('An error occurred. Please try again.', 'error');
                                } finally {
                                    this.publishing = false;
                                }
                            },
                        };
                    }


                    {{-- ══ DIRECTORY BROWSER ══ --}}

                    function pesoDirectory() {
                        return {
                            province: '',
                            officeType: '',
                            showType: false,
                            showResults: false,
                            openId: null,
                            pesoData: {},
                            officeTypes: [],
                            search: '',
                            _fuseCache: {},
                            async init() {
                                this.pesoData = window._pesoData ?? {};
                                try {
                                    const res = await fetch('/admin/office-types', {
                                        headers: {
                                            'X-Requested-With': 'XMLHttpRequest'
                                        }
                                    });
                                    if (res.ok) this.officeTypes = await res.json();
                                } catch (e) {}
                                window.addEventListener('office-type-added', evt => {
                                    if (!this.officeTypes.includes(evt.detail.name)) {
                                        this.officeTypes.push(evt.detail.name);
                                        this.officeTypes.sort();
                                    }
                                });
                                window.addEventListener('office-type-deleted', evt => {
                                    this.officeTypes = this.officeTypes.filter(t => t !== evt.detail.name);
                                    if (this.officeType === evt.detail.name) {
                                        this.officeType = '';
                                        this.showResults = false;
                                    }
                                });
                                window.addEventListener('office-type-renamed', evt => {
                                    const idx = this.officeTypes.indexOf(evt.detail.oldName);
                                    if (idx !== -1) this.officeTypes.splice(idx, 1, evt.detail.newName);
                                    this.officeTypes.sort();
                                    if (this.officeType === evt.detail.oldName) this.officeType = evt.detail.newName;
                                });
                            },
                            toggleCard(id) {
                                this.openId = (this.openId === id) ? null : id;
                            },
                            isOpen(id) {
                                return this.openId === id;
                            },
                            selectProvince(val) {
                                this.province = val;
                                this.officeType = '';
                                this.showResults = false;
                                this.showType = !!val;
                                this.openId = null;
                                this.search = '';
                                this._fuseCache = {};
                                if (val) this.$nextTick(() => this.$refs.typeSection?.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'nearest'
                                }));
                            },
                            selectType(t) {
                                this.officeType = t;
                                this.showResults = !!t;
                                this.openId = null;
                                this.search = '';
                                if (t) this.$nextTick(() => this.$refs.resultsSection?.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'nearest'
                                }));
                            },
                            countFor(province, type) {
                                const entries = this.pesoData?.[province] ?? [];
                                if (type === 'ALL') return entries.length;
                                return entries.filter(e => e.type === type).length;
                            },
                            filteredEntries() {
                                let entries = this.pesoData?.[this.province] ?? [];
                                if (this.officeType !== 'ALL') entries = entries.filter(e => e.type === this.officeType);
                                if (!this.search.trim()) return entries;
                                const cacheKey = this.province + '|' + this.officeType;
                                if (!this._fuseCache[cacheKey] || this._fuseCache[cacheKey]._list !== entries) {
                                    this._fuseCache[cacheKey] = new Fuse(entries, {
                                        keys: [{
                                            name: 'name',
                                            weight: 0.6
                                        }, {
                                            name: 'persons_name',
                                            weight: 0.3
                                        }, {
                                            name: 'type',
                                            weight: 0.1
                                        }],
                                        threshold: 0.4,
                                        distance: 200,
                                        minMatchCharLength: 2,
                                        includeScore: true,
                                    });
                                    this._fuseCache[cacheKey]._list = entries;
                                }
                                return this._fuseCache[cacheKey].search(this.search.trim()).map(r => r.item);
                            },
                            typeColor(idx, part) {
                                const p = [{
                                        main: '#3b82f6',
                                        bg: '#eff6ff',
                                        border: '#bfdbfe'
                                    },
                                    {
                                        main: '#f97316',
                                        bg: '#fff7ed',
                                        border: '#fed7aa'
                                    },
                                    {
                                        main: '#10b981',
                                        bg: '#ecfdf5',
                                        border: '#a7f3d0'
                                    },
                                    {
                                        main: '#8b5cf6',
                                        bg: '#f5f3ff',
                                        border: '#ddd6fe'
                                    },
                                    {
                                        main: '#ec4899',
                                        bg: '#fdf2f8',
                                        border: '#fbcfe8'
                                    },
                                    {
                                        main: '#14b8a6',
                                        bg: '#f0fdfa',
                                        border: '#99f6e4'
                                    },
                                    {
                                        main: '#f59e0b',
                                        bg: '#fffbeb',
                                        border: '#fde68a'
                                    },
                                    {
                                        main: '#6366f1',
                                        bg: '#eef2ff',
                                        border: '#c7d2fe'
                                    },
                                ];
                                return p[idx % p.length][part];
                            },
                        };
                    }
                </script>

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
                                        <div class="flex items-center gap-3 px-4 py-3 cursor-pointer select-none"
                                            @click.stop="toggleCard(entry.id)">
                                            <div class="w-9 h-9 rounded-lg flex-shrink-0 flex items-center justify-center text-white font-bold text-sm"
                                                :style="entry.type === 'JPO' ? 'background:#3b82f6' : 'background:#22c55e'"
                                                x-text="entry.name.charAt(0)"></div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-slate-800 truncate"
                                                    style="text-transform: capitalize;"
                                                    x-text="entry.name.toLowerCase().replace(/^(peso|jpo)\s+/i, '')">
                                                </p>
                                                <p class="text-xs text-slate-400 truncate font-semibold uppercase tracking-wide"
                                                    x-text="(entry.position_title || '') + (entry.persons_name ? ' · ' + entry.persons_name : '')">
                                                </p>
                                            </div>
                                            <span
                                                class="inline-flex items-center text-xs font-bold px-2 py-0.5 rounded flex-shrink-0"
                                                :style="entry.type === 'JPO' ?
                                                    'background:#eff6ff; color:#3b82f6; border:1px solid #bfdbfe' :
                                                    'background:#f0fdf4; color:#22c55e; border:1px solid #bbf7d0'"
                                                x-text="entry.type"></span>
                                            <svg class="w-4 h-4 text-slate-400 flex-shrink-0"
                                                style="transition: transform 0.2s ease;"
                                                :style="isOpen(entry.id) ? 'transform:rotate(180deg)' : 'transform:rotate(0deg)'"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
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
                                <div x-data="officeTypeSelector()" x-init="init()">
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Office Type <span
                                            class="text-red-500">*</span></label>
                                    <div x-show="mode === 'select'" class="space-y-2">
                                        <div class="flex gap-2">
                                            <select x-model="form.type"
                                                :class="formErrors.type ? 'border-red-500 ring-2 ring-red-200' :
                                                    'border-slate-300 focus:ring-indigo-400'"
                                                @change="formErrors.type = false"
                                                class="flex-1 border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none bg-white cursor-pointer">
                                                <option value="" disabled selected hidden>— Select type —
                                                </option>
                                                <template x-for="t in types" :key="t">
                                                    <option :value="t" x-text="t"
                                                        :selected="form.type === t"></option>
                                                </template>
                                            </select>
                                            <button type="button" @click="mode = 'add'; inputName = ''"
                                                title="Add new type"
                                                class="flex-shrink-0 w-10 h-10 rounded-lg bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-600 flex items-center justify-center transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.5" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </button>
                                            <button type="button" @click="startEdit()" :disabled="!form.type"
                                                title="Edit selected type"
                                                class="flex-shrink-0 w-10 h-10 rounded-lg bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-600 flex items-center justify-center transition disabled:opacity-30 disabled:cursor-not-allowed">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button type="button" @click="mode = 'delete'" :disabled="!form.type"
                                                title="Delete selected type"
                                                class="flex-shrink-0 w-10 h-10 rounded-lg bg-red-50 hover:bg-red-100 border border-red-200 text-red-500 flex items-center justify-center transition disabled:opacity-30 disabled:cursor-not-allowed">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                        <p x-show="typeError" x-text="typeError"
                                            class="text-xs text-red-500 font-semibold" x-cloak></p>
                                        <p x-show="formErrors.type && !typeError"
                                            class="text-xs text-red-500 font-semibold" x-cloak>Please select an office
                                            type.</p>
                                    </div>
                                    <div x-show="mode === 'add'" x-cloak class="space-y-2">
                                        <div class="flex gap-2">
                                            <input type="text" x-model="inputName"
                                                @keydown.enter.prevent="saveNewType(form)"
                                                @keydown.escape.prevent="mode = 'select'" placeholder="e.g. SFO"
                                                class="flex-1 border border-indigo-400 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none"
                                                x-ref="addInput">
                                            <button type="button" @click="saveNewType(form)" :disabled="saving"
                                                class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition disabled:opacity-50">
                                                <span x-show="!saving">Save</span><span x-show="saving"
                                                    x-cloak>…</span>
                                            </button>
                                            <button type="button" @click="mode = 'select'; typeError = ''"
                                                class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-lg transition">Cancel</button>
                                        </div>
                                        <p x-show="typeError" x-text="typeError"
                                            class="text-xs text-red-500 font-semibold" x-cloak></p>
                                    </div>
                                    <div x-show="mode === 'edit'" x-cloak class="space-y-2">
                                        <p class="text-xs text-slate-500">Renaming: <strong
                                                x-text="form.type"></strong></p>
                                        <div class="flex gap-2">
                                            <input type="text" x-model="inputName"
                                                @keydown.enter.prevent="updateType(form)"
                                                @keydown.escape.prevent="mode = 'select'" placeholder="New name..."
                                                class="flex-1 border border-amber-400 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-400 outline-none"
                                                x-ref="editInput">
                                            <button type="button" @click="updateType(form)" :disabled="saving"
                                                class="px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg transition disabled:opacity-50">
                                                <span x-show="!saving">Rename</span><span x-show="saving"
                                                    x-cloak>…</span>
                                            </button>
                                            <button type="button" @click="mode = 'select'; typeError = ''"
                                                class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-lg transition">Cancel</button>
                                        </div>
                                        <p x-show="typeError" x-text="typeError"
                                            class="text-xs text-red-500 font-semibold" x-cloak></p>
                                    </div>
                                    <div x-show="mode === 'delete'" x-cloak
                                        class="rounded-lg border border-red-200 bg-red-50 p-3 space-y-2">
                                        <p class="text-sm text-red-700 font-semibold">Delete type "<span
                                                x-text="form.type"></span>"?</p>
                                        <p class="text-xs text-red-500">This only removes it from the type list.
                                            Existing offices are not affected.</p>
                                        <div class="flex gap-2">
                                            <button type="button" @click="deleteType(form)" :disabled="saving"
                                                class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg transition disabled:opacity-50">
                                                <span x-show="!saving">Yes, Delete</span><span x-show="saving"
                                                    x-cloak>…</span>
                                            </button>
                                            <button type="button" @click="mode = 'select'"
                                                class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-lg transition">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Province <span
                                            class="text-red-500">*</span></label>
                                    <select x-model="form.province"
                                        :class="formErrors.province ? 'border-red-500 ring-2 ring-red-200' :
                                            'border-slate-300 focus:ring-indigo-400'"
                                        @change="formErrors.province = false"
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
                            <div x-data="positionTitleSelector()" x-init="init()">
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Position Title <span
                                        class="text-red-500">*</span></label>

                                {{-- SELECT mode --}}
                                <div x-show="mode === 'select'" class="space-y-2">
                                    <div class="flex gap-2">
                                        <select x-model="form.position_title"
                                            :class="formErrors.position_title ? 'border-red-500 ring-2 ring-red-200' :
                                                'border-slate-300 focus:ring-indigo-400'"
                                            @change="formErrors.position_title = false"
                                            class="flex-1 border rounded-lg px-4 py-2.5 text-sm focus:ring-2 outline-none bg-white cursor-pointer">
                                            <option value="" disabled selected hidden>— Select position —
                                            </option>
                                            <template x-for="t in titles" :key="t">
                                                <option :value="t" x-text="t"
                                                    :selected="form.position_title === t"></option>
                                            </template>
                                        </select>
                                        <button type="button" @click="mode = 'add'; inputName = ''"
                                            title="Add new position title"
                                            class="flex-shrink-0 w-10 h-10 rounded-lg bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-600 flex items-center justify-center transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2.5" d="M12 4v16m8-8H4" />
                                            </svg>
                                        </button>
                                        <button type="button" @click="startEdit()" :disabled="!form.position_title"
                                            title="Rename selected position"
                                            class="flex-shrink-0 w-10 h-10 rounded-lg bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-600 flex items-center justify-center transition disabled:opacity-30 disabled:cursor-not-allowed">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button type="button" @click="mode = 'delete'"
                                            :disabled="!form.position_title" title="Delete selected position"
                                            class="flex-shrink-0 w-10 h-10 rounded-lg bg-red-50 hover:bg-red-100 border border-red-200 text-red-500 flex items-center justify-center transition disabled:opacity-30 disabled:cursor-not-allowed">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                    <p x-show="titleError" x-text="titleError"
                                        class="text-xs text-red-500 font-semibold" x-cloak></p>
                                    <p x-show="formErrors.position_title && !titleError"
                                        class="text-xs text-red-500 font-semibold" x-cloak>Please select a position
                                        title.</p>
                                </div>

                                {{-- ADD mode --}}
                                <div x-show="mode === 'add'" x-cloak class="space-y-2">
                                    <div class="flex gap-2">
                                        <input type="text" x-model="inputName"
                                            @keydown.enter.prevent="saveNewTitle(form)"
                                            @keydown.escape.prevent="mode = 'select'" placeholder="e.g. District Head"
                                            class="flex-1 border border-indigo-400 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-400 outline-none">
                                        <button type="button" @click="saveNewTitle(form)" :disabled="saving"
                                            class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition disabled:opacity-50">
                                            <span x-show="!saving">Save</span><span x-show="saving" x-cloak>…</span>
                                        </button>
                                        <button type="button" @click="mode = 'select'; titleError = ''"
                                            class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-lg transition">Cancel</button>
                                    </div>
                                    <p x-show="titleError" x-text="titleError"
                                        class="text-xs text-red-500 font-semibold" x-cloak></p>
                                </div>

                                {{-- EDIT mode --}}
                                <div x-show="mode === 'edit'" x-cloak class="space-y-2">
                                    <p class="text-xs text-slate-500">Renaming: <strong
                                            x-text="form.position_title"></strong></p>
                                    <div class="flex gap-2">
                                        <input type="text" x-model="inputName"
                                            @keydown.enter.prevent="updateTitle(form)"
                                            @keydown.escape.prevent="mode = 'select'" placeholder="New name..."
                                            class="flex-1 border border-amber-400 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-amber-400 outline-none"
                                            x-ref="editTitleInput">
                                        <button type="button" @click="updateTitle(form)" :disabled="saving"
                                            class="px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg transition disabled:opacity-50">
                                            <span x-show="!saving">Rename</span><span x-show="saving" x-cloak>…</span>
                                        </button>
                                        <button type="button" @click="mode = 'select'; titleError = ''"
                                            class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-lg transition">Cancel</button>
                                    </div>
                                    <p x-show="titleError" x-text="titleError"
                                        class="text-xs text-red-500 font-semibold" x-cloak></p>
                                </div>

                                {{-- DELETE confirm --}}
                                <div x-show="mode === 'delete'" x-cloak
                                    class="rounded-lg border border-red-200 bg-red-50 p-3 space-y-2">
                                    <p class="text-sm text-red-700 font-semibold">Delete position "<span
                                            x-text="form.position_title"></span>"?</p>
                                    <p class="text-xs text-red-500">This only removes it from the list. Existing
                                        offices are not affected.</p>
                                    <div class="flex gap-2">
                                        <button type="button" @click="deleteTitle(form)" :disabled="saving"
                                            class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg transition disabled:opacity-50">
                                            <span x-show="!saving">Yes, Delete</span><span x-show="saving"
                                                x-cloak>…</span>
                                        </button>
                                        <button type="button" @click="mode = 'select'"
                                            class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-lg transition">Cancel</button>
                                    </div>
                                </div>
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

    <script>
        function escapeText(str) {
            const div = document.createElement('div');
            div.textContent = String(str ?? '');
            return div.innerHTML;
        }

        document.addEventListener('alpine:init', () => {
            Alpine.store('pesoDirectory', {
                hasDraftChanges: @json($directoryHasDraft),
                changeLog: @json($directoryChangelog),
                markDirty(entry) {
                    this.hasDraftChanges = true;
                    this.changeLog.push(entry);
                },
                reset() {
                    this.hasDraftChanges = false;
                    this.changeLog = [];
                },
            });
            Alpine.data('pesoCard', (id) => ({
                entryId: id
            }));
        });

        function adminPesoPage() {
            const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            async function jsonRequest(method, url, body = {}) {
                try {
                    const res = await fetch(url, {
                        method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(body),
                    });
                    if (!res.ok) {
                        try {
                            const errBody = await res.json();
                            const msg = errBody.message || Object.values(errBody.errors || {})[0]?.[0] ||
                                `Server error (${res.status}).`;
                            return {
                                success: false,
                                message: msg
                            };
                        } catch {
                            return {
                                success: false,
                                message: `Server error (${res.status}).`
                            };
                        }
                    }
                    return res.json();
                } catch (e) {
                    return {
                        success: false,
                        message: 'Network error. Please check your connection.'
                    };
                }
            }

            return {
                modal: {
                    open: false,
                    type: null,
                    title: '',
                    id: null,
                    loading: false,
                    error: null
                },
                form: {},
                formErrors: {},

                openModal(detail) {
                    if (['add-slide', 'edit-slide', 'delete-slide'].includes(detail.type)) return;
                    const titles = {
                        'add-peso': 'Add PESO / JPO Office',
                        'edit-peso': 'Edit PESO / JPO Office',
                        'delete-peso': 'Delete PESO / JPO Office',
                        'publish-directory': 'Publish PESO / JPO Directory',
                        'publish-peso-info': 'Publish PESO Info to Public',
                        'delete-list-item': 'Delete Item',
                        'save-all-confirm': 'Save All Changes',
                    };
                    this.modal = {
                        open: true,
                        type: detail.type,
                        title: titles[detail.type] ?? 'Edit',
                        id: detail.id ?? null,
                        loading: false,
                        error: null,
                        listKey: detail.listKey ?? null,
                        listIndex: detail.listIndex ?? null
                    };
                    this.formErrors = {};
                    this.form = detail.data ? {
                        ...detail.data
                    } : {};
                },

                fail(msg) {
                    this.modal.loading = false;
                    showToast(msg || 'Something went wrong. Please try again.', 'error');
                },

                confirmListItemDelete() {
                    const key = this.modal.listKey;
                    const idx = this.modal.listIndex;
                    if (key !== null && idx !== null) {
                        const editorEl = document.getElementById('peso-info-editor');
                        const editorData = editorEl?._x_dataStack?.[0];
                        if (editorData && Array.isArray(editorData.form[key])) {
                            editorData.form[key] = editorData.form[key].filter((_, i) => i !== idx);
                        }
                    }
                    this.modal.open = false;
                },

                async submitFieldOffice() {
                    this.formErrors = {};
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!this.form.name?.trim()) this.formErrors.name = true;
                    if (!this.form.type) this.formErrors.type = true;
                    if (!this.form.province?.trim()) this.formErrors.province = true;
                    if (!this.form.persons_name?.trim()) this.formErrors.persons_name = true;
                    if (!this.form.position_title?.trim()) this.formErrors.position_title = true;
                    if (!this.form.email?.trim() || !emailRegex.test(this.form.email.trim())) this.formErrors.email =
                        true;
                    if (!this.form.address?.trim()) this.formErrors.address = true;
                    if (Object.keys(this.formErrors).length) return;

                    this.modal.loading = true;
                    const isEdit = this.modal.type === 'edit-peso';
                    const body = {
                        name: this.form.name,
                        office_type: this.form.type,
                        province: this.form.province,
                        persons_name: this.form.persons_name,
                        position_title: this.form.position_title,
                        email: this.form.email,
                        address: this.form.address
                    };
                    const res = await jsonRequest(isEdit ? 'PUT' : 'POST', isEdit ?
                        `/admin/field-offices/${this.modal.id}` : '/admin/field-offices', body);

                    if (res.success) {
                        const prov = this.form.province;
                        const pesoState = document.getElementById('peso-ajax-container')?._x_dataStack?.[0];
                        if (pesoState) {
                            if (!pesoState.pesoData[prov]) pesoState.pesoData[prov] = [];
                            if (isEdit) {
                                const idx = pesoState.pesoData[prov].findIndex(e => e.id === this.modal.id);
                                if (idx !== -1) {
                                    pesoState.pesoData[prov][idx] = {
                                        ...pesoState.pesoData[prov][idx],
                                        name: body.name,
                                        type: body.office_type,
                                        persons_name: body.persons_name,
                                        position_title: body.position_title,
                                        email: body.email,
                                        address: body.address,
                                        id: this.modal.id
                                    };
                                    pesoState.pesoData[prov] = [...pesoState.pesoData[prov]];
                                }
                            } else {
                                pesoState.pesoData[prov] = [...pesoState.pesoData[prov], {
                                    id: res.id ?? Date.now(),
                                    name: body.name,
                                    type: body.office_type,
                                    persons_name: body.persons_name,
                                    position_title: body.position_title,
                                    email: body.email,
                                    address: body.address
                                }];
                            }
                        }
                        Alpine.store('pesoDirectory').markDirty({
                            action: isEdit ? 'edited' : 'added',
                            label: body.name,
                            type: body.office_type,
                            province: prov,
                            time: new Date().toLocaleTimeString([], {
                                hour: '2-digit',
                                minute: '2-digit'
                            })
                        });
                        jsonRequest('POST', '/admin/field-offices/touch', {
                            action: isEdit ? 'edited' : 'added',
                            label: body.name,
                            type: body.office_type,
                            province: prov,
                            time: new Date().toLocaleTimeString([], {
                                hour: '2-digit',
                                minute: '2-digit'
                            })
                        }).catch(() => {});
                        this.modal.open = false;
                        window.dispatchEvent(new CustomEvent('show-success-modal', {
                            detail: {
                                title: isEdit ? 'Office Updated!' : 'Office Added!',
                                message: escapeText(body.name) + (isEdit ?
                                    ' has been updated successfully.' : ' has been added to ' +
                                    escapeText(prov) + '.')
                            }
                        }));
                    } else {
                        this.fail(res.message);
                    }
                },

                async destroyFieldOffice() {
                    this.modal.loading = true;
                    const res = await jsonRequest('DELETE', `/admin/field-offices/${this.modal.id}`);
                    if (res.success) {
                        const pesoState = document.getElementById('peso-ajax-container')?._x_dataStack?.[0];
                        let deletedName = 'Unknown',
                            deletedType = '',
                            deletedProv = '';
                        if (pesoState) {
                            for (const prov in pesoState.pesoData) {
                                const found = pesoState.pesoData[prov].find(e => e.id === this.modal.id);
                                if (found) {
                                    deletedName = found.name;
                                    deletedType = found.type;
                                    deletedProv = prov;
                                    break;
                                }
                            }
                            for (const prov in pesoState.pesoData) {
                                pesoState.pesoData[prov] = pesoState.pesoData[prov].filter(e => e.id !== this.modal.id);
                            }
                        }
                        Alpine.store('pesoDirectory').markDirty({
                            action: 'deleted',
                            label: deletedName,
                            type: deletedType,
                            province: deletedProv,
                            time: new Date().toLocaleTimeString([], {
                                hour: '2-digit',
                                minute: '2-digit'
                            })
                        });
                        jsonRequest('POST', '/admin/field-offices/touch', {
                            action: 'deleted',
                            label: deletedName,
                            type: deletedType,
                            province: deletedProv,
                            time: new Date().toLocaleTimeString([], {
                                hour: '2-digit',
                                minute: '2-digit'
                            })
                        }).catch(() => {});
                        this.modal.open = false;
                        window.dispatchEvent(new CustomEvent('show-success-modal', {
                            detail: {
                                title: 'Office Deleted',
                                message: escapeText(deletedName) + ' has been removed from the directory.'
                            }
                        }));
                    } else {
                        this.fail(res.message);
                    }
                },

                async submitPublishDirectory() {
                    this.modal.loading = true;
                    const res = await jsonRequest('POST', '/admin/field-offices/publish');
                    if (res.success) {
                        Alpine.store('pesoDirectory').reset();
                        this.modal.open = false;
                        window.dispatchEvent(new CustomEvent('show-success-modal', {
                            detail: {
                                title: 'Directory Published!',
                                message: 'The PESO / JPO Directory is now live and visible to the public.'
                            }
                        }));
                    } else {
                        this.fail(res.message ?? 'Failed to publish directory.');
                    }
                },
            };
        }

        function officeTypeSelector() {
            return {
                types: [],
                mode: 'select',
                inputName: '',
                saving: false,
                typeError: '',
                async init() {
                    try {
                        const res = await fetch('/admin/office-types', {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (res.ok) this.types = await res.json();
                    } catch (e) {}
                },
                startEdit() {
                    this.inputName = '';
                    this.mode = 'edit';
                },
                async saveNewType(form) {
                    this.typeError = '';
                    const name = this.inputName.trim().toUpperCase();
                    if (!name) {
                        this.typeError = 'Please enter a type name.';
                        return;
                    }
                    if (this.types.includes(name)) {
                        this.typeError = 'That type already exists.';
                        return;
                    }
                    this.saving = true;
                    try {
                        const res = await fetch('/admin/office-types', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                name
                            })
                        });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.types.push(data.name);
                            this.types.sort();
                            form.type = data.name;
                            this.mode = 'select';
                            this.inputName = '';
                            window.dispatchEvent(new CustomEvent('office-type-added', {
                                detail: {
                                    name: data.name
                                }
                            }));
                        } else {
                            this.typeError = data.message ?? 'Failed to save type.';
                        }
                    } catch (e) {
                        this.typeError = 'Network error. Please try again.';
                    }
                    this.saving = false;
                },
                async updateType(form) {
                    this.typeError = '';
                    const oldName = form.type;
                    const newName = this.inputName.trim().toUpperCase();
                    if (!newName) {
                        this.typeError = 'Please enter a new name.';
                        return;
                    }
                    if (newName === oldName) {
                        this.mode = 'select';
                        return;
                    }
                    if (this.types.includes(newName)) {
                        this.typeError = 'That type already exists.';
                        return;
                    }
                    this.saving = true;
                    try {
                        const res = await fetch('/admin/office-types/' + encodeURIComponent(oldName), {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                name: newName
                            })
                        });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            const idx = this.types.indexOf(oldName);
                            if (idx !== -1) this.types.splice(idx, 1, newName);
                            this.types.sort();
                            form.type = newName;
                            this.mode = 'select';
                            this.inputName = '';
                            window.dispatchEvent(new CustomEvent('office-type-renamed', {
                                detail: {
                                    oldName,
                                    newName
                                }
                            }));
                        } else {
                            this.typeError = data.message ?? 'Failed to rename type.';
                        }
                    } catch (e) {
                        this.typeError = 'Network error. Please try again.';
                    }
                    this.saving = false;
                },
                async deleteType(form) {
                    const name = form.type;
                    this.saving = true;
                    try {
                        const res = await fetch('/admin/office-types/' + encodeURIComponent(name), {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.types = this.types.filter(t => t !== name);
                            form.type = '';
                            this.mode = 'select';
                            window.dispatchEvent(new CustomEvent('office-type-deleted', {
                                detail: {
                                    name
                                }
                            }));
                            window.dispatchEvent(new CustomEvent('show-success-modal', {
                                detail: {
                                    title: 'Type Deleted',
                                    message: 'Office type "' + escapeText(name) + '" has been removed.'
                                }
                            }));
                        } else {
                            this.typeError = data.message ?? 'Failed to delete type.';
                            this.mode = 'select';
                        }
                    } catch (e) {
                        this.typeError = 'Network error. Please try again.';
                        this.mode = 'select';
                    }
                    this.saving = false;
                },
            };
        }

        // ─── Position Title Selector ─────────────────────────────────────────────
        function positionTitleSelector() {
            return {
                titles: [],
                mode: 'select',
                inputName: '',
                saving: false,
                titleError: '',

                async init() {
                    try {
                        const res = await fetch('/admin/position-titles', {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (res.ok) this.titles = await res.json();
                    } catch (e) {}
                },

                startEdit() {
                    this.inputName = '';
                    this.mode = 'edit';
                    this.$nextTick(() => this.$refs.editTitleInput?.focus());
                },

                async saveNewTitle(form) {
                    this.titleError = '';
                    const name = this.inputName.trim();
                    if (!name) {
                        this.titleError = 'Please enter a position title.';
                        return;
                    }
                    if (this.titles.includes(name)) {
                        this.titleError = 'That title already exists.';
                        return;
                    }
                    this.saving = true;
                    try {
                        const res = await fetch('/admin/position-titles', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                name
                            })
                        });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.titles.push(data.name);
                            this.titles.sort();
                            form.position_title = data.name;
                            this.mode = 'select';
                            this.inputName = '';
                        } else {
                            this.titleError = data.message ?? 'Failed to save.';
                        }
                    } catch (e) {
                        this.titleError = 'Network error. Please try again.';
                    }
                    this.saving = false;
                },

                async updateTitle(form) {
                    this.titleError = '';
                    const oldName = form.position_title;
                    const newName = this.inputName.trim();
                    if (!newName) {
                        this.titleError = 'Please enter a new name.';
                        return;
                    }
                    if (newName === oldName) {
                        this.mode = 'select';
                        return;
                    }
                    if (this.titles.includes(newName)) {
                        this.titleError = 'That title already exists.';
                        return;
                    }
                    this.saving = true;
                    try {
                        const res = await fetch('/admin/position-titles/' + encodeURIComponent(oldName), {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                name: newName
                            })
                        });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            const idx = this.titles.indexOf(oldName);
                            if (idx !== -1) this.titles.splice(idx, 1, newName);
                            this.titles.sort();
                            form.position_title = newName;
                            this.mode = 'select';
                            this.inputName = '';
                        } else {
                            this.titleError = data.message ?? 'Failed to rename.';
                        }
                    } catch (e) {
                        this.titleError = 'Network error. Please try again.';
                    }
                    this.saving = false;
                },

                async deleteTitle(form) {
                    const name = form.position_title;
                    this.saving = true;
                    try {
                        const res = await fetch('/admin/position-titles/' + encodeURIComponent(name), {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.titles = this.titles.filter(t => t !== name);
                            form.position_title = '';
                            this.mode = 'select';
                            window.dispatchEvent(new CustomEvent('show-success-modal', {
                                detail: {
                                    title: 'Position Deleted',
                                    message: 'Position title "' + escapeText(name) + '" has been removed.'
                                }
                            }));
                        } else {
                            this.titleError = data.message ?? 'Failed to delete.';
                            this.mode = 'select';
                        }
                    } catch (e) {
                        this.titleError = 'Network error. Please try again.';
                        this.mode = 'select';
                    }
                    this.saving = false;
                },
            };
        }

        // ─── Toast Notification System ──────────────────────────────────────────
        function showToast(message, type = 'error') {
            const container = document.getElementById('toastContainer');

            const configs = {
                error: {
                    bg: 'bg-red-50 border-red-400',
                    icon: `<svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                           </svg>`,
                    text: 'text-red-800',
                    bar: 'bg-red-400',
                },
                warning: {
                    bg: 'bg-amber-50 border-amber-400',
                    icon: `<svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                           </svg>`,
                    text: 'text-amber-800',
                    bar: 'bg-amber-400',
                },
                success: {
                    bg: 'bg-green-50 border-green-400',
                    icon: `<svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                           </svg>`,
                    text: 'text-green-800',
                    bar: 'bg-green-400',
                },
                info: {
                    bg: 'bg-blue-50 border-blue-400',
                    icon: `<svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                           </svg>`,
                    text: 'text-blue-800',
                    bar: 'bg-blue-400',
                },
            };

            const c = configs[type] || configs.error;

            const toast = document.createElement('div');
            toast.className = `pointer-events-auto w-full border-l-4 ${c.bg} rounded-xl shadow-xl overflow-hidden
                               transform transition-all duration-300 translate-x-full opacity-0`;

            toast.innerHTML = `
                <div class="flex items-start gap-3 px-4 py-4">
                    ${c.icon}
                    <p class="text-sm font-medium ${c.text} flex-1 leading-snug">${message}</p>
                    <button onclick="this.closest('.pointer-events-auto').remove()"
                            class="text-gray-400 hover:text-gray-600 transition ml-1 flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="h-1 ${c.bar}" style="animation: shrink 4s linear forwards;"></div>
            `;

            container.appendChild(toast);

            // Slide in
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    toast.classList.remove('translate-x-full', 'opacity-0');
                });
            });

            // Auto-remove after 4s
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // CSS for the shrink progress bar
        if (!document.getElementById('toastStyle')) {
            const style = document.createElement('style');
            style.id = 'toastStyle';
            style.textContent = `@keyframes shrink { from { width: 100%; } to { width: 0%; } }`;
            document.head.appendChild(style);
        }
    </script>

</body>

</html>