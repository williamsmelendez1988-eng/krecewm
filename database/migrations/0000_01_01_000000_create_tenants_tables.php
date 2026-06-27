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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->decimal('price', 12, 2);
            $table->enum('billing_period', ['monthly', 'yearly'])->default('monthly');
            $table->integer('max_products')->default(100);
            $table->integer('max_users')->default(2);
            $table->json('features')->nullable();
            $table->timestamps();
        });

        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->nullable()->constrained('plans')->onDelete('restrict');
            $table->string('name', 150);
            $table->string('subdomain', 100)->unique();
            $table->string('custom_domain', 150)->nullable()->unique();
            $table->string('logo', 255)->nullable();
            $table->string('banner', 255)->nullable();
            $table->string('primary_color', 7)->default('#1e293b');
            $table->string('secondary_color', 7)->default('#f59e0b');
            $table->string('contact_email', 150);
            $table->string('contact_phone', 50);
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->json('social_links')->nullable();
            $table->json('schedule')->nullable();
            $table->enum('status', ['active', 'suspended', 'trial'])->default('trial');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Habilitar Soft Deletes solicitado por el usuario
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
        Schema::dropIfExists('plans');
    }
};
