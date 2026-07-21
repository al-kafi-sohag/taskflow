<?php

use App\Enums\UserStatus;
use App\Traits\AuditColumnsTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use AuditColumnsTrait;

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('status')->default(UserStatus::default()->value)->after('email_verified_at')->comment(collect(UserStatus::cases())->map(fn ($case) => "{$case->value}={$case->label()}")->implode(', '));
            $table->softDeletes();
            $this->addAuditColumns($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
            $this->dropAuditColumns($table);
            $table->dropSoftDeletes();

        });
    }
};
