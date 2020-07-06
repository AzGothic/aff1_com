<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMessagesScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('messages_schedule', function (Blueprint $table) {
            $table->dropIndex(['client_id', 'message_id', 'time']);
            $table->dropColumn('client_id');
            $table->index(['message_id']);
            $table->index(['time']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('messages_schedule', function (Blueprint $table) {
            $table->bigInteger('client_id')->after('id');
            $table->index(['client_id']);
        });
    }
}
