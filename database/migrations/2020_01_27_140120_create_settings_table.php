<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id')->unique()->index();
            $table->string('name');
            $table->string('slogan')->nullable();
            $table->text('description');
            $table->text('keywords')->nullable();
            $table->string('author');

            // contact
            $table->string('email')->nullable();
            $table->string('cellphone')->nullable();
            $table->string('telephone')->nullable();
            $table->string('address')->nullable();
            $table->string('po_box')->nullable();

            // social media
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('youtube')->nullable();
            $table->string('instagram')->nullable();

            // google maps
            $table->smallInteger('zoom_level')->nullable();
            $table->string('latitude', '50')->nullable();
            $table->string('longitude', '50')->nullable();

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
        Schema::dropIfExists('settings');
    }
}
