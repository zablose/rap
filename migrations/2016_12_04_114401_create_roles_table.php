<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    public function up(): void
    {
        Schema::create('rap_roles', function (Blueprint $table)
        {
            $table->charset   = 'utf8';
            $table->collation = 'utf8_unicode_ci';

            $table->increments('id');
            $table->string('name', 60)->unique();
            $table->mediumText('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('rap_roles');
    }
}
