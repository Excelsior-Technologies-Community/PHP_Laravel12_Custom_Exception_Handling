<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exception_logs', function (Blueprint $table) {
            $table->string('severity')->default('medium')->after('exception_type'); // low, medium, high, critical
            $table->string('status')->default('open')->after('severity');           // open, investigating, resolved
            $table->text('stack_trace')->nullable()->after('status');
            $table->string('ip_address')->nullable()->after('stack_trace');
            $table->text('user_agent')->nullable()->after('ip_address');
            $table->string('http_method')->nullable()->after('user_agent');
            $table->json('request_payload')->nullable()->after('http_method');

            $table->index('created_at');
            $table->index('exception_type');
            $table->index('severity');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('exception_logs', function (Blueprint $table) {
            $table->dropColumn(['severity', 'status', 'stack_trace', 'ip_address', 'user_agent', 'http_method', 'request_payload']);
        });
    }
};
