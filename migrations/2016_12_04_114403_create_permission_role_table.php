<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rap_permission_role', function (Blueprint $table)
        {
            $table->increments('id');
            $table->unsignedTinyInteger('role_id')->index();
            $table->unsignedSmallInteger('permission_id')->index();
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('rap_roles')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('rap_permissions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('rap_permission_role');
    }
}
