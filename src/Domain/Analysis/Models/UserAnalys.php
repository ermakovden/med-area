<?php

declare(strict_types=1);

namespace Domain\Analysis\Models;

use Domain\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property string $id
 * @property string $user_id
 * @property integer $analys_id
 * @property string $analys_name
 * @property float $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
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
            'analys_id' => 'int',
            'data' => 'float',
        ];
    }

    /**
     * @return HasOne<Analys, $this>
     */
    public function analys(): HasOne
    {
        return $this->hasOne(Analys::class);
    }

    /**
     * @return HasOne<User, $this>
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
