<?php

declare(strict_types=1);

namespace App\Jobs\Attachments;

use App\Enums\Queue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Vagebond\Beeld\Facades\Beeld;
use Vagebond\Bijlagen\Models\Attachment;

class PrepareImage implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected Attachment $attachment)
    {
        $this->onQueue(Queue::FILE_PROCESSING);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->createSizedVersion(600, 400, 'medium');
        $this->createSizedVersion(120, 120, 'thumb', 'crop');
    }

    protected function createSizedVersion($maxWidth, $maxHeight, $name, $method = 'resize', $extension = 'webp')
    {
        $file = $this->attachment->file;
        $copy = $file->getTmpLocalCopy();
        $tmpfile = storage_path('tmp/' . Str::random(12) . '.' . $extension);

        Beeld::load($copy)
            ->autoOrient(true)
            ->applyDefaultColorProfile()
            ->{$method}($maxWidth, $maxHeight)
            ->save($tmpfile);

        $this->optimizeFile($tmpfile);

        $this->attachment->putVariantFromPath($name, $tmpfile);

        unlink($tmpfile);
    }

    protected function optimizeFile($path)
    {
        $optimizerChain = OptimizerChainFactory::create();
        $optimizerChain->optimize($path);
    }
}
