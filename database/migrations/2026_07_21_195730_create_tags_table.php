<?php

use App\Traits\AuditColumnsTrait;
use App\Enums\TagStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use AuditColumnsTrait;

    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            $table->tinyInteger('status')->default(TagStatus::default()->value)->comment(collect(TagStatus::cases())->map(fn ($case) => "{$case->value}={$case->label()}")->implode(', '));

            $table->timestamps();
            $table->softDeletes();
            $this->addAuditColumns($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
