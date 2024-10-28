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
        Schema::create('complains', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('food_id');
            $table->unsignedBigInteger('user_id');
            $table->text('message');
            $table->enum('status',['pending','under-review','solved','further-investigation'])->default('pending');
            $table->string('cmp_date')->nullable();
            $table->string('cmp_time')->nullable();
            $table->string('clnt_cmp_date')->nullable();
            $table->string('clnt_cmp_time')->nullable();
            $table->string('clnt_cmp_feedback_date')->nullable();
            $table->string('clnt_cmp_feedback_time')->nullable();

            $table->foreign('order_id')->references('id')->on('orders')
                ->restrictOnDelete()->cascadeOnUpdate();

            $table->foreign('food_id')->references('id')->on('food')
                ->restrictOnDelete()->cascadeOnUpdate();

            $table->foreign('user_id')->references('id')->on('users')
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
        Schema::dropIfExists('complains');
    }
};

