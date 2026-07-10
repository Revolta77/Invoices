<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('google_access_token')->nullable()->after('avatar');
            $table->text('google_refresh_token')->nullable()->after('google_access_token');
            $table->timestamp('google_token_expires_at')->nullable()->after('google_refresh_token');
            $table->string('google_drive_root_folder_id')->nullable()->after('google_token_expires_at');
            $table->timestamp('google_backup_last_at')->nullable()->after('google_drive_root_folder_id');
            $table->string('google_backup_status', 32)->nullable()->after('google_backup_last_at');
            $table->text('google_backup_error')->nullable()->after('google_backup_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'google_access_token',
                'google_refresh_token',
                'google_token_expires_at',
                'google_drive_root_folder_id',
                'google_backup_last_at',
                'google_backup_status',
                'google_backup_error',
            ]);
        });
    }
};
