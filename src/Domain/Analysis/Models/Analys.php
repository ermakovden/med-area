<?php

declare(strict_types=1);

namespace Domain\Analysis\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $id
 * @property string $name
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analys newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analys newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analys query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analys whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analys whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analys whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analys whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analys whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Analys extends Model
{
    protected $table = 'analysis';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'name',
        'description',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'int',
        ];
    }
}
