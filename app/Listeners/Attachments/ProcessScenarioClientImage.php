<?php

declare(strict_types=1);

namespace App\Listeners\Attachments;

use App\Enums\AttachmentType;
use App\Enums\Disk;
use App\Jobs\Attachments\PrepareScenarioClientImage;
use App\Models\Attachment;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Vagebond\Bijlagen\Enums\AttachmentStatus;
use Vagebond\Bijlagen\Events\AttachmentCreated;
use Vagebond\Bijlagen\Events\AttachmentFactoryStoring;
use Vagebond\Bijlagen\Jobs\MarkAttachmentAsDone;

class ProcessScenarioClientImage
{
    public function handleStoring(AttachmentFactoryStoring $event)
    {
        $factory = $event->factory;

        if (! $this->isObjectRelevant($factory)) {
            return;
        }

        if (! Str::startsWith($factory->mimetype, 'image/')) {
            return new InvalidArgumentException('This should be a valid image file.');
        }

        $factory
            ->setVisibility(Attachment::VISIBILITY_PRIVATE)
            // ->setDisk(Disk::SCENARIO)
            ->setDirnamePrefix('clients');
    }

    public function handleCreated(AttachmentCreated $event)
    {
        $attachment = $event->attachment;

        if (! $this->isObjectRelevant($attachment)) {
            return;
        }

        $attachment->update([
            'status' => AttachmentStatus::PROCESSING,
        ]);

        Bus::chain([
            new PrepareScenarioClientImage($attachment),
            new MarkAttachmentAsDone($attachment),
        ])->dispatch();
    }

    private function isObjectRelevant(object $object)
    {
        $type = AttachmentType::coerce($object->type);
        if (empty($type)) {
            return false;
        }

        if ($type->notIn([
            AttachmentType::ScenarioClientImage,
        ])) {
            return false;
        }

        return true;
    }
}
