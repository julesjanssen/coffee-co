<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\Locale;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use LogicException;
use Symfony\Component\Finder\Finder;

use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class AppTranslationsFind extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:translations:find';

    /**
     * The console command description.
     */
    protected $description = 'Find translatable strings.';

    /**
     * Options used to encode JSON
     */
    protected int $jsonOptions = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE;

    /**
     * Active language
     */
    protected ?string $language = null;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->language = $this->selectLanguage();

        $keys = $this->listUntranslatedKeys();
        $translations = [];

        foreach ($keys as $key) {
            $label = $key;
            $default = $this->getDefaultTranslation($key);

            $translation = text(
                label: $label,
                default: $default,
            );

            if (empty($translation)) {
                continue;
            }

            $translation = trim($translation);
            if (! empty($translation)) {
                $translations[$key] = $translation;
            }
        }

        $merged = array_merge($this->listCurrentTranslations(), $translations);

        // filter out keys which no longer exist in the codebase
        $translations = collect($this->findTranslatableKeys())
            ->mapWithKeys(fn($key) => [$key => $merged[$key] ?? null])
            ->filter()
            ->toArray();

        $this->saveTranslations($translations, $this->getLanguageFilePath());

        foreach ($this->listAssetDirectories() as $directory) {
            $this->saveAssetDirectoryTranslations($directory, $translations);
        }
    }

    private function getDefaultTranslation(string $key)
    {
        if ($this->language !== 'en') {
            return '';
        }

        if (preg_match('/\S:\S/', $key)) {
            return Str::after($key, ':');
        }

        return $key;
    }

    private function selectLanguage()
    {
        $languages = Locale::collect()->mapWithKeys(fn($v) => [$v->value => $v->description()]);

        if ($languages->isEmpty()) {
            throw new LogicException('Define language options.');
        }

        if ($languages->count() === 1) {
            return $languages->keys()->first();
        }

        return select(
            label: 'Select language',
            options: $languages,
            default: $languages->keys()->first(),
        );
    }

    private function listUntranslatedKeys()
    {
        $current = array_keys($this->listCurrentTranslations());
        $all = $this->findTranslatableKeys();

        return array_diff($all, $current);
    }

    private function listCurrentTranslations()
    {
        $path = $this->getLanguageFilePath();
        if (! file_exists($path)) {
            return [];
        }

        return json_decode(file_get_contents($path), true);
    }

    public function saveTranslations($translations, $path)
    {
        uksort($translations, fn($a, $b) => strcmp(strtolower((string) $a), strtolower((string) $b)));

        $dir = dirname((string) $path);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $json = json_encode($translations, $this->jsonOptions);
        file_put_contents($path, $json);
    }

    private function saveAssetDirectoryTranslations($directory, $translations)
    {
        $keys = $this->findKeysInDirectories($directory->getPathname());

        $results = Arr::only($translations, $keys);
        $path = $directory->getPathname() . '/locale/' . $this->language . '.json';

        $this->saveTranslations($results, $path);
    }

    private function getLanguageFilePath()
    {
        return base_path('lang/' . $this->language . '.json');
    }

    private function findTranslatableKeys()
    {
        return once(function () {
            $directories = config('translations.directories', []);

            return [
                ...$this->findKeysInConfigs()->toArray(),
                ...$this->findKeysInDirectories($directories),
            ];
        });
    }

    private function findKeysInConfigs()
    {
        $finder = new Finder()
            ->in(resource_path('config/'))
            ->name('*.yaml')
            ->files();

        $pattern = "/(\W)\~([a-z-:]+)(\W)/";

        return collect($finder->getIterator())
            ->map(function ($file) use ($pattern) {
                $keys = [];

                preg_match_all($pattern, $file->getContents(), $matches);

                if (count($matches[2])) {
                    foreach ($matches[2] as $key) {
                        $keys[] = stripslashes($key);
                    }
                }

                return $keys;
            })
            ->flatten(0);
    }

    private function findKeysInDirectories($directories)
    {
        $files = $this->listFiles($directories);
        $pattern = $this->buildPattern();
        $keys = [];

        foreach ($files as $file) {
            // Search the current file for the pattern
            preg_match_all($pattern, (string) $file->getContents(), $matches);
            if (count($matches[2])) {
                // Get all matches
                foreach ($matches[2] as $key) {
                    $keys[] = stripslashes($key);
                }
            }
        }

        // Remove duplicates
        $keys = array_unique($keys);

        return $keys;
    }

    private function listFiles($directories)
    {
        if (! is_array($directories)) {
            $directories = [$directories];
        }

        $extensions = config('translations.extensions', []);
        $extensions = '{' . implode(',', $extensions) . '}';

        $finder = new Finder();
        foreach ($directories as $directory) {
            if (! file_exists($directory)) {
                $directory = base_path() . '/' . ltrim((string) $directory, '/');
            }

            $finder->in($directory)->name('*.' . $extensions)->files();
        }

        return $finder->getIterator();
    }

    private function listAssetDirectories()
    {
        $finder = new Finder()
            ->in(resource_path())
            ->exclude([
                'config',
                'content',
                'lang',
                'scripts',
                'stubs',
                'views',
            ])
            ->depth(0)
            ->directories();

        return $finder->getIterator();
    }

    private function buildPattern()
    {
        $functions = config('translations.functions', []);

        $replace = collect($functions)
            ->map(fn($function) => preg_quote((string) $function))
            ->implode('|');

        $pattern = '/([FUNCTIONS])\([\'"`](.+)[\'"`][\),]/U';
        $pattern = str_replace('[FUNCTIONS]', $replace, $pattern);

        return $pattern;
    }
}
