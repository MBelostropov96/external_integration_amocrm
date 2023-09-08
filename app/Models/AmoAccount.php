<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @see Builder::updateOrCreate
 * @method static $this updateOrCreate(array $attributes, array $values = [])
 *
 * @see Builder::findOrFail
 * @method static $this findOrFail(mixed $id, array|string $columns = ['*'])
 */
class AmoAccount extends Model
{
    use HasFactory;

    public const TABLE = 'amo_accounts';

    public const ACCOUNT_ID_COLUMN = 'account_id';
    public const BASE_DOMAIN_COLUMN = 'base_domain';
    public const ACCESS_TOKEN_COLUMN = 'access_token';
    public const REFRESH_TOKEN_COLUMN = 'refresh_token';
    public const EXPIRED_COLUMN = 'expires';

    protected $table = self::TABLE;
    protected $primaryKey = self::ACCOUNT_ID_COLUMN;
    public $incrementing = false;
    protected $fillable = [
        self::ACCOUNT_ID_COLUMN,
        self::BASE_DOMAIN_COLUMN,
        self::ACCESS_TOKEN_COLUMN,
        self::REFRESH_TOKEN_COLUMN,
        self::EXPIRED_COLUMN,
    ];

    public function getAccountId(): int
    {
        return $this->getAttribute(self::ACCOUNT_ID_COLUMN);
    }

    public function setAccountId(int $accountId): void
    {
        $this->setAttribute(self::ACCOUNT_ID_COLUMN, $accountId);
    }

    public function getBaseDomain(): string
    {
        return $this->getAttribute(self::BASE_DOMAIN_COLUMN);
    }

    public function setBaseDomain(string $baseDomain): void
    {
        $this->setAttribute(self::BASE_DOMAIN_COLUMN, $baseDomain);
    }

    public function getAccessToken(): string
    {
        return $this->getAttribute(self::ACCESS_TOKEN_COLUMN);
    }

    public function setAccessToken(string $accessToken): void
    {
        $this->setAttribute(self::ACCESS_TOKEN_COLUMN, $accessToken);
    }

    public function getRefreshToken(): string
    {
        return $this->getAttribute(self::REFRESH_TOKEN_COLUMN);
    }

    public function setRefreshToken(string $refreshToken): void
    {
        $this->setAttribute(self::REFRESH_TOKEN_COLUMN, $refreshToken);
    }

    public function getExpires(): int
    {
        return $this->getAttribute(self::EXPIRED_COLUMN);
    }

    public function setExpires(int $expires): void
    {
        $this->setAttribute(self::EXPIRED_COLUMN, $expires);
    }
}
