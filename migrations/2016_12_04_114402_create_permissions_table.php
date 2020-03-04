<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsTable extends Migration
{
    public function up(): void
    {
        Schema::create('rap_permissions', function (Blueprint $table)
        {
            $table->charset   = 'utf8';
            $table->collation = 'utf8_unicode_ci';

            $table->increments('id');
            $table->string('name', 80)->unique();
            $table->mediumText('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('rap_permissions');
    }
}
