@props(['page'])

@php
    use App\Models\TPage;
    use App\Models\TPageBlock;

    // Load page if slug is provided
    if (is_string($page)) {
        $page = TPage::where('slug', $page)->where('is_active', true)->firstOrFail();
    }

    // Load blocks with relationships
    $blocks = TPageBlock::where('page_id', $page->id)
        ->whereNull('parent_block_id') // Only root blocks
        ->where('is_active', true)
        ->with(['blockType', 'childBlocks.blockType'])
        ->orderBy('sort')
        ->get();
@endphp

<div class="page-content">
    @if($page->type === 'static')
        <!-- Static page content -->
        <div class="container mx-auto px-4 py-8">
            {!! $page->content !!}
        </div>
    @else
        <!-- Dynamic page blocks -->
        @foreach($blocks as $block)
            @php
                $blockCode = str_replace('_', '-', $block->blockType->code);
                $componentPath = 'components.blocks.' . $blockCode;
            @endphp

            @if($block->blockType->is_container)
                @include($componentPath, [
                    'settings' => $block->settings,
                    'childBlocks' => $block->childBlocks
                ])
            @else
                @include($componentPath, ['settings' => $block->settings])
            @endif
        @endforeach
    @endif
</div>
