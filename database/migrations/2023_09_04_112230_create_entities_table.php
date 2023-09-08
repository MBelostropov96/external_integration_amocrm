<?php

declare(strict_types=1);

use App\Models\AmoAccount;
use App\Models\Entity;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(Entity::TABLE, function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger(Entity::ENTITY_ID_COLUMN);
            $table->string(Entity::ENTITY_TYPE_COLUMN);
            $table->json(Entity::DATA_COLUMN);
            $table->unsignedInteger(Entity::ACCOUNT_ID_COLUMN);
            $table->timestamps();

            $table->index(Entity::ACCOUNT_ID_COLUMN, 'amo_account_idx');

            $table->foreign(Entity::ACCOUNT_ID_COLUMN, 'entity_amo_account_fk')
                ->references(AmoAccount::ACCOUNT_ID_COLUMN)
                ->on(AmoAccount::TABLE);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Entity::TABLE);
    }
};
