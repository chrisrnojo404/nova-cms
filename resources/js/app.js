import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.data('blockEditor', ({ initialJson = '[]', catalog = [], mediaCatalog = [] } = {}) => ({
        catalog,
        mediaCatalog,
        blocks: [],
        rawJson: initialJson,
        importError: '',
        draggingIndex: null,
        pickerTarget: null,
        autosaveKey: null,
        autosaveLabel: 'Idle',

        init() {
            this.loadFromJson(this.rawJson, false);
        },

        configureAutosave(key) {
            this.autosaveKey = key;
            this.autosaveLabel = localStorage.getItem(key) ? 'Snapshot available' : 'Idle';
        },

        addBlock(type) {
            const template = this.catalog.find((item) => item.type === type)?.example;

            if (!template) {
                return;
            }

            this.blocks.push(JSON.parse(JSON.stringify(template)));
            this.syncRawJson();
        },

        duplicateBlock(index) {
            const block = this.blocks[index];

            if (!block) {
                return;
            }

            const clone = JSON.parse(JSON.stringify(block));
            clone.id = `${block.id || block.type}-${Date.now()}`;
            this.blocks.splice(index + 1, 0, clone);
            this.syncRawJson();
        },

        removeBlock(index) {
            this.blocks.splice(index, 1);
            this.syncRawJson();
        },

        applyLayout(layout) {
            if (!Array.isArray(layout)) {
                return;
            }

            this.blocks = JSON.parse(JSON.stringify(layout));
            this.syncRawJson();
        },

        moveBlock(index, direction) {
            const nextIndex = index + direction;

            if (nextIndex < 0 || nextIndex >= this.blocks.length) {
                return;
            }

            const [block] = this.blocks.splice(index, 1);
            this.blocks.splice(nextIndex, 0, block);
            this.syncRawJson();
        },

        dragStart(index) {
            this.draggingIndex = index;
        },

        dragOver(event) {
            event.preventDefault();
        },

        dropOn(index) {
            if (this.draggingIndex === null || this.draggingIndex === index) {
                this.draggingIndex = null;
                return;
            }

            const [block] = this.blocks.splice(this.draggingIndex, 1);
            this.blocks.splice(index, 0, block);
            this.draggingIndex = null;
            this.syncRawJson();
        },

        dragEnd() {
            this.draggingIndex = null;
        },

        toggleCollapse(block) {
            block.collapsed = !block.collapsed;
            this.syncRawJson();
        },

        openMediaPicker(blockIndex, imageIndex = null, mode = 'image') {
            this.pickerTarget = {
                blockIndex,
                imageIndex,
                mode,
            };
        },

        closeMediaPicker() {
            this.pickerTarget = null;
        },

        applyMediaAsset(asset) {
            if (!this.pickerTarget) {
                return;
            }

            const block = this.blocks[this.pickerTarget.blockIndex];

            if (!block || !block.data) {
                this.closeMediaPicker();
                return;
            }

            if (block.type === 'image') {
                block.data.url = asset.url;
                block.data.alt = block.data.alt || asset.alt || asset.name || '';
            }

            if (block.type === 'video') {
                block.data.url = asset.url;
                block.data.caption = block.data.caption || asset.name || '';
            }

            if (block.type === 'gallery') {
                if (!Array.isArray(block.data.images)) {
                    block.data.images = [];
                }

                const targetImage = block.data.images[this.pickerTarget.imageIndex];

                if (targetImage) {
                    targetImage.url = asset.url;
                    targetImage.alt = targetImage.alt || asset.alt || asset.name || '';
                }
            }

            this.syncRawJson();
            this.closeMediaPicker();
        },

        addGalleryImage(block) {
            if (!Array.isArray(block.data.images)) {
                block.data.images = [];
            }

            block.data.images.push({
                url: '',
                alt: '',
                caption: '',
            });

            this.syncRawJson();
        },

        removeGalleryImage(block, imageIndex) {
            if (!Array.isArray(block.data.images)) {
                return;
            }

            block.data.images.splice(imageIndex, 1);
            this.syncRawJson();
        },

        syncRawJson() {
            this.importError = '';
            this.rawJson = JSON.stringify(this.blocks, null, 2);
            this.saveAutosaveSnapshot();
        },

        loadFromJson(json = this.rawJson, shouldReplaceTextarea = true) {
            try {
                const parsed = JSON.parse(json || '[]');

                if (!Array.isArray(parsed)) {
                    throw new Error('Builder JSON must be an array.');
                }

                this.blocks = parsed;
                this.importError = '';

                if (shouldReplaceTextarea) {
                    this.syncRawJson();
                }
            } catch (error) {
                this.importError = error.message || 'Unable to parse builder JSON.';
            }
        },

        filteredMediaCatalog() {
            if (!this.pickerTarget) {
                return [];
            }

            if (this.pickerTarget.mode === 'video') {
                return this.mediaCatalog.filter((asset) => (asset.mime_type || '').startsWith('video/'));
            }

            return this.mediaCatalog.filter((asset) => (asset.mime_type || '').startsWith('image/'));
        },

        blockPreview(block) {
            if (!block || !block.data) {
                return '';
            }

            if (block.type === 'heading') {
                return block.data.content || 'Heading block';
            }

            if (block.type === 'paragraph') {
                return block.data.content || 'Paragraph block';
            }

            if (block.type === 'button') {
                return `${block.data.text || 'Button'} -> ${block.data.url || 'URL missing'}`;
            }

            if (block.type === 'image') {
                return block.data.url || 'Image URL missing';
            }

            if (block.type === 'gallery') {
                return `${(block.data.images || []).length} gallery image(s)`;
            }

            if (block.type === 'video') {
                return block.data.url || 'Video URL missing';
            }

            return 'Block preview unavailable';
        },

        saveAutosaveSnapshot() {
            if (!this.autosaveKey) {
                return;
            }

            try {
                localStorage.setItem(this.autosaveKey, this.rawJson);
                this.autosaveLabel = `Autosaved ${new Date().toLocaleTimeString()}`;
            } catch (_error) {
                this.autosaveLabel = 'Autosave unavailable';
            }
        },

        restoreAutosaveSnapshot() {
            if (!this.autosaveKey) {
                return;
            }

            const snapshot = localStorage.getItem(this.autosaveKey);

            if (!snapshot) {
                this.autosaveLabel = 'No snapshot found';
                return;
            }

            this.rawJson = snapshot;
            this.loadFromJson(snapshot, false);
            this.autosaveLabel = 'Snapshot restored';
        },

        clearAutosaveSnapshot() {
            if (!this.autosaveKey) {
                return;
            }

            localStorage.removeItem(this.autosaveKey);
            this.autosaveLabel = 'Snapshot cleared';
        },
    }));

    Alpine.data('draftSnapshot', ({ key = '', fields = [] } = {}) => ({
        key,
        fields,
        status: 'Idle',

        init() {
            this.status = localStorage.getItem(this.key) ? 'Draft snapshot available' : 'Idle';

            this.$nextTick(() => {
                this.fields.forEach((field) => {
                    const input = this.$root.querySelector(`[name="${field}"]`);

                    if (!input) {
                        return;
                    }

                    input.addEventListener('input', () => this.save());
                    input.addEventListener('change', () => this.save());
                });
            });
        },

        save() {
            if (!this.key) {
                return;
            }

            const payload = {};

            this.fields.forEach((field) => {
                const input = this.$root.querySelector(`[name="${field}"]`);

                if (!input) {
                    return;
                }

                if (input.type === 'checkbox') {
                    payload[field] = input.checked;
                    return;
                }

                payload[field] = input.value;
            });

            localStorage.setItem(this.key, JSON.stringify(payload));
            this.status = `Draft autosaved ${new Date().toLocaleTimeString()}`;
        },

        restore() {
            if (!this.key) {
                return;
            }

            const raw = localStorage.getItem(this.key);

            if (!raw) {
                this.status = 'No draft snapshot found';
                return;
            }

            const payload = JSON.parse(raw);

            this.fields.forEach((field) => {
                const input = this.$root.querySelector(`[name="${field}"]`);

                if (!input || !(field in payload)) {
                    return;
                }

                if (input.type === 'checkbox') {
                    input.checked = !!payload[field];
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                    return;
                }

                input.value = payload[field] ?? '';
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.dispatchEvent(new Event('change', { bubbles: true }));
            });

            this.status = 'Draft snapshot restored';
        },

        clear() {
            if (!this.key) {
                return;
            }

            localStorage.removeItem(this.key);
            this.status = 'Draft snapshot cleared';
        },
    }));
});

Alpine.start();
