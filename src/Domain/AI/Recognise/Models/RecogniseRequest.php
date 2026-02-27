<?php

declare(strict_types=1);

namespace Domain\AI\Recognise\Models;

use Domain\AI\Recognise\Enums\RecogniseStatus;
use Domain\File\Models\File;
use Domain\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $user_id
 * @property string $file_id
 * @property string|null $operation_id
 * @property array<string, string>|null $response
 * @property RecogniseStatus $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecogniseRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecogniseRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecogniseRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecogniseRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecogniseRequest whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecogniseRequest whereFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecogniseRequest whereOperationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecogniseRequest whereResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecogniseRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecogniseRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RecogniseRequest whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RecogniseRequest extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'file_id',
        'operation_id',
        'response',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'response' => 'array',
            'status' => RecogniseStatus::class,
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<File, $this>
     */
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
}
