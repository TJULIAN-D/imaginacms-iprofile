<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IprofileAddAuditStampsInTables extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('iprofile__addresses', function (Blueprint $table) {
      $table->auditStamps();
    });
    Schema::table('iprofile__fields', function (Blueprint $table) {
      $table->auditStamps();
    });
    Schema::table('iprofile__provider_accounts', function (Blueprint $table) {
      $table->auditStamps();
    });
    Schema::table('iprofile__settings', function (Blueprint $table) {
      $table->auditStamps();
    });
    Schema::table('iprofile__user_password_history', function (Blueprint $table) {
      $table->auditStamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {

  }
}
