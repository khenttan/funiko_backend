<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_videos', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned();
            $table->string('video')->nullable();
            $table->string('video_title')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('is_delete')->default(0)->comment('1 => Delete, 0 => Not Delete');
            $table->tinyInteger('is_visible')->default(1)->comment('1 => visible, 0 => Not visible');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('post_videos');
    }
}
