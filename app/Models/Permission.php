<?php

namespace App\Models;

use App\Support\HasAdvancedFilter;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use HasAdvancedFilter, SoftDeletes, HasFactory;

    public const TABLE_NAME = 'permissions';

    public const ID = 'id';
    public const TITLE = 'title';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated';
    public const DELETED_AT = 'deleted_at';

    public $table = 'permissions';

    protected array $orderable = [
        self::ID,
        self::TITLE,
    ];

    protected array $filterable = [
        self::ID,
        self::TITLE,
    ];

    protected array $dates = [
        self::CREATED_AT,
        self::UPDATED_AT,
        self::DELETED_AT,
    ];

    protected $fillable = [
        self::TITLE,
        self::CREATED_AT,
        self::UPDATED_AT,
        self::DELETED_AT,
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
