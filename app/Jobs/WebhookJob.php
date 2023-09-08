<?php

declare(strict_types=1);

namespace App\Jobs;

use AmoCRM\Models\NoteType\CommonNote;
use App\Helper\WebhookHelper;
use App\Models\Entity;
use App\Service\ClientService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class WebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $message;
    private ClientService $clientService;

    public function __construct(array $message, ClientService $clientService)
    {
        $this->message = $message;
        $this->clientService = $clientService;
    }

    public function handle(): void
    {
        Log::info('Getting started with the hook:', $this->message);

        try {
            $accountId = $this->message['account_id'];
            $entityType = $this->message['entity_type'];
            $actionData = $this->message['action_data'];

            $this->clientService->createByAccountId($accountId);

            $users = $this->clientService->getUsers()->toArray();

            foreach ($actionData as $action => $entities) {
                foreach ($entities as $entity) {
                    $entityId = (int)$entity['id'];

                    if ($action === WebhookHelper::ACTION_UPDATE) {
                        $entityData = [];

                        try {
                            $entityData = Entity::firstWhere(Entity::ENTITY_ID_COLUMN, '=', $entityId);
                        } catch (Throwable $e) {
                            Log::warning(sprintf('Failed to find entity with entity_id %s', $entityId), [
                                $e->getMessage(),
                                $e->getTrace(),
                            ]);
                        }

                        // todo когда-нибудь здвесь будет работа с колекцией
                        $messageInfo = !empty($entityData)
                            ? WebhookHelper::arrayDifference($entity, $entityData->getData())
                            : [];
                    } else {
                        $messageInfo['responsible_user_id'] = $entity['responsible_user_id'];
                    }

                    if (isset($messageInfo['responsible_user_id'])) {
                        $messageInfo['responsible_user_name'] = WebhookHelper::getResponsibleUserName(
                            $users,
                            (int)$messageInfo['responsible_user_id']
                        );
                        unset($messageInfo['responsible_user_id']);
                    }

                    if (isset($messageInfo['status_id']) && isset($entity['pipeline_id'])) {
                        $messageInfo['Status'] = $this->clientService->getLeadStatus(
                            $entity['pipeline_id'],
                            $messageInfo['status_id']
                        )->getName();
                        unset($messageInfo['status_id']);
                    }

                    Log::info('Write data to database');
                    $entityModel = new Entity();
                    $entityModel->setEntityId($entityId);
                    $entityModel->setEntityType($entityType);
                    $entityModel->setData($entity);
                    $entityModel->setAccountId($accountId);

                    $result = $entityModel->updateOrCreate(
                        [Entity::ENTITY_ID_COLUMN => $entityId, Entity::ACCOUNT_ID_COLUMN => $accountId],
                        $entityModel->toArray()
                    );
                    Log::info('Write is done:', $result->toArray());

                    $messageText = WebhookHelper::createTextMessage($action, $entity, $messageInfo);

                    if (!empty($messageText)) {
                        $messageNote = new CommonNote();
                        $messageNote
                            ->setText($messageText)
                            ->setEntityId($entityId);

                        $note = $this->clientService->addNote($entityType, $messageNote);

                        Log::info('Note sent:', $note->toArray());
                    } else {
                        Log::warning('Message text is empty');
                    }
                }
            }
        } catch (Throwable $e) {
            Log::error('Failed to send note:', [
                $e->getMessage(),
                $e->getTrace(),
            ]);
        }
    }
}
