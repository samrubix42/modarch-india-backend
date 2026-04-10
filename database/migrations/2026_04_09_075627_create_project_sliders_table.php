<?php

use App\Enums\ContentType;
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
        Schema::create('project_sliders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('project_category_id')->constrained('project_categories')->cascadeOnDelete();
            $table->enum('type', ContentType::values())->default(ContentType::IMAGE->value);
            $table->string('image')->nullable();
            $table->string('video')->nullable();
            $table->text('description')->nullable();
            $table->string('width', 50)->default('100');
            $table->unsignedInteger('sort_order')->default(1);
            $table->timestamps();

            $table->index(['project_id', 'project_category_id', 'sort_order']);
            $table->index(['project_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_sliders');
    }
};
