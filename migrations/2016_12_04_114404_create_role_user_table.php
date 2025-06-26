<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateRoleUserTable extends Migration
{
    public function up(): void
    {
        Schema::create(
            'rap_role_user',
            function (Blueprint $table)
            {
                $table->charset = 'utf8';
                $table->collation = 'utf8_unicode_ci';

                $table->bigIncrements('id');
                $table->unsignedInteger('role_id')->index();
                $table->unsignedBigInteger('user_id')->index();
                $table->timestamps();

                $table->foreign('role_id')->references('id')->on('rap_roles')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        );
    }

    public function down(): void
    {
        Schema::drop('rap_role_user');
    }
}
