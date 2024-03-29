<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id          ('event_id')->unique()->autoIncrement();
            $table->string      ('name');
            $table->string      ('location')->nullable();
            $table->string      ('zip_code')->nullable();
            $table->string      ('street_name')->nullable();
            $table->string      ('house_number')->nullable();
            $table->decimal     ('longitude',10,7)->nullable();
            $table->decimal     ('latitude',10,7)->nullable();
            $table->string      ('description')->nullable();
            $table->dateTime    ('start_datetime');
            $table->dateTime    ('end_datetime');
            $table->integer     ('status');  //0,1,2

            $table->bigInteger  ('event_creator_id')->unsigned();
            $table->bigInteger  ('event_type_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('event_creator_id')->references('user_id')
                ->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('event_type_id')->references('event_type_id')
                ->on('event_types');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
