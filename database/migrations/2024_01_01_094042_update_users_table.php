<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('password');
            $table->enum('is_admin', [0, 1])->default(0)->after('avatar');
            $table->string('gender')->nullable()->after('is_admin');
            $table->string('address')->nullable()->after('gender');
            $table->string('phone')->nullable()->after('address');
            $table->integer('number_code')->nullable()->after('phone');
            $table->boolean('is_active')->default(false)->after('number_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
       public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar');
            $table->dropColumn('is_admin');
            $table->dropColumn('gender');
            $table->dropColumn('address');
            $table->dropColumn('phone');
        });
    }
}
