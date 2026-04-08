<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('parsed_items', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->integer('status_code');
            $table->text('content_snippet')->nullable();
            $table->json('network_headers')->nullable();
            $table->timestamp('parsed_at');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('parsed_items'); }
};