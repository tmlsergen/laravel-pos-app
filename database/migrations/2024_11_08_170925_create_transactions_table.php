<?php

use App\Enums\CurrencyEnum;
use App\Enums\PaymentProviderEnum;
use App\Enums\PaymentStatusEnum;
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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained();
            $table->string('order_id');
            $table->enum('provider', PaymentProviderEnum::values());
            $table->decimal('amount', 10, 2);
            $table->enum('currency', CurrencyEnum::values());
            $table->enum('status', PaymentStatusEnum::values());
            $table->text('raw_response')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
