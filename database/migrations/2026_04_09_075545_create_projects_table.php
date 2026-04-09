<?php

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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tag_id')->nullable()->constrained('project_tags')->nullOnDelete();
            $table->foreignId('project_status_id')->nullable()->constrained('project_statuses')->nullOnDelete();
            $table->string('client_name');
            $table->string('project_name');
            $table->string('project_address')->nullable();
            $table->string('site_area');
            $table->string('built_up_area')->nullable();
            $table->string('project_thumbnail')->nullable();
            $table->string('project_main_image')->nullable();
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
