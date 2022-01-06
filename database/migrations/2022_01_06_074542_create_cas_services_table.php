<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCasServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cas_services', function (Blueprint $table) {
            $table->id();
            $table->string('ticket', 256)->unique();
            $table->string('service_url', 1024);
            $table->integer('service_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('expire_at')->nullable();
            $table->text('proxies')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cas_services');
    }
}
