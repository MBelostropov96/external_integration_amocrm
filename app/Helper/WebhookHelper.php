<?php

declare(strict_types=1);

namespace App\Helper;

use Carbon\Carbon;

class WebhookHelper
{
    public const ACTION_ADD = 'add';
    public const ACTION_UPDATE = 'update';
    public const DATE_PATTERN = 'Y-m-d H:i:s';
    public const UNKNOWN = 'unknown';

    /**
     * @param array $users
     * @param int $responsibleUserId
     * @return string|null
     */
    public static function getResponsibleUserName(array $users, int $responsibleUserId): ?string
    {
        foreach ($users as $user) {
            if ($user['id'] === $responsibleUserId) {
                $responsibleUserName = $user['name'];
            }
        }

        return $responsibleUserName ?? null;
    }

    /**
     * @param array $entityData
     * @param array $baseEntityData
     * @return array
     */
    public static function arrayDifference(array $entityData, array $baseEntityData): array
    {
        $cfEntityData = self::getCfValues($entityData);
        $cfBaseEntityData = self::getCfValues($baseEntityData);

        $result = [];
        foreach ($cfEntityData as $cfKey => $cf) {
            if (is_array($cf)) {
                if (isset($cfBaseEntityData[$cfKey])) {
                    if (!empty(array_diff($cf['values'], $cfBaseEntityData[$cfKey]['values']))) {
                        $result[$cf['name']] = $cf['values'];
                    }
                } else {
                    $result[$cf['name']] = $cf['values'];
                }
            } else {
                if (isset($cfBaseEntityData[$cfKey])) {
                    if ($cf !== $cfBaseEntityData[$cfKey]) {
                        $result[$cfKey] = $cf;
                    }
                } else {
                    $result[$cfKey] = $cf;
                }
            }
        }

        return $result;
    }

    /**
     * @param array $data
     * @return array
     */
    public static function getCfValues(array $data = []): array
    {
        $result = [];

        if (!empty($data['name'])) {
            $result['name'] = $data['name'];
        }

        if (!empty($data['responsible_user_id'])) {
            $result['responsible_user_id'] = $data['responsible_user_id'];
        }

        if (!empty($data['price'])) {
            $result['price'] = $data['price'];
        }

        if (!empty($data['status_id'])) {
            $result['status_id'] = $data['status_id'];
        }

        if (!empty($data)) {
            foreach ($data as $datum) {
                if (!empty($datum) && is_array($datum)) {
                    foreach ($datum as $customField) {
                        foreach ($customField as $cfValues) {
                            if (!empty($cfValues) && is_array($cfValues)) {
                                foreach ($cfValues as $cfValue) {
                                    if (!empty($customField['id']) && !empty($cfValue['value'])) {
                                        $result[$customField['id']]['name'] = $customField['name'];
                                        $result[$customField['id']]['values'][] = $cfValue['value'];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @param string $action
     * @param array $entity
     * @param array $messageInfo
     * @return string
     */
    public static function createTextMessage(string $action, array $entity, array $messageInfo): string
    {
        $messageUpdate = '';

        if ($action === self::ACTION_UPDATE) {
            $messageInfoToString = implode(', ', array_map(
                function ($value, $key) {
                    if (is_array($value)) {
                        return sprintf('%s%s - %s', PHP_EOL, $key, implode(', ', $value));
                    } else {
                        return sprintf('%s%s - %s', PHP_EOL, $key, $value);
                    }
                },
                $messageInfo,
                array_keys($messageInfo)
            ));

            if (!empty($messageInfoToString)) {
                $messageUpdate = sprintf('Измененные поля: %s', $messageInfoToString);
            }
        }

        return match ($action) {
            self::ACTION_ADD => sprintf(
                'Наименование: %s. Ответственный: %s. Время добавления: %s',
                $entity['name'] ?? self::UNKNOWN,
                $messageInfo['responsible_user_name'] ?? self::UNKNOWN,
                Carbon::createFromTimestamp($entity['date_create'], 'Europe/Moscow')->format(self::DATE_PATTERN) ?? self::UNKNOWN
            ),
            self::ACTION_UPDATE => sprintf(
                'Наименование: %s. Время изменения: %s. %s',
                $entity['name'] ?? self::UNKNOWN,
                Carbon::createFromTimestamp($entity['updated_at'], 'Europe/Moscow')->format(self::DATE_PATTERN) ?? self::UNKNOWN,
                $messageUpdate,
            ),
            default => '',
        };
    }
}
