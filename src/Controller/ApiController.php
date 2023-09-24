<?php

declare(strict_types=1);

namespace App\Controller;

use App\Bundle\Message\RetryCollectRecord;
use App\Bundle\Message\RetryDownloadImage;
use App\Bundle\Repository\DoctrineRepository;
use Mango\HttpKernel\AsPayloadInitializer;
use Mango\HttpKernel\MapRequestPayload;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Ulid;

#[AsController]
#[Route('/api')]
#[IsGranted('ROLE_STAFF')]
class ApiController
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private DoctrineRepository $repository,
    ) {
    }

    #[AsPayloadInitializer]
    public function initRetryRecord(Ulid $id): RetryCollectRecord
    {
        if (! $task = $this->repository->getRecordTask($id)) {
            throw new NotFoundHttpException();
        }

        return new RetryCollectRecord($task);
    }

    #[Route('/record-tasks/{id}/retry', methods: ['POST'])]
    public function retryRecord(
        #[MapRequestPayload]
        RetryCollectRecord $data,
    ): Response {
        $this->messageBus->dispatch($data);

        return new JsonResponse($data, Response::HTTP_ACCEPTED);
    }

    #[AsPayloadInitializer]
    public function initRetryImage(Ulid $id): RetryDownloadImage
    {
        if (! $task = $this->repository->getImageTask($id)) {
            throw new NotFoundHttpException();
        }

        return new RetryDownloadImage($task);
    }

    #[Route('/image-tasks/{id}/retry', methods: ['POST'])]
    public function retryImage(
        #[MapRequestPayload]
        RetryDownloadImage $data,
    ): Response {
        $this->messageBus->dispatch($data);

        return new JsonResponse($data, Response::HTTP_ACCEPTED);
    }
}
