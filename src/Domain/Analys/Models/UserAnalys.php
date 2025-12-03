<?php

declare(strict_types=1);

namespace Domain\Analys\Models;

use Domain\Analys\Enums\Analys as AnalysEnum;
use Domain\User\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $user_id
 * @property AnalysEnum $analys_id
 * @property Analys $analys_name
 * @property float $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Domain\Analys\Factories\UserAnalysFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAnalys newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAnalys newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAnalys query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAnalys whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAnalys whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAnalys whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAnalys whereAnalysId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAnalys whereAnalysName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAnalys whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserAnalys whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class UserAnalys extends Model
{
    /** @use HasFactory<\Domain\Analys\Factories\UserAnalysFactory> */
    use HasFactory;
    use HasUuids;

    protected $table = 'user_analys';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'analys_id',
        'analys_name',
        'data',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'analys_id' => AnalysEnum::class,
            'analys_name' => 'string',
            'data' => 'float',
        ];
    }

    /**
     * @return BelongsTo<Analys, $this>
     */
    public function analys(): BelongsTo
    {
        return $this->belongsTo(Analys::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
