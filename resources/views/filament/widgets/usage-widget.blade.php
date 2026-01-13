<x-filament-widgets::widget class="h-full">
    <x-filament::section class="h-full flex flex-col max-w-sm">

        <x-slot name="heading">
            <div class="flex items-center justify-between">
                <span>{{ $this->heading }}</span>
                <div class="shrink-0" wire:click.stop>
                    {{ $this->form }}
                </div>
            </div>
        </x-slot>

        <div wire:click="openDetails" class="cursor-pointer flex flex-col hover:opacity-90 transition">
            @if ($this->errorMessage)
                <p class="text-2xl font-semibold text-center">{{ $errorMessage }}</p>
            @else

                <div class="w-full">
                    <div class="flex flex-row justify-between space-y-10">
                        <p class="text-xl font-semibold">Tokens:</p>
                        <p class="text-[23px] font-semibold">{{ number_format($this->usage['tokens'], 2, ',', '.') }}</p>
                    </div>

                    <div class="flex flex-row justify-between">
                        <p class="text-xl font-semibold">Euro:</p>
                        <p class="text-[23px] font-semibold">€{{ number_format($this->usage['euro'], 2, '.', '') }}</p>
                    </div>
                </div>
            @endif
        </div>

    </x-filament::section>
</x-filament-widgets::widget>
