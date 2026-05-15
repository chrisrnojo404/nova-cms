<?php

namespace App\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use JsonException;

class BlockBuilder
{
    public function availableBlocks(): array
    {
        return [
            [
                'type' => 'heading',
                'label' => 'Heading',
                'schema' => ['content', 'level'],
                'example' => ['type' => 'heading', 'data' => ['content' => 'Section title', 'level' => 2]],
            ],
            [
                'type' => 'paragraph',
                'label' => 'Paragraph',
                'schema' => ['content'],
                'example' => ['type' => 'paragraph', 'data' => ['content' => 'A supporting paragraph for the section.']],
            ],
            [
                'type' => 'image',
                'label' => 'Image',
                'schema' => ['url', 'alt', 'caption'],
                'example' => ['type' => 'image', 'data' => ['url' => '/storage/media/example.webp', 'alt' => 'Example visual', 'caption' => 'Optional caption']],
            ],
            [
                'type' => 'button',
                'label' => 'Button',
                'schema' => ['text', 'url', 'style'],
                'example' => ['type' => 'button', 'data' => ['text' => 'Get started', 'url' => '/contact', 'style' => 'primary']],
            ],
            [
                'type' => 'gallery',
                'label' => 'Gallery',
                'schema' => ['images[]'],
                'example' => ['type' => 'gallery', 'data' => ['images' => [['url' => '/storage/media/one.webp', 'alt' => 'Gallery item one'], ['url' => '/storage/media/two.webp', 'alt' => 'Gallery item two']]]],
            ],
            [
                'type' => 'video',
                'label' => 'Video',
                'schema' => ['url', 'caption'],
                'example' => ['type' => 'video', 'data' => ['url' => 'https://www.youtube.com/embed/example', 'caption' => 'Product walkthrough']],
            ],
        ];
    }

    public function starterLayouts(): array
    {
        return [
            [
                'key' => 'hero_cta',
                'label' => 'Hero + CTA',
                'description' => 'A landing-section opener with headline, paragraph, and a primary button.',
                'blocks' => [
                    ['type' => 'heading', 'data' => ['content' => 'Launch your next content experience', 'level' => 1]],
                    ['type' => 'paragraph', 'data' => ['content' => 'Introduce the value of this page with a concise supporting paragraph that guides readers toward a call to action.']],
                    ['type' => 'button', 'data' => ['text' => 'Explore more', 'url' => '/contact', 'style' => 'primary']],
                ],
            ],
            [
                'key' => 'story_gallery',
                'label' => 'Story + Gallery',
                'description' => 'A narrative content section followed by a visual gallery.',
                'blocks' => [
                    ['type' => 'heading', 'data' => ['content' => 'Tell the story behind the work', 'level' => 2]],
                    ['type' => 'paragraph', 'data' => ['content' => 'Use this layout for editorial case studies, campaign recaps, or product storytelling that benefits from both narrative and visuals.']],
                    ['type' => 'gallery', 'data' => ['images' => [
                        ['url' => '/storage/media/story-1.webp', 'alt' => 'Story image one', 'caption' => 'Highlight image one'],
                        ['url' => '/storage/media/story-2.webp', 'alt' => 'Story image two', 'caption' => 'Highlight image two'],
                    ]]],
                ],
            ],
            [
                'key' => 'feature_stack',
                'label' => 'Feature Stack',
                'description' => 'An explainer layout with multiple headings and a supporting embedded video.',
                'blocks' => [
                    ['type' => 'heading', 'data' => ['content' => 'Why teams choose Nova CMS', 'level' => 2]],
                    ['type' => 'paragraph', 'data' => ['content' => 'Break the page into digestible product or feature value points using repeating heading and paragraph blocks.']],
                    ['type' => 'heading', 'data' => ['content' => 'Built for modular growth', 'level' => 3]],
                    ['type' => 'paragraph', 'data' => ['content' => 'Themes, plugins, APIs, and builder content all share the same CMS foundation so the platform can scale cleanly.']],
                    ['type' => 'video', 'data' => ['url' => 'https://www.youtube.com/embed/example', 'caption' => 'Optional product walkthrough or presentation embed']],
                ],
            ],
        ];
    }

    public function editorJson(?array $blocks, array $fallback): string
    {
        $payload = empty($blocks) ? $fallback : $blocks;

        try {
            return json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return '[]';
        }
    }

    public function decode(?string $json): array
    {
        $json = trim((string) $json);

        if ($json === '') {
            return [];
        }

        try {
            $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return [];
        }

        return is_array($decoded) ? $decoded : [];
    }

    public function normalize(array $blocks): array
    {
        $normalized = [];

        foreach ($blocks as $block) {
            if (! is_array($block)) {
                continue;
            }

            $type = (string) Arr::get($block, 'type', '');
            $data = Arr::get($block, 'data', []);

            if (! is_array($data)) {
                $data = [];
            }

            $normalizedBlock = match ($type) {
                'heading' => $this->normalizeHeading($data),
                'paragraph' => $this->normalizeParagraph($data),
                'image' => $this->normalizeImage($data),
                'button' => $this->normalizeButton($data),
                'gallery' => $this->normalizeGallery($data),
                'video' => $this->normalizeVideo($data),
                default => null,
            };

            if (! $normalizedBlock) {
                continue;
            }

            $normalized[] = [
                'id' => (string) Arr::get($block, 'id', Str::uuid()),
                'type' => $type,
                'data' => $normalizedBlock,
            ];
        }

        return $normalized;
    }

    public function validationErrors(array $blocks): array
    {
        $errors = [];

        foreach ($blocks as $index => $block) {
            if (! is_array($block)) {
                $errors[] = "Block #".($index + 1)." must be an object.";
                continue;
            }

            $type = (string) Arr::get($block, 'type', '');
            $data = Arr::get($block, 'data', []);

            if ($type === '' || ! in_array($type, collect($this->availableBlocks())->pluck('type')->all(), true)) {
                $errors[] = "Block #".($index + 1)." has an unsupported type.";
                continue;
            }

            if (! is_array($data)) {
                $errors[] = "Block #".($index + 1)." data must be an object.";
                continue;
            }

            $blockErrors = match ($type) {
                'heading' => $this->validateHeading($data),
                'paragraph' => $this->validateParagraph($data),
                'image' => $this->validateImage($data),
                'button' => $this->validateButton($data),
                'gallery' => $this->validateGallery($data),
                'video' => $this->validateVideo($data),
                default => ['Unsupported block type.'],
            };

            foreach ($blockErrors as $error) {
                $errors[] = "Block #".($index + 1).": {$error}";
            }
        }

        return $errors;
    }

    public function fallbackPageBlocks(string $title, ?string $content): array
    {
        return $this->normalize(array_values(array_filter([
            ['type' => 'heading', 'data' => ['content' => $title, 'level' => 1]],
            filled($content) ? ['type' => 'paragraph', 'data' => ['content' => strip_tags((string) $content)]] : null,
        ])));
    }

    public function fallbackPostBlocks(string $title, ?string $excerpt, ?string $content): array
    {
        return $this->normalize(array_values(array_filter([
            ['type' => 'heading', 'data' => ['content' => $title, 'level' => 1]],
            filled($excerpt) ? ['type' => 'paragraph', 'data' => ['content' => (string) $excerpt]] : null,
            filled($content) ? ['type' => 'paragraph', 'data' => ['content' => strip_tags((string) $content)]] : null,
        ])));
    }

    public function renderReady(array $blocks, callable $textRenderer): array
    {
        return array_map(function (array $block) use ($textRenderer): array {
            $data = $block['data'] ?? [];

            foreach (['content', 'text', 'caption'] as $key) {
                if (isset($data[$key]) && is_string($data[$key]) && $data[$key] !== '') {
                    $data[$key] = $textRenderer($data[$key]);
                }
            }

            if (($block['type'] ?? null) === 'gallery' && isset($data['images']) && is_array($data['images'])) {
                $data['images'] = array_map(function ($image) use ($textRenderer) {
                    if (! is_array($image)) {
                        return $image;
                    }

                    if (isset($image['caption']) && is_string($image['caption']) && $image['caption'] !== '') {
                        $image['caption'] = $textRenderer($image['caption']);
                    }

                    return $image;
                }, $data['images']);
            }

            $block['data'] = $data;

            return $block;
        }, $blocks);
    }

    public function stripLeadingMetadataBlocks(array $blocks, ?string $headline = null, ?string $lede = null): array
    {
        $blocks = array_values($blocks);

        if ($headline !== null && isset($blocks[0]) && ($blocks[0]['type'] ?? null) === 'heading') {
            $content = trim((string) Arr::get($blocks[0], 'data.content', ''));

            if ($content !== '' && $content === trim($headline)) {
                array_shift($blocks);
            }
        }

        if ($lede !== null && isset($blocks[0]) && ($blocks[0]['type'] ?? null) === 'paragraph') {
            $content = trim((string) Arr::get($blocks[0], 'data.content', ''));

            if ($content !== '' && $content === trim($lede)) {
                array_shift($blocks);
            }
        }

        return array_values($blocks);
    }

    private function normalizeHeading(array $data): ?array
    {
        $content = trim((string) Arr::get($data, 'content', ''));

        if ($content === '') {
            return null;
        }

        return [
            'content' => $content,
            'level' => min(max((int) Arr::get($data, 'level', 2), 1), 6),
        ];
    }

    private function normalizeParagraph(array $data): ?array
    {
        $content = trim((string) Arr::get($data, 'content', ''));

        return $content === '' ? null : ['content' => $content];
    }

    private function normalizeImage(array $data): ?array
    {
        $url = trim((string) Arr::get($data, 'url', ''));

        if ($url === '') {
            return null;
        }

        return [
            'url' => $url,
            'alt' => trim((string) Arr::get($data, 'alt', '')),
            'caption' => trim((string) Arr::get($data, 'caption', '')),
        ];
    }

    private function normalizeButton(array $data): ?array
    {
        $text = trim((string) Arr::get($data, 'text', ''));
        $url = trim((string) Arr::get($data, 'url', ''));

        if ($text === '' || $url === '') {
            return null;
        }

        $style = (string) Arr::get($data, 'style', 'primary');

        return [
            'text' => $text,
            'url' => $url,
            'style' => in_array($style, ['primary', 'secondary', 'link'], true) ? $style : 'primary',
        ];
    }

    private function normalizeGallery(array $data): ?array
    {
        $images = array_values(array_filter(array_map(function ($image) {
            if (! is_array($image)) {
                return null;
            }

            $url = trim((string) Arr::get($image, 'url', ''));

            if ($url === '') {
                return null;
            }

            return [
                'url' => $url,
                'alt' => trim((string) Arr::get($image, 'alt', '')),
                'caption' => trim((string) Arr::get($image, 'caption', '')),
            ];
        }, Arr::get($data, 'images', []))));

        return $images === [] ? null : ['images' => $images];
    }

    private function normalizeVideo(array $data): ?array
    {
        $url = trim((string) Arr::get($data, 'url', ''));

        if ($url === '') {
            return null;
        }

        return [
            'url' => $url,
            'caption' => trim((string) Arr::get($data, 'caption', '')),
        ];
    }

    private function validateHeading(array $data): array
    {
        $errors = [];

        if (trim((string) Arr::get($data, 'content', '')) === '') {
            $errors[] = 'Heading blocks require content.';
        }

        $level = (int) Arr::get($data, 'level', 2);

        if ($level < 1 || $level > 6) {
            $errors[] = 'Heading level must be between 1 and 6.';
        }

        return $errors;
    }

    private function validateParagraph(array $data): array
    {
        return trim((string) Arr::get($data, 'content', '')) === ''
            ? ['Paragraph blocks require content.']
            : [];
    }

    private function validateImage(array $data): array
    {
        return trim((string) Arr::get($data, 'url', '')) === ''
            ? ['Image blocks require a URL.']
            : [];
    }

    private function validateButton(array $data): array
    {
        $errors = [];

        if (trim((string) Arr::get($data, 'text', '')) === '') {
            $errors[] = 'Button blocks require button text.';
        }

        if (trim((string) Arr::get($data, 'url', '')) === '') {
            $errors[] = 'Button blocks require a URL.';
        }

        return $errors;
    }

    private function validateGallery(array $data): array
    {
        $images = Arr::get($data, 'images', []);

        if (! is_array($images) || $images === []) {
            return ['Gallery blocks require at least one image.'];
        }

        foreach ($images as $image) {
            if (! is_array($image) || trim((string) Arr::get($image, 'url', '')) === '') {
                return ['Each gallery image must include a URL.'];
            }
        }

        return [];
    }

    private function validateVideo(array $data): array
    {
        return trim((string) Arr::get($data, 'url', '')) === ''
            ? ['Video blocks require a URL.']
            : [];
    }
}
