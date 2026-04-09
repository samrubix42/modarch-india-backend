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
        Schema::table('applied_jobs', function (Blueprint $table) {
            $table->foreignId('job_profile_id')->nullable()->after('id')->constrained('job_profiles')->nullOnDelete();
            $table->string('job_title')->nullable()->after('job_profile_id');
            $table->string('name')->after('job_title');
            $table->string('email')->after('name');
            $table->string('phone', 50)->after('email');
            $table->string('city')->nullable()->after('phone');
            $table->string('portfolio_url')->nullable()->after('city');
            $table->string('resume_path')->nullable()->after('portfolio_url');
            $table->string('portfolio_path')->nullable()->after('resume_path');
            $table->text('message')->nullable()->after('portfolio_path');
            $table->string('status', 30)->default('new')->after('message');
            $table->timestamp('reviewed_at')->nullable()->after('status');

            $table->index('status');
            $table->index('created_at');
            $table->index(['job_profile_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applied_jobs', function (Blueprint $table) {
            $table->dropIndex(['job_profile_id', 'status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['status']);

            $table->dropConstrainedForeignId('job_profile_id');
            $table->dropColumn([
                'job_title',
                'name',
                'email',
                'phone',
                'city',
                'portfolio_url',
                'resume_path',
                'portfolio_path',
                'message',
                'status',
                'reviewed_at',
            ]);
        });
    }
};
