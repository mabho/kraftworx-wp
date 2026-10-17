@php
    $container = match ($container_type) {
        'full' => 'w-full px-4 sm:px-6 lg:px-8',
        'edge' => 'w-full',
        default => 'mx-auto max-w-7xl px-4 sm:px-6 lg:px-8',
    };

    $grid = match ($columns) {
        '50-50' => 'md:grid-cols-2',
        '33-33-33' => 'md:grid-cols-3',
        default => 'grid-cols-1',
    };

    $background = match ($background_color) {
        'dark' => 'bg-neutral-900 text-white',
        'primary' => 'bg-blue-600 text-white',
        'secondary' => 'bg-neutral-100 text-neutral-900',
        'accent' => 'bg-amber-400 text-neutral-900',
        default => 'bg-white text-neutral-900',
    };
@endphp

<section {{ $attributes->merge(['class' => "section py-12 $background"]) }}>
    <div class="{{ $container }}">
        <div class="grid gap-6 {{ $grid }}">
            <InnerBlocks template="{{ $block->template }}" />
        </div>
    </div>
</section>
