<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreatePermissionUserTable extends Migration
{
    public function up(): void
    {
        Schema::create(
            'rap_permission_user',
            function (Blueprint $table)
            {
                $table->charset = 'utf8';
                $table->collation = 'utf8_unicode_ci';

                $table->bigIncrements('id');
                $table->unsignedInteger('user_id')->index();
                $table->unsignedInteger('permission_id')->index();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('permission_id')->references('id')->on('rap_permissions')->onDelete('cascade');
            }
        );
    }

    public function down(): void
    {
        Schema::drop('rap_permission_user');
    }
}
