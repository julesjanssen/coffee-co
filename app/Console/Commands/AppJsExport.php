<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Support\Attributes\JsExportable;
use BackedEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionEnum;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class AppJsExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:js:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export constants for JS consumption.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $js = $this->load([
            app_path('Enums'),
        ])
            ->map(fn($v, $k) => $this->processClassContent($v, $k))
            ->filter(fn($v) => ! empty($v))
            ->flatten(1)
            ->join(PHP_EOL . PHP_EOL);

        foreach (['admin'] as $key) {
            $path = resource_path($key . '/ts/shared/constants.ts');
            file_put_contents($path, $js);
        }
    }

    /**
     * @param class-string $className
     */
    private function processClassContent(string $className, string $constName)
    {
        $reflection = new ReflectionClass($className);

        return match (true) {
            $reflection->implementsInterface(BackedEnum::class) => $this->processBackedEnum($className, $constName),
            default => null,
        };
    }

    /**
     * @param class-string $className
     */
    private function processBackedEnum(string $className, string $constName)
    {
        $reflection = new ReflectionEnum($className);
        $cases = collect($reflection->getConstants());

        if ($reflection->hasMethod('description')) {
            $values = $cases->mapWithKeys(fn($v) => [$v->value => $v->description()]);
        } else {
            $values = $cases->mapWithKeys(fn($v) => [$v->value => $v->value]);
        }

        $constContent = $values
            ->map(fn($value, $key) => vsprintf("\t%s: %s,", [
                json_encode($key),
                json_encode($value),
            ]))
            ->implode(PHP_EOL);

        $typeContent = $values
            ->map(fn($v) => json_encode($v))
            ->join(' | ');

        $exports = [
            'export const ' . $constName . ' = {' . PHP_EOL . $constContent . PHP_EOL . '} as const',
            'export type ' . $constName . 'Type = ' . $typeContent,
        ];

        return $exports;
    }

    /**
     * @param  array|string  $paths
     */
    private function load($paths)
    {
        $namespace = $this->laravel->getNamespace();
        $results = collect([]);

        foreach (Finder::create()->in($paths)->files() as $file) {
            $class = $this->classFromFile($file, $namespace);

            if ($attribute = new ReflectionClass($class)->getAttributes(JsExportable::class)) {
                $name = $attribute[0]->newInstance()->name ?? Str::afterLast($class, '\\');
                $results->put($name, $class);
            }
        }

        return $results;
    }

    /**
     * Extract the class name from the given file path.
     *
     * @param SplFileInfo $file
     * @param  string  $namespace
     * @return string
     */
    protected function classFromFile(SplFileInfo $file, string $namespace): string
    {
        return $namespace . str_replace(
            ['/', '.php'],
            ['\\', ''],
            Str::after($file->getRealPath(), realpath(app_path()) . DIRECTORY_SEPARATOR)
        );
    }
}
