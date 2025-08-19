<?php

declare(strict_types=1);

namespace App\Jobs\Attachments;

class PrepareScenarioClientImage extends PrepareImage
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->createSizedVersion(600, 800, 'medium');
        $this->createSizedVersion(200, 200, 'small', 'crop');
    }
}
