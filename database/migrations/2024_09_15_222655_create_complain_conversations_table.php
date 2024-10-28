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
        Schema::create('complain_conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('complain_id'); // Links to the Complain model
            $table->unsignedBigInteger('sender_id'); // Links to the User who sent the message
            $table->text('reply_message'); // The message content
            $table->enum('sender_role', ['user', 'client','admin']); // Defines whether the sender is a user or client

            $table->foreign('complain_id')->references('id')->on('complains')
                ->cascadeOnDelete()->cascadeOnUpdate();

            $table->foreign('sender_id')->references('id')->on('users')
                ->cascadeOnDelete()->cascadeOnUpdate();


            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complain_conversations');
    }
};
