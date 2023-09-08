<?php

declare(strict_types=1);

use App\Models\AmoAccount;
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
        Schema::create(AmoAccount::TABLE, function (Blueprint $table) {
            $table->unsignedInteger(AmoAccount::ACCOUNT_ID_COLUMN)->primary();
            $table->string(AmoAccount::BASE_DOMAIN_COLUMN);
            $table->text(AmoAccount::ACCESS_TOKEN_COLUMN);
            $table->text(AmoAccount::REFRESH_TOKEN_COLUMN);
            $table->timestamp(AmoAccount::EXPIRED_COLUMN);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(AmoAccount::TABLE);
    }
};
