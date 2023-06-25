<?php

declare(strict_types=1);

namespace App\Model;


/**
 * @property int $id
 * @property string $openid
 * @property \Carbon\Carbon $created_at
 */
class User extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'users';

    /**
     * The attributes that are mass assignable.
     */
    protected array $guarded = ['id'];

    public const UPDATED_AT = null;

}
