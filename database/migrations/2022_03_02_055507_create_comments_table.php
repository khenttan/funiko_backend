<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_video_id');
            $table->integer('user_id')->unsigned();
            $table->text('comment_text')->nullable();
            $table->integer('parent_id')->unsigned()->default(0);
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('post_video_id')->references('id')->on('post_videos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
