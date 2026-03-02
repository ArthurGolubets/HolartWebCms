@props(['settings' => [], 'childBlocks' => []])

@php
    $gap = $settings['gap'] ?? 'medium';
    $gapClass = match($gap) {
        'small' => 'g-3',
        'medium' => 'g-4',
        'large' => 'g-5',
        default => 'g-4',
    };

    $col1Blocks = $childBlocks->where('container_column', 'col1');
    $col2Blocks = $childBlocks->where('container_column', 'col2');
@endphp

<section class="container-50-50 py-5">
    <div class="container">
        <div class="row {{ $gapClass }}">
            <!-- Column 1 -->
            <div class="col-md-6 container-column">
                @foreach($col1Blocks as $block)
                    @include('components.blocks.' . str_replace('_', '-', $block->blockType->code), ['settings' => $block->settings])
                @endforeach
            </div>

            <!-- Column 2 -->
            <div class="col-md-6 container-column">
                @foreach($col2Blocks as $block)
                    @include('components.blocks.' . str_replace('_', '-', $block->blockType->code), ['settings' => $block->settings])
                @endforeach
            </div>
        </div>
    </div>
</section>
