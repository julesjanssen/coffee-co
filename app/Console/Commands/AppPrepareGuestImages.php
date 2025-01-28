<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Symfony\Component\Yaml\Yaml;
use Vagebond\Beeld\Facades\Beeld;

use function Laravel\Prompts\text;

class AppPrepareGuestImages extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:prepare:guest-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prepare guest images.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $source = text(
            label: 'Please provide path to photos listing file (.yaml)',
            required: true,
            validate: function (string $input) {
                $path = $this->prepareSourcePath($input);
                if (empty($path)) {
                    return 'Path not found.';
                }
            }
        );

        $path = $this->prepareSourcePath($source);
        $baseDir = dirname((string) $path);

        $yaml = Yaml::parseFile($path);

        $data = collect($yaml)
            ->map(fn($v) => $this->processImage($v, $baseDir))
            ->filter()
            ->toArray();

        file_put_contents(
            $baseDir . '/dest/photos.json',
            json_encode($data, JSON_PRETTY_PRINT)
        );
    }

    private function processImage(array $details, string $baseDir)
    {
        if (! array_key_exists('basename', $details)) {
            throw new Exception('Missing basename in config.');
        }

        $paths = [
            $baseDir . '/' . $details['basename'],
            $baseDir . '/src/' . $details['basename'],
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                break;
            }
        }

        if (! file_exists($path)) {
            $this->info('Can not find ' . $details['basename']);

            return;
        }

        $filename = hash('xxh3', (string) $details['basename']) . '.webp';
        $dest = $baseDir . '/dest/' . $filename;

        if (! is_dir(dirname($dest))) {
            mkdir(dirname($dest));
        }

        Beeld::load($path)
            ->resize(2000, 2000)
            ->autoOrient()
            ->applyDefaultColorProfile()
            ->quality(88)
            ->save($dest);

        return [
            ...$details,
            'basename' => $filename,
        ];
    }

    private function prepareSourcePath(string $path)
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (empty($extension) || ! in_array($extension, ['yaml', 'yml'])) {
            return;
        }

        $paths = [
            $path,
            base_path($path),
            storage_path($path),
        ];

        foreach ($paths as $path) {
            if (file_exists($path) && is_file($path)) {
                return $path;
            }
        }
    }
}
