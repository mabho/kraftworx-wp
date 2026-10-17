@php
    $wrapper = match (true) {
        $column_width === 'narrow' => 'mx-auto w-full max-w-4xl px-4 sm:px-6 lg:px-8',
        $contained === 'uncontained' => 'w-full',
        default => 'container mx-auto px-4 sm:px-6 lg:px-8',
    };

    $grid = match ($columns) {
        '50-50' => 'md:grid-cols-2',
        '33-33-33' => 'md:grid-cols-3',
        default => 'grid-cols-1',
    };

    $background = match ($background_color) {
        'primary' => 'bg-primary text-white',
        'secondary' => 'bg-secondary text-white',
        'tertiary' => 'bg-tertiary text-black',
        'black' => 'bg-black text-white',
        default => 'bg-white text-black',
    };
@endphp

<section {{ $attributes->merge(['class' => "section py-12 $background"]) }}>
    <div class="{{ $wrapper }}">
        <div class="grid gap-6 {{ $grid }}">
            <InnerBlocks template="{{ $block->template }}" />
        </div>
    </div>
</section>
