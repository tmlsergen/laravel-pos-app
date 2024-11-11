<?php

namespace Database\Seeders;

use App\Models\PaymentProvider;
use App\Models\PaymentProviderSetting;
use Illuminate\Database\Seeder;

class PaymentProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $garantiProvider = PaymentProvider::query()
            ->create([
                'name' => 'Garanti',
                'slug' => 'garanti',
                'description' => 'Garanti BBVA Payment Provider',
            ]);

        PaymentProviderSetting::query()->insert([
            [
                'provider' => $garantiProvider->slug,
                'payment_provider_id' => $garantiProvider->id,
                'key' => 'merchant_id',
                'value' => '7000679',
            ],
            [
                'provider' => $garantiProvider->slug,
                'payment_provider_id' => $garantiProvider->id,
                'key' => 'terminal_id',
                'value' => '30691297',
            ],
            [
                'provider' => $garantiProvider->slug,
                'payment_provider_id' => $garantiProvider->id,
                'key' => '2d_provider_user_id',
                'value' => 'PROVAUT',
            ],
            [
                'provider' => $garantiProvider->slug,
                'payment_provider_id' => $garantiProvider->id,
                'key' => '3d_provider_user_id',
                'value' => 'PROVAUT',
            ],
            [
                'provider' => $garantiProvider->slug,
                'payment_provider_id' => $garantiProvider->id,
                'key' => 'user_id',
                'value' => 'PROVAUT',
            ],
            [
                'provider' => $garantiProvider->slug,
                'payment_provider_id' => $garantiProvider->id,
                'key' => '3d_user_id',
                'value' => 'GARANTI',
            ],
            [
                'provider' => $garantiProvider->slug,
                'payment_provider_id' => $garantiProvider->id,
                'key' => 'provision_password',
                'value' => '123qweASD/',
            ],
            [
                'provider' => $garantiProvider->slug,
                'payment_provider_id' => $garantiProvider->id,
                'key' => 'store_key',
                'value' => '12345678',
            ],
        ]);
    }
}
