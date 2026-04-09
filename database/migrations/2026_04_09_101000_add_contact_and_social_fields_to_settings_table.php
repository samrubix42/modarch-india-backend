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
        Schema::table('settings', function (Blueprint $table) {
            $table->text('address_1')->nullable()->after('id');
            $table->text('address_2')->nullable()->after('address_1');
            $table->string('phone_1', 50)->nullable()->after('address_2');
            $table->string('phone_2', 50)->nullable()->after('phone_1');
            $table->string('email_1')->nullable()->after('phone_2');
            $table->string('email_2')->nullable()->after('email_1');
            $table->string('instagram_url')->nullable()->after('email_2');
            $table->string('linkedin_url')->nullable()->after('instagram_url');
            $table->string('facebook_url')->nullable()->after('linkedin_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'address_1',
                'address_2',
                'phone_1',
                'phone_2',
                'email_1',
                'email_2',
                'instagram_url',
                'linkedin_url',
                'facebook_url',
            ]);
        });
    }
};
