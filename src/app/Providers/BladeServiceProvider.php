<?php

// phpcs:ignoreFile

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    #[\Override]
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Blade::directive('money', function ($argumentString): string {
            [$money, $currency] = $this->getArguments($argumentString);

            return "<?php echo ($money ? new App\Models\Money({$money}, {$currency}) : null); ?>";
        });

        Blade::directive('tax', fn ($expression): string => "<?php echo $expression * 100 . '%'; ?>");
    }

    /**
     * Get argument array from argument string.
     *
     * @param  string  $argumentString
     */
    private function getArguments($argumentString): array
    {
        return explode(', ', str_replace(['(', ')'], '', $argumentString));
    }
}
