<?php

declare(strict_types=1);

namespace App\Models;

use AmoCRM\Helpers\EntityTypesInterface;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\Constraint\IsType;

/**
 * @see Builder::updateOrCreate
 * @method $this updateOrCreate(array $attributes, array $values = [])
 *
 * @see Builder::firstWhere
 * @method static $this firstWhere(\Closure|string|array|Expression $column, mixed $operator = null, mixed $value = null, string $boolean = 'and')
 *
 * @see Builder::findOrFail
 * @method static $this findOrFail(mixed $id, array|string $columns = ['*'])
 */
class Entity extends Model
{
    use HasFactory;

    public const TABLE = 'entities';

    public const ID_COLUMN = 'id';
    public const ENTITY_ID_COLUMN = 'entity_id';
    public const ENTITY_TYPE_COLUMN = 'entity_type';
    public const DATA_COLUMN = 'data';
    public const ACCOUNT_ID_COLUMN = 'account_id';

    protected $table = self::TABLE;

    protected $fillable = [
        self::ENTITY_ID_COLUMN,
        self::ENTITY_TYPE_COLUMN,
        self::DATA_COLUMN,
        self::ACCOUNT_ID_COLUMN,
    ];

    protected $casts = [
        self::DATA_COLUMN => IsType::TYPE_ARRAY,
    ];

    public function getId(): int
    {
        return $this->getAttribute(self::ID_COLUMN);
    }

    public function setId(int $id): void
    {
        $this->setAttribute(self::ID_COLUMN, $id);
    }

    public function getEntityId(): int
    {
        return $this->getAttribute(self::ENTITY_ID_COLUMN);
    }

    public function setEntityId(int $entityId): void
    {
        $this->setAttribute(self::ENTITY_ID_COLUMN, $entityId);
    }

    public function getEntityType(): string
    {
        return $this->getAttribute(self::ENTITY_TYPE_COLUMN);
    }

    public function setEntityType(string $entityType): void
    {
        $this->setAttribute(self::ENTITY_TYPE_COLUMN, $entityType);
    }

    public function getData(): array
    {
        return $this->getAttribute(self::DATA_COLUMN);
    }

    public function setData(array $data): void
    {
        $this->setAttribute(self::DATA_COLUMN, $data);
    }

    public function getAccountId(): int
    {
        return $this->getAttribute(self::ACCOUNT_ID_COLUMN);
    }

    public function setAccountId(int $accountId): void
    {
        $this->setAttribute(self::ACCOUNT_ID_COLUMN, $accountId);
    }

    public static function availableEntities(): array
    {
        return [
            EntityTypesInterface::LEADS,
            EntityTypesInterface::CONTACTS,
//            EntityTypesInterface::CUSTOMERS,
//            EntityTypesInterface::COMPANIES,
        ];
    }
}
