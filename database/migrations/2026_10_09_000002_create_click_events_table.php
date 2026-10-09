<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('click_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('short_link_id')->constrained()->cascadeOnDelete();
            $table->timestamp('clicked_at');
            $table->string('referer_host')->nullable();
            $table->string('user_agent_family', 50)->nullable();
            $table->char('country_code', 2)->nullable();

            $table->index(['short_link_id', 'clicked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('click_events');
    }
};
