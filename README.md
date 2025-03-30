# Filament MultiLang Input

A component for managing multilingual fields in Filament 3, integrated with Spatie Laravel Translatable.

## Installation

You can install the package via composer:

```bash
composer require jeromesiau/filament-multilang
```

This package automatically integrates [Spatie Laravel Translatable](https://github.com/spatie/laravel-translatable) to efficiently manage translations storage.

You can publish the configuration file:

```bash
php artisan vendor:publish --tag="filament-multilang-config"
```

The published configuration file will look like this:

```php
return [
    'locales' => [
        'fr' => ['name' => 'French'],
        'en' => ['name' => 'English'],
    ],
];
```

## Model Configuration

To use the component with your models, make sure they use Spatie's `HasTranslations` trait:

```php
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasTranslations;
    
    public $translatable = ['title', 'content'];
}
```

## Usage

```php
use JeromeSiau\FilamentMultilang\Components\MultiLangInput;

// In your Filament form
MultiLangInput::make('title')
    ->required() // All languages will be required

// For rich text fields
MultiLangInput::make('content')
    ->type('rich')
    ->required()

// To make only specific languages required
MultiLangInput::make('title')
    ->required()
    ->requiredLocales(['fr'])

// Customize available languages
MultiLangInput::make('title')
    ->locales([
        'fr' => ['name' => 'French'],
        'en' => ['name' => 'English'],
        'es' => ['name' => 'Spanish'],
    ])

// Customize rich editor toolbar buttons
MultiLangInput::make('content')
    ->type('rich')
    ->toolbarButtons([
        'bold',
        'italic',
        'link',
    ])

// Set editor height
MultiLangInput::make('content')
    ->type('rich')
    ->editorHeight('300px')
```

## Data Transformation

You can transform data before it's stored:

```php
MultiLangInput::make('content')
    ->type('rich')
    ->transform(
        function ($value) {
            // Transform value before storing
            return clean($value);
        },
        function ($value) {
            // Transform value when retrieved
            return $value;
        }
    )
```

## Data Structure

Data is stored as an associative array with language codes as keys:

```php
[
    'fr' => 'French content',
    'en' => 'English content',
]
```

With Spatie Laravel Translatable, this array is automatically stored in a JSON field in your database, which greatly simplifies translation management.

## Credits

- [Jérôme Siau](https://github.com/jeromesiau)
- [All Contributors](../../contributors)

## License

MIT 