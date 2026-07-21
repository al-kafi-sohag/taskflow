<?php

use App\Traits\AuditColumnsTrait;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use AuditColumnsTrait;

    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();

            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(TaskStatus::default()->value)->comment(collect(TaskStatus::cases())->map(fn ($case) => "{$case->value}={$case->label()}")->implode(', '));
            $table->tinyInteger('priority')->default(TaskPriority::default()->value)->comment(collect(TaskPriority::cases())->map(fn ($case) => "{$case->value}={$case->label()}")->implode(', '));

            $table->timestamp('due_date')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $this->addAuditColumns($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
