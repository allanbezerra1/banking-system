<?php

namespace App\Models;

use App\Support\HasAdvancedFilter;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasAdvancedFilter, SoftDeletes, HasFactory;

    public const TABLE_NAME = 'expenses';

    public const ID = 'id';
    public const EXPENSE_CATEGORY_ID = 'expense_category_id';
    public const USER_ID = 'user_id';
    public const ENTRY_DATE = 'entry_date';
    public const AMOUNT = 'amount';
    public const DESCRIPTION = 'description';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated';
    public const DELETED_AT = 'deleted_at';

    # Others constants
    public const EXPENSE_CATEGORY_NAME = 'expense_category.name';
    public const USER_NAME = 'user.name';

    public $table = self::TABLE_NAME;

    protected array $dates = [
        self::ENTRY_DATE,
        self::CREATED_AT,
        self::UPDATED_AT,
        self::DELETED_AT,
    ];

    protected array $orderable = [
        self::ID,
        self::ENTRY_DATE,
        self::AMOUNT,
        self::DESCRIPTION,
    ];

    protected array $filterable = [
        self::ID,
        self::ENTRY_DATE,
        self::AMOUNT,
        self::DESCRIPTION,
        self::EXPENSE_CATEGORY_NAME,
        self::USER_NAME,
    ];

    protected $fillable = [
        self::EXPENSE_CATEGORY_ID,
        self::USER_ID,
        self::ENTRY_DATE,
        self::AMOUNT,
        self::DESCRIPTION,
        self::CREATED_AT,
        self::UPDATED_AT,
        self::DELETED_AT,
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function expenseCategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getEntryDateAttribute($value): ?string
    {
        return $value ? Carbon::createFromFormat('Y-m-d', $value)->format(config('project.date_format')) : null;
    }

    public function setEntryDateAttribute($value): void
    {
        $this->attributes['entry_date'] = $value ? Carbon::createFromFormat(config('project.date_format'), $value)->format('Y-m-d') : null;
    }
}
