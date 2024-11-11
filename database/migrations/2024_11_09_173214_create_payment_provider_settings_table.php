<?php

use App\Enums\PaymentProviderEnum;
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
        Schema::create('payment_provider_settings', function (Blueprint $table) {
            $table->id();
            $table->enum('provider', PaymentProviderEnum::values());
            $table->foreignId('payment_provider_id')->constrained('payment_providers');
            $table->string('key');
            $table->string('value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_settings');
    }
};
