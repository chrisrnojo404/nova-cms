@foreach ($blocks as $block)
    @php
        $type = $block['type'] ?? null;
        $data = $block['data'] ?? [];
    @endphp

    @if ($type === 'heading' && ! empty($data['content']))
        @php $level = min(max((int) ($data['level'] ?? 2), 1), 6); @endphp
        @if ($level === 1)
            <h1>{!! $data['content'] !!}</h1>
        @elseif ($level === 2)
            <h2>{!! $data['content'] !!}</h2>
        @elseif ($level === 3)
            <h3>{!! $data['content'] !!}</h3>
        @elseif ($level === 4)
            <h4>{!! $data['content'] !!}</h4>
        @elseif ($level === 5)
            <h5>{!! $data['content'] !!}</h5>
        @else
            <h6>{!! $data['content'] !!}</h6>
        @endif
    @elseif ($type === 'paragraph' && ! empty($data['content']))
        <p>{!! nl2br($data['content']) !!}</p>
    @elseif ($type === 'image' && ! empty($data['url']))
        <figure class="not-prose my-10 overflow-hidden rounded-3xl border border-slate-200 dark:border-slate-800">
            <img src="{{ $data['url'] }}" alt="{{ $data['alt'] ?? '' }}" class="h-auto w-full object-cover">
            @if (! empty($data['caption']))
                <figcaption class="border-t border-slate-200 bg-slate-50 px-5 py-4 text-sm text-slate-500 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-400">{!! $data['caption'] !!}</figcaption>
            @endif
        </figure>
    @elseif ($type === 'button' && ! empty($data['url']) && ! empty($data['text']))
        @php
            $buttonStyles = [
                'primary' => 'bg-cyan-500 text-white hover:bg-cyan-400',
                'secondary' => 'border border-slate-300 text-slate-800 hover:border-cyan-400 hover:text-cyan-700 dark:border-slate-700 dark:text-slate-100 dark:hover:border-cyan-500 dark:hover:text-cyan-300',
                'link' => 'text-cyan-600 hover:text-cyan-500 dark:text-cyan-400 dark:hover:text-cyan-300',
            ];
            $style = $buttonStyles[$data['style'] ?? 'primary'] ?? $buttonStyles['primary'];
        @endphp
        <div class="not-prose my-8">
            <a href="{{ $data['url'] }}" class="inline-flex items-center justify-center rounded-full px-5 py-3 text-sm font-semibold transition {{ $style }}">
                {!! $data['text'] !!}
            </a>
        </div>
    @elseif ($type === 'gallery' && ! empty($data['images']) && is_array($data['images']))
        <div class="not-prose my-10 grid gap-4 sm:grid-cols-2">
            @foreach ($data['images'] as $image)
                @if (! empty($image['url']))
                    <figure class="overflow-hidden rounded-3xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-950">
                        <img src="{{ $image['url'] }}" alt="{{ $image['alt'] ?? '' }}" class="h-64 w-full object-cover">
                        @if (! empty($image['caption']))
                            <figcaption class="px-4 py-3 text-sm text-slate-500 dark:text-slate-400">{!! $image['caption'] !!}</figcaption>
                        @endif
                    </figure>
                @endif
            @endforeach
        </div>
    @elseif ($type === 'video' && ! empty($data['url']))
        <div class="not-prose my-10 overflow-hidden rounded-3xl border border-slate-200 bg-slate-950 dark:border-slate-800">
            <div class="aspect-video">
                @php
                    $videoUrl = $data['url'];
                    $isMediaFile = preg_match('/\.(mp4|webm|ogg)(\?.*)?$/i', $videoUrl) === 1;
                @endphp
                @if ($isMediaFile)
                    <video src="{{ $videoUrl }}" class="h-full w-full object-cover" controls preload="metadata"></video>
                @else
                    <iframe src="{{ $videoUrl }}" title="{{ $data['caption'] ?? 'Embedded video' }}" class="h-full w-full" loading="lazy" allowfullscreen></iframe>
                @endif
            </div>
            @if (! empty($data['caption']))
                <div class="border-t border-slate-800 px-5 py-4 text-sm text-slate-300">{!! $data['caption'] !!}</div>
            @endif
        </div>
    @endif
@endforeach
