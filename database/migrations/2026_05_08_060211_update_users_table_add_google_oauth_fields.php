<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Google OAuth fields
            $table->string('google_id')->nullable()->after('email');
            $table->string('avatar')->nullable()->after('google_id');

            // Make fields nullable for Google users
            $table->string('first_name')->nullable()->change();
            $table->string('last_name')->nullable()->change();
            $table->string('phone_no')->nullable()->change();
            $table->string('address')->nullable()->change();
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'avatar']);

            $table->string('first_name')->nullable(false)->change();
            $table->string('last_name')->nullable(false)->change();
            $table->string('phone_no')->nullable(false)->change();
            $table->string('address')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
};
