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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstName',50);
            $table->string('lastName',50)->nullable();
            $table->string('email',50)->unique();
            $table->string('mobile',50)->nullable();
            $table->string('image')->nullable();
            $table->string('password',1000);
            $table->enum('role',['admin','client','user'])->default('user');
            $table->boolean('accept_registration_tnc')->default(0);
            $table->string('otp',10);
            $table->boolean('is_email_verified')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};