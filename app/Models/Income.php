<?php

namespace App\Models;

use App\Support\HasAdvancedFilter;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Income extends Model implements HasMedia
{
    use HasAdvancedFilter, SoftDeletes, InteractsWithMedia, HasFactory;

    public const TABLE_NAME = 'incomes';

    public const ID = 'id';
    public const DOCUMENT = 'document';
    public const INCOME_CATEGORY_ID = 'income_category_id';
    public const USER_ID = 'user_id';
    public const ENTRY_DATE = 'entry_date';
    public const AMOUNT = 'amount';
    public const DESCRIPTION = 'description';
    public const APPROVED = 'approved';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated';
    public const DELETED_AT = 'deleted_at';

    # Others constants
    public const INCOME_CATEGORY_NAME = 'income_category.name';
    public const USER_NAME = 'user.name';

    public $table = self::TABLE_NAME;

    protected $appends = [
        self::DOCUMENT,
    ];

    protected $casts = [
        self::APPROVED => 'boolean',
    ];

    protected array $dates = [
        self::ENTRY_DATE,
        self::CREATED_AT,
        self::UPDATED_AT,
        self::DELETED_AT,
    ];

    protected array $filterable = [
        self::ID,
        self::ENTRY_DATE,
        self::AMOUNT,
        self::DESCRIPTION,
        self::APPROVED,
        self::INCOME_CATEGORY_NAME,
        self::USER_NAME,
    ];

    protected array $orderable = [
        self::ID,
        self::ENTRY_DATE,
        self::AMOUNT,
        self::DESCRIPTION,
        self::APPROVED,
    ];

    protected $fillable = [
        self::INCOME_CATEGORY_ID,
        self::USER_ID,
        self::ENTRY_DATE,
        self::AMOUNT,
        self::DESCRIPTION,
        self::APPROVED,
        self::CREATED_AT,
        self::UPDATED_AT,
        self::DELETED_AT,
    ];

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * @throws InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $thumbnailWidth  = 50;
        $thumbnailHeight = 50;

        $thumbnailPreviewWidth  = 120;
        $thumbnailPreviewHeight = 120;

        $this->addMediaConversion('thumbnail')
            ->width($thumbnailWidth)
            ->height($thumbnailHeight)
            ->fit('crop', $thumbnailWidth, $thumbnailHeight);
        $this->addMediaConversion('preview_thumbnail')
            ->width($thumbnailPreviewWidth)
            ->height($thumbnailPreviewHeight)
            ->fit('crop', $thumbnailPreviewWidth, $thumbnailPreviewHeight);
    }

    public function incomeCategory(): BelongsTo
    {
        return $this->belongsTo(IncomeCategory::class);
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

    public function getDocumentAttribute()
    {
        return $this->getMedia('income_document')->map(function ($item) {
            $media                      = $item->toArray();
            $media['url']               = $item->getUrl();
            $media['thumbnail']         = $item->getUrl('thumbnail');
            $media['preview_thumbnail'] = $item->getUrl('preview_thumbnail');

            return $media;
        });
    }
}
