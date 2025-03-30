<?php

namespace JeromeSiau\FilamentMultilang;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Filesystem\Filesystem;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use JeromeSiau\FilamentMultilang\Components\MultiLangInput;

class FilamentMultilangServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-multilang';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasConfigFile()
            ->hasAssets()
            ->publishesServiceProvider('FilamentMultilangServiceProvider');
    }

    public function packageRegistered(): void
    {
        $this->app->singleton('filament-multilang', function (): object {
            return new class() {
                public function components(): array
                {
                    return [
                        'multi-lang-input' => MultiLangInput::class,
                    ];
                }
            };
        });
    }

    public function packageBooted(): void
    {
        // Register the component with Filament Forms
        \Filament\Forms\Components\Field::macro('multiLangInput', function (string $name) {
            return MultiLangInput::make($name);
        });

        // Register CSS assets
        FilamentAsset::register([
            Css::make('multilang-input', __DIR__ . '/../resources/css/multilang-input.css'),
        ]);
    }
} 