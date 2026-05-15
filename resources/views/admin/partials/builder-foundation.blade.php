<div
    x-data="blockEditor({ initialJson: @js(old('builder_blocks', $builderBlocksJson)), catalog: @js($builderCatalog), mediaCatalog: @js($builderMediaLibrary) })"
    x-init="configureAutosave(@js($builderAutosaveKey ?? null))"
    class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900"
>
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <p class="text-sm font-semibold text-slate-900 dark:text-white">Page builder foundation</p>
            <p class="mt-2 max-w-2xl text-sm leading-7 text-slate-500 dark:text-slate-400">
                Shape structured content visually now, while still keeping the JSON contract editable for advanced use. This becomes the stable base for the future drag-and-drop builder.
            </p>
        </div>
        <div class="rounded-2xl border border-cyan-200 bg-cyan-50 px-4 py-3 text-xs font-medium uppercase tracking-[0.24em] text-cyan-700 dark:border-cyan-900/70 dark:bg-cyan-950/40 dark:text-cyan-300">
            Visual block editor enabled
        </div>
    </div>

    <div class="mt-6">
        <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Add blocks</p>
        <div class="mt-3 flex flex-wrap gap-3">
            <template x-for="item in catalog" :key="item.type">
                <button
                    type="button"
                    @click="addBlock(item.type)"
                    class="inline-flex items-center rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-200 dark:hover:border-cyan-500 dark:hover:text-cyan-300"
                >
                    <span x-text="`Add ${item.label}`"></span>
                </button>
            </template>
        </div>
    </div>

    <div class="mt-8">
        <div>
            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Starter layouts</p>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Drop in a starter composition, then fine-tune each block visually.</p>
        </div>

        <div class="mt-4 grid gap-4 lg:grid-cols-3">
            @foreach ($builderLayouts as $layout)
                <button
                    type="button"
                    @click='applyLayout(@js($layout["blocks"]))'
                    class="rounded-3xl border border-slate-200 bg-slate-50 p-4 text-left transition hover:border-cyan-400 hover:bg-cyan-50 dark:border-slate-800 dark:bg-slate-950/60 dark:hover:border-cyan-500 dark:hover:bg-cyan-950/20"
                >
                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $layout['label'] }}</p>
                    <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $layout['description'] }}</p>
                </button>
            @endforeach
        </div>
    </div>

    <div class="mt-8">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Reusable templates</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Apply shared templates managed in the dashboard across pages and posts.</p>
            </div>
            <a href="{{ route('admin.block-templates.index') }}" class="inline-flex items-center rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-200 dark:hover:border-cyan-500 dark:hover:text-cyan-300">
                Manage templates
            </a>
        </div>

        <div class="mt-4 grid gap-4 lg:grid-cols-3">
            @forelse ($builderReusableTemplates as $template)
                <button
                    type="button"
                    @click='applyLayout(@js($template->blocks))'
                    class="rounded-3xl border border-slate-200 bg-slate-50 p-4 text-left transition hover:border-cyan-400 hover:bg-cyan-50 dark:border-slate-800 dark:bg-slate-950/60 dark:hover:border-cyan-500 dark:hover:bg-cyan-950/20"
                >
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $template->name }}</p>
                        <span class="rounded-full border border-slate-200 px-2 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-slate-500 dark:border-slate-700 dark:text-slate-400">{{ $template->scope }}</span>
                    </div>
                    <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $template->description ?: 'Reusable block composition' }}</p>
                </button>
            @empty
                <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-6 py-8 text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-950/60 dark:text-slate-400 lg:col-span-3">
                    No reusable templates yet. Create one in Builder Templates to reuse a polished composition across multiple pages or posts.
                </div>
            @endforelse
        </div>
    </div>

    <div class="mt-8 rounded-3xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-950/60">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-slate-900 dark:text-white">Builder snapshots</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Keep a browser-side autosave of your in-progress block JSON while editing.</p>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400" x-text="autosaveLabel"></p>
        </div>
        <div class="mt-4 flex flex-wrap gap-3">
            <button type="button" @click="restoreAutosaveSnapshot()" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-200 dark:hover:border-cyan-500 dark:hover:text-cyan-300">Restore builder snapshot</button>
            <button type="button" @click="clearAutosaveSnapshot()" class="rounded-full border border-rose-300 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-50 dark:border-rose-900 dark:text-rose-300 dark:hover:bg-rose-950/40">Clear builder snapshot</button>
        </div>
    </div>

    <div class="mt-6 space-y-4">
        <template x-if="blocks.length === 0">
            <div class="rounded-3xl border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center text-sm leading-7 text-slate-500 dark:border-slate-700 dark:bg-slate-950/60 dark:text-slate-400">
                No visual blocks added yet. Add one above, or leave the builder empty to let Nova generate starter blocks from the classic content fields.
            </div>
        </template>

        <template x-for="(block, index) in blocks" :key="block.id || `${block.type}-${index}`">
            <section
                draggable="true"
                @dragstart="dragStart(index)"
                @dragover="dragOver($event)"
                @drop="dropOn(index)"
                @dragend="dragEnd()"
                :class="{ 'ring-2 ring-cyan-400 ring-offset-2 dark:ring-offset-slate-900': draggingIndex === index }"
                class="rounded-3xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-950/60"
            >
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-cyan-600 dark:text-cyan-400" x-text="block.type"></p>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Block <span x-text="index + 1"></span> • drag to reorder</p>
                        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300" x-text="blockPreview(block)"></p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button type="button" @click="toggleCollapse(block)" class="rounded-full border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-200" x-text="block.collapsed ? 'Expand' : 'Collapse'"></button>
                        <button type="button" @click="duplicateBlock(index)" class="rounded-full border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-200">Duplicate</button>
                        <button type="button" @click="moveBlock(index, -1)" class="rounded-full border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-200">Move up</button>
                        <button type="button" @click="moveBlock(index, 1)" class="rounded-full border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-200">Move down</button>
                        <button type="button" @click="removeBlock(index)" class="rounded-full border border-rose-300 px-3 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-50 dark:border-rose-900 dark:text-rose-300 dark:hover:bg-rose-950/40">Remove</button>
                    </div>
                </div>

                <div class="mt-5 grid gap-4" x-show="!block.collapsed">
                    <template x-if="block.type === 'heading'">
                        <div class="grid gap-4 sm:grid-cols-[1fr_140px]">
                            <div>
                                <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Content</label>
                                <input type="text" x-model="block.data.content" @input="syncRawJson()" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Level</label>
                                <select x-model="block.data.level" @change="syncRawJson()" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                    <template x-for="level in [1,2,3,4,5,6]" :key="level">
                                        <option :value="level" x-text="`H${level}`"></option>
                                    </template>
                                </select>
                            </div>
                        </div>
                    </template>

                    <template x-if="block.type === 'paragraph'">
                        <div>
                            <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Content</label>
                            <textarea x-model="block.data.content" @input="syncRawJson()" rows="5" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-900 dark:text-white"></textarea>
                        </div>
                    </template>

                    <template x-if="block.type === 'image'">
                        <div class="grid gap-4">
                            <div>
                                <div class="flex items-center justify-between gap-3">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Image URL</label>
                                    <button type="button" @click="openMediaPicker(index, null, 'image')" class="rounded-full border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-200">Pick from media</button>
                                </div>
                                <input type="text" x-model="block.data.url" @input="syncRawJson()" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Alt text</label>
                                    <input type="text" x-model="block.data.alt" @input="syncRawJson()" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Caption</label>
                                    <input type="text" x-model="block.data.caption" @input="syncRawJson()" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                </div>
                            </div>
                        </div>
                    </template>

                    <template x-if="block.type === 'button'">
                        <div class="grid gap-4">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Button text</label>
                                    <input type="text" x-model="block.data.text" @input="syncRawJson()" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Button URL</label>
                                    <input type="text" x-model="block.data.url" @input="syncRawJson()" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                </div>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Style</label>
                                <select x-model="block.data.style" @change="syncRawJson()" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                                    <option value="primary">Primary</option>
                                    <option value="secondary">Secondary</option>
                                    <option value="link">Link</option>
                                </select>
                            </div>
                        </div>
                    </template>

                    <template x-if="block.type === 'gallery'">
                        <div>
                            <div class="flex items-center justify-between gap-4">
                                <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Gallery images</label>
                                <button type="button" @click="addGalleryImage(block)" class="rounded-full border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-200">Add image</button>
                            </div>
                            <div class="mt-4 space-y-4">
                                <template x-for="(image, imageIndex) in (block.data.images || [])" :key="`${index}-${imageIndex}`">
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900">
                                        <div class="flex items-center justify-between gap-3">
                                            <p class="text-sm font-semibold text-slate-900 dark:text-white">Image <span x-text="imageIndex + 1"></span></p>
                                            <div class="flex items-center gap-3">
                                                <button type="button" @click="openMediaPicker(index, imageIndex, 'image')" class="text-xs font-semibold text-cyan-600 transition hover:text-cyan-500 dark:text-cyan-300 dark:hover:text-cyan-200">Pick media</button>
                                                <button type="button" @click="removeGalleryImage(block, imageIndex)" class="text-xs font-semibold text-rose-500 transition hover:text-rose-400">Remove</button>
                                            </div>
                                        </div>
                                        <div class="mt-4 grid gap-4">
                                            <input type="text" x-model="image.url" @input="syncRawJson()" placeholder="Image URL" class="w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                            <input type="text" x-model="image.alt" @input="syncRawJson()" placeholder="Alt text" class="w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                            <input type="text" x-model="image.caption" @input="syncRawJson()" placeholder="Caption" class="w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-950 dark:text-white">
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <template x-if="block.type === 'video'">
                        <div class="grid gap-4">
                            <div>
                                <div class="flex items-center justify-between gap-3">
                                    <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Video URL or embed</label>
                                    <button type="button" @click="openMediaPicker(index, null, 'video')" class="rounded-full border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-200">Pick video asset</button>
                                </div>
                                <input type="text" x-model="block.data.url" @input="syncRawJson()" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-slate-700 dark:text-slate-200">Caption</label>
                                <input type="text" x-model="block.data.caption" @input="syncRawJson()" class="mt-2 w-full rounded-2xl border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700 dark:bg-slate-900 dark:text-white">
                            </div>
                        </div>
                    </template>
                </div>
            </section>
        </template>
    </div>

    <div class="mt-8 rounded-3xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-950/60">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <label for="builder_blocks" class="text-sm font-semibold text-slate-700 dark:text-slate-200">Advanced JSON editor</label>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Use this for direct editing, pasting exported blocks, or future external builder tooling.</p>
            </div>
            <button
                type="button"
                @click="loadFromJson()"
                class="inline-flex items-center rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-200 dark:hover:border-cyan-500 dark:hover:text-cyan-300"
            >
                Load JSON Into Editor
            </button>
        </div>

        <textarea id="builder_blocks" name="builder_blocks" x-model="rawJson" rows="18" class="mt-4 w-full rounded-3xl border-slate-300 bg-slate-950 px-4 py-4 font-mono text-sm leading-7 text-cyan-100 shadow-sm focus:border-cyan-400 focus:ring-cyan-400 dark:border-slate-700"></textarea>
        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
            Leave this empty to let Nova generate starter blocks from the classic title, excerpt, and content fields.
        </p>
        <p x-show="importError" x-text="importError" class="mt-2 text-sm text-rose-500"></p>
        @error('builder_blocks') <p class="mt-2 text-sm text-rose-500">{{ $message }}</p> @enderror
    </div>

    <div x-show="pickerTarget" x-cloak class="mt-8 rounded-3xl border border-cyan-200 bg-cyan-50/70 p-5 dark:border-cyan-900/60 dark:bg-cyan-950/20">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-slate-900 dark:text-white">Media library picker</p>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Select an uploaded image to populate the current builder block.</p>
            </div>
            <button type="button" @click="closeMediaPicker()" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-200">Close picker</button>
        </div>

        <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <template x-for="asset in filteredMediaCatalog()" :key="asset.id">
                <button
                    type="button"
                    @click="applyMediaAsset(asset)"
                    class="overflow-hidden rounded-3xl border border-slate-200 bg-white text-left transition hover:border-cyan-400 hover:shadow-md dark:border-slate-800 dark:bg-slate-900"
                >
                    <div class="aspect-[4/3] overflow-hidden bg-slate-100 dark:bg-slate-950">
                        <img :src="asset.url" :alt="asset.alt || asset.name" class="h-full w-full object-cover">
                    </div>
                    <div class="p-4">
                        <p class="truncate text-sm font-semibold text-slate-900 dark:text-white" x-text="asset.name"></p>
                        <p class="mt-1 truncate text-sm text-slate-500 dark:text-slate-400" x-text="asset.alt || asset.mime_type || 'No metadata yet'"></p>
                    </div>
                </button>
            </template>
        </div>

        <template x-if="filteredMediaCatalog().length === 0">
            <div class="mt-4 rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-8 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400">
                No matching media assets are available yet. Upload some in the media library first, then return here to pick them visually.
            </div>
        </template>
    </div>
</div>
