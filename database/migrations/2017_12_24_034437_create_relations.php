<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('backups', function(Blueprint $table)
        {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('todos', function(Blueprint $table)
        {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('activity_logs', function(Blueprint $table)
        {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('login_as_user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('chats', function(Blueprint $table)
        {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('profiles', function(Blueprint $table)
        {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('messages', function(Blueprint $table)
        {
            $table->foreign('from_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('to_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reply_id')->references('id')->on('messages')->onDelete('cascade');
        });

        Schema::table('user_preferences', function(Blueprint $table)
        {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('uploads', function(Blueprint $table)
        {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('backups', function(Blueprint $table)
        {
            $table->dropForeign('backups_user_id_foreign');
        });

        Schema::table('todos', function(Blueprint $table)
        {
            $table->dropForeign('todos_user_id_foreign');
        });

        Schema::table('activity_logs', function(Blueprint $table)
        {
            $table->dropForeign('activity_logs_user_id_foreign');
            $table->dropForeign('activity_logs_login_as_user_id_foreign');
        });

        Schema::table('chats', function(Blueprint $table)
        {
            $table->dropForeign('chats_user_id_foreign');
        });

        Schema::table('profiles', function(Blueprint $table)
        {
            $table->dropForeign('profiles_user_id_foreign');
        });

        Schema::table('messages', function(Blueprint $table)
        {
            $table->dropForeign('messages_from_user_id_foreign');
            $table->dropForeign('messages_to_user_id_foreign');
            $table->dropForeign('messages_reply_id_foreign');
        });

        Schema::table('user_preferences', function(Blueprint $table)
        {
            $table->dropForeign('user_preferences_user_id_foreign');
        });

        Schema::table('uploads', function(Blueprint $table)
        {
            $table->dropForeign('uploads_user_id_foreign');
        });
    }
}
