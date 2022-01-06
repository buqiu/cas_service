<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCasProxyGrantingTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cas_proxy_granting_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket', 256)->unique();
            $table->string('pgt_url', 1024);
            $table->integer('service_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->text('proxies')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('expire_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cas_proxy_granting_tickets');
    }
}
