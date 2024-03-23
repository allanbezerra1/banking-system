<?php

use App\Models\ExpenseCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpenseCategoriesTable extends Migration
{
    public function up(): void
    {
        Schema::create(ExpenseCategory::TABLE_NAME, function (Blueprint $table) {
            $table->bigIncrements(ExpenseCategory::ID);
            $table->string(ExpenseCategory::NAME);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(ExpenseCategory::TABLE_NAME);
    }
}
