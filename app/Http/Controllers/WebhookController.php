<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Models\NoteType\CommonNote;
use App\Exceptions\InvalidParameterException;
use App\Helper\WebhookHelper;
use App\Jobs\WebhookJob;
use App\Models\Entity;
use App\Service\ClientService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class WebhookController extends Controller
{
    private ClientService $clientService;

    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
    }

    public function __invoke(Request $request): Response
    {
        $message['account_id'] = $request->input('account_id');
        $message['entity_type'] = $request->input('entity_type');
        $message['action_data'] = $request->input($message['entity_type']);

        WebhookJob::dispatch($message, $this->clientService)->onQueue('webhook');

        return response()->noContent();

//        try {
//            $entityType = $request->input('entity_type');
//            $accountId = $request->input('account_id');
//            $actionData = $request->input($entityType);
//
//            $this->clientService->createByAccountId($accountId);
//
//            $users = $this->clientService->getUsers()->toArray();
//
//            foreach ($actionData as $action => $entities) {
//                foreach ($entities as $entity) {
//                    $entityId = (int)$entity['id'];
//
//                    if ($action === WebhookHelper::ACTION_UPDATE) {
//                        $entityData = [];
//
//                        try {
//                            $entityData = Entity::firstWhere(Entity::ENTITY_ID_COLUMN, '=', $entityId);
//                        } catch (Throwable $e) {
//                            Log::warning('Неудалось найти Entity', [
//                                $e->getMessage(),
//                                $e->getTrace(),
//                            ]);
//                        }
//
//                        $messageInfo = !empty($entityData) ? WebhookHelper::arrayDifference($entity, $entityData->getData()) : [];
//                    } else {
//                        $messageInfo['responsible_user_id'] = $entity['responsible_user_id'];
//                    }
//
//                    if (isset($messageInfo['responsible_user_id'])) {
//                        $messageInfo['responsible_user_name'] = WebhookHelper::getResponsibleUserName($users, (int)$messageInfo['responsible_user_id']);
//                        unset($messageInfo['responsible_user_id']);
//                    }
//
//                    if (isset($messageInfo['status_id']) && isset($entity['pipeline_id'])) {
//                        $messageInfo['Status'] = $this->clientService->getLeadStatus($entity['pipeline_id'], $messageInfo['status_id'])->getName();
//                        unset($messageInfo['status_id']);
//                    }
//
//                    $entityModel = new Entity();
//                    $entityModel->setEntityId($entityId);
//                    $entityModel->setEntityType($entityType);
//                    $entityModel->setData($entity);
//                    $entityModel->setAccountId((int)$entity['account_id']);
//
//                    $entityModel->updateOrCreate(
//                        [Entity::ENTITY_ID_COLUMN => $entityId, Entity::ACCOUNT_ID_COLUMN => (int)$entity['account_id']],
//                        $entityModel->toArray()
//                    );
//
//                    $messageText = WebhookHelper::createTextMessage($action, $entity, $messageInfo);
//
//                    dd($messageText);
//                    $messageNote = new CommonNote();
//                    $messageNote
//                        ->setText($messageText)
//                        ->setEntityId($entityId);
//
//                    $note = $this->clientService->addNote($entityType, $messageNote);
//
//                    Log::info('NOTE:', $note->toArray());
//                    dd($note);
//                }
//            }
//        } catch (Throwable $e) {
//            Log::info('ERROR:', [$e->getMessage(), $e->getTrace()]);
//            dd($e->getMessage(), $e->getTrace());
//        }
    }
}
