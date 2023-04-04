<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stripe_callbacks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('charge_id');
            $table->string('status');
            $table->json('response');
            $table->timestamps();

            $table->unique(['charge_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stripe_callbacks');
    }
};
