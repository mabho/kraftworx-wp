<section {{ $attributes->merge(['class' => 'editorial-content relative overflow-hidden bg-black py-15 lg:py-20 xl:py-25 2xl:py-30 text-white']) }}>
  <div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <InnerBlocks template="{{ $block->template }}" templateLock="all" />
  </div>
</section>
