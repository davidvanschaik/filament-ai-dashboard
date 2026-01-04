<x-filament-widgets::widget class="h-full">
    <x-filament::section class="h-full flex flex-col max-w-sm">

        <x-slot name="heading">
            <div class="flex items-center justify-between gap-4">
                <span>{{ $this->heading }}</span>
                <div class="shrink-0" wire:click.stop>
                    {{ $this->form }}
                </div>
            </div>
        </x-slot>

        <div wire:click="openDetails" class="cursor-pointer flex flex-col justify-start hover:opacity-90 transition">
            @if ($this->errorMessage)
                <p class="text-2xl font-semibold text-center">{{ $errorMessage }}</p>
            @else
                <ul class="space-y-4 w-full">
                    @foreach ($this->models as $model => $count)
                        <li class="flex items-center justify-between">
                            <div class="font-semibold text-xl">
                                <p>{{ $model }}</p>
                            </div>

                            <div class="flex flex-row items-center gap-x-4">
                                <p class="text-gray-400 text-lg">Requests</p>
                                <p class="font-bold text-xl text-right min-w-[60px]">{{ $count }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

    </x-filament::section>
</x-filament-widgets::widget>
