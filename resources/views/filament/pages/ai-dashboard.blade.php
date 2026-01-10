@php
    $widgets = array_values($this->getHeaderWidgets());
@endphp

<div class="space-y-6 pt-14">
    @foreach (array_chunk($widgets, 2) as $index => $widget)
        <div class="grid grid-cols-2 gap-6 justify-items-center">

            <div class="w-full h-full max-w-sm justify-self-end">
                @if (! empty($widget[0]))
                    @livewire($widget[0], [], key("aiw-{$index}-0-" . md5($widget[0])))
                @endif
            </div>

            <div class="w-full h-full max-w-sm justify-self-start">
                @if (! empty($widget[1]))
                    @livewire($widget[1], [], key("aiw-{$index}-0-" . md5($widget[1])))
                @endif
            </div>

        </div>
    @endforeach
</div>
