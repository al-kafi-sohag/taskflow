<?php

use App\Traits\AuditColumnsTrait;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use AuditColumnsTrait;

    public function up(): void
    {
        Schema::create('task_projects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('task_id')->constrained('tasks')->nullable()->nullOnDelete();
            $table->foreignId('project_id')->constrained('projects')->nullable()->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
            $this->addAuditColumns($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_projects');
    }
};
