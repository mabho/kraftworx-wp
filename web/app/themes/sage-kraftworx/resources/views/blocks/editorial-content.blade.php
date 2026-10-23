<section {{ $attributes->merge(['class' => 'editorial-content relative overflow-hidden bg-black py-15 lg:py-20 xl:py-25 2xl:py-30 text-white']) }}>
  <div class="container mx-auto px-4 sm:px-6 lg:px-8">
    @if ($title)
      <h2 class="editorial-content__title mb-8 lg:mb-15 font-display font-normal text-[clamp(2.75rem,10vw,var(--text-hero))] leading-[0.875] tracking-tight">
        {!! nl2br(e($title)) !!}
      </h2>
    @endif

    <InnerBlocks template="{{ $block->template }}" templateLock="all" />
  </div>
</section>
