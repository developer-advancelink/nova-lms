<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('bio')->nullable()->after('role');
        $table->string('id_number')->nullable()->after('bio');   // student ID or staff ID
        $table->string('department')->nullable()->after('id_number'); // program or department
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['bio', 'id_number', 'department']);
    });
}
};
