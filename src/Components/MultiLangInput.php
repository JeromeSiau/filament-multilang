<?php

namespace JeromeSiau\FilamentMultilang\Components;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;

class MultiLangInput extends Tabs
{
    public string $fieldKey = '';
    protected string $inputType = 'text';
    protected bool $isRequired = false;
    protected string $type = 'text';
    protected array $locales = [];
    protected array $requiredLocales = [];

    protected $transformCallback;
    protected $reverseTransformCallback;

    protected $toolbarButtons = [
        'attachFiles',
        'bold',
        'italic',
        'strike',
        'link',
        'h2',
        'h3',
        'h4',
        'blockquote',
        'orderedList',
        'unorderedList',
        'redo',
        'undo',
    ];

    protected $editorHeight = null;

    public static function make(?string $label = null): static
    {
        static::configureUsing(function ($component) use ($label) {
            $component->fieldKey = $label;
            $component->locales = config('filament-multilang.locales', []);
        });

        return parent::make($label);
    }

    public function required(bool $condition = true): static
    {
        $this->isRequired = $condition;
        return $this;
    }

    public function requiredLocales(array $locales): static
    {
        $this->requiredLocales = $locales;
        return $this;
    }

    public function locales(array $locales): static
    {
        $this->locales = $locales;
        return $this;
    }

    public function type(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function transform(callable $transformCallback, callable $reverseTransformCallback = null): static
    {
        $this->transformCallback = $transformCallback;
        $this->reverseTransformCallback = $reverseTransformCallback;
        return $this;
    }

    public function toolbarButtons(array $buttons): static
    {
        $this->toolbarButtons = $buttons;
        return $this;
    }

    public function editorHeight(?string $height): static
    {
        $this->editorHeight = $height;
        return $this;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->tabs(function (MultiLangInput $multiLangComponent) {
                $tabs = [];
                
                foreach ($this->locales as $lang => $info) {
                    $isLocaleRequired = $this->isRequired && (
                        !empty($this->requiredLocales) ? in_array($lang, $this->requiredLocales) : true
                    );
                    
                    $tabs[] = Tabs\Tab::make('tab-' . $lang)
                        ->statePath($this->getLangKey())
                        ->label(fn() => $info['name'] . ($isLocaleRequired ? ' *' : ''))
                        ->schema(fn (Tabs\Tab $tabComponent) => [
                            $this->createInputComponent($lang, $multiLangComponent, $isLocaleRequired)
                        ]);
                }

                return $tabs;
            })->extraAttributes(['class'=>'fi-multilang-input']);
    }

    protected function createInputComponent(string $lang, MultiLangInput $multiLangComponent, bool $isRequired)
    {
        if ($this->type === 'rich') {
            $editor = RichEditor::make($lang)
                ->required($isRequired)
                ->label(fn () => $multiLangComponent->getLabel())
                ->toolbarButtons($this->toolbarButtons)
                ->extraAttributes([
                    'wire:ignore' => true,
                    'x-data' => '{}'
                ]);

            if ($this->editorHeight) {
                $editor->extraAttributes([
                    'style' => "height: {$this->editorHeight}; min-height: {$this->editorHeight}; max-height: {$this->editorHeight}; overflow-y: hidden;"
                ]);
            }

            if ($this->transformCallback) {
                $editor->beforeStateDehydrated(function ($component) {
                    $state = $component->getState();
                    if ($state && is_callable($this->transformCallback)) {
                        $component->state(($this->transformCallback)($state));
                    }
                });
            }

            return $editor;
        }

        return TextInput::make($lang)
            ->required($isRequired)
            ->label(fn () => $multiLangComponent->getLabel());
    }

    public function getLangKey(): string
    {
        return $this->fieldKey;
    }
} 