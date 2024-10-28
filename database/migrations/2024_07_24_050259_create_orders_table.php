<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('food_id');
            $table->unsignedBigInteger('client_id');
            $table->enum('status',['pending','approved food request','completed','cancel'])->default('pending');
            $table->string('order_date');
            $table->string('order_time');
            $table->boolean('accept_order_request_tnc')->default(0);
            $table->string('approve_date')->nullable();
            $table->string('approve_time')->nullable();
            $table->boolean('accept_food_deliver_tnc')->default(0);
            $table->string('delivery_date')->nullable();
            $table->string('delivery_time')->nullable();
            $table->string('cancel_date')->nullable();
            $table->string('cancel_time')->nullable();

            $table->foreign('user_id')->references('id')->on('users')
                ->restrictOnDelete()->cascadeOnUpdate();

            $table->foreign('food_id')->references('id')->on('food')
                ->restrictOnDelete()->cascadeOnUpdate();

            $table->foreign('client_id')->references('id')->on('users')
                ->restrictOnDelete()->cascadeOnUpdate();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
