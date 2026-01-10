<x-filament-widgets::widget class="h-full">
    <x-filament::section class="h-full flex flex-col max-w-sm">

        <x-slot name="heading">
            <div class="flex items-center justify-between gap-4">
                <span>{{ $this->heading }}</span>
            </div>
        </x-slot>

        <div class="flex-1 flex flex-col justify-center">
            @if ($this->errorMessage)
                <p class="text-2xl font-semibold text-center">{{ $errorMessage }}</p>
            @else

                <div class="w-full flex flex-col gap-10 mt-4">
                    @foreach ($this->storage as $key => $data)

                        <div class="flex items-center justify-between">

                            <div class="flex items-center gap-2">
                                <p class="text-md font-semibold text-gray-400">{{ $key }}:</p>
                                <p class="text-lg font-semibold">{{ $data['count'] }}</p>
                            </div>

                            <div class="flex items-center gap-2 text-right align-baseline">
                                <p class="text-lg text-gray-400">Total:</p>
                                <p class="text-lg font-bold min-w-[95px]">{{ $data['bytes'] }}</p>
                            </div>

                        </div>

                    @endforeach
                </div>

            @endif
        </div>

    </x-filament::section>
</x-filament-widgets::widget>
