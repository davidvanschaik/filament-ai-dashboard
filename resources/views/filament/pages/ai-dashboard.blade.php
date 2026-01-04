<div class="space-y-6 pt-8">
    <div class="grid grid-cols-2 gap-6 justify-items-center">
        <div class="w-full h-full max-w-sm justify-self-end">
            @livewire(\DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\ModelsWidget::class)
        </div>

        <div class="w-full h-full max-w-sm justify-self-start">
            @livewire(\DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\UsageWidget::class)
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6 justify-items-center">
        <div class="w-full h-full max-w-sm justify-self-end">
            @livewire(\DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\StorageWidget::class)
        </div>

        <div class="w-full h-full max-w-sm justify-self-start">
            @livewire(\DavidvanSchaik\FilamentAiDashboard\Filament\Widgets\JobsWidget::class)
        </div>
    </div>
</div>
