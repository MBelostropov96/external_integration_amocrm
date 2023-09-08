<?php

declare(strict_types=1);

namespace App\Service;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\UsersCollection;
use AmoCRM\Models\Leads\Pipelines\Statuses\StatusModel;
use AmoCRM\Models\NoteModel;
use AmoCRM\Models\NoteType\CommonNote;
use App\Factory\ClientFactory;
use Throwable;

class ClientService
{
    protected ?AmoCRMApiClient $client = null;

    private ClientFactory $factory;

    public function __construct(ClientFactory $factory)
    {
        $this->factory = $factory;
    }

    public function createByAccountId(int $id): void
    {
        $this->client = $this->factory->createByAccountId($id);
    }

    public function getUsers(): UsersCollection
    {
        return $this->client->users()->get();
    }

    public function getLeadStatus(int $pipelineId, int $statusId, array $with = []): StatusModel
    {
        return $this->client->statuses($pipelineId)->getOne($statusId, $with);
    }

    public function addNote(string $entityType, CommonNote $messageNote): NoteModel
    {
        return $this->client->notes($entityType)->addOne($messageNote);
    }

    private function wrapRequest(callable $request)
    {
        try {
            return $request();
        } catch (Throwable $exception) {
            dd($exception->getMessage(),$exception->getTrace());
        }
    }
}
