<?php

// phpcs:ignoreFile

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('money', function ($argumentString) {
            [$money, $currency] = $this->getArguments($argumentString);

            return "<?php echo ($money ? new App\Models\Money({$money}, {$currency}) : null); ?>";
        });

        Blade::directive('tax', function ($expression) {
            return "<?php echo $expression * 100 . '%'; ?>";
        });
    }

    /**
     * Get argument array from argument string.
     *
     * @param  string  $argumentString
     * @return array
     */
    private function getArguments($argumentString)
    {
        return explode(', ', str_replace(['(', ')'], '', $argumentString));
    }
}
