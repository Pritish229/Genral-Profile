<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('universities', function (Blueprint $table) {
            $table->id();
            $table->string('org_name');
            $table->string('city');
            $table->string('district');
            $table->string('state');
            $table->string('phone_no')->unique();
            $table->string('alternate_no')->nullable();
            $table->string('email_id')->unique();
            $table->string('alt_email_id')->nullable();
            $table->string('org_logo')->nullable();
            $table->text('address');
            $table->text('website_url')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('universities');
    }
};