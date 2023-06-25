<?php

declare(strict_types=1);

namespace App\Model;


/**
 * @property int $id
 * @property int $user_id
 * @property string $template_id
 * @property string $question
 * @property string $content
 * @property \Carbon\Carbon $created_at
 */
class AiServiceRecord extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'ai_service_records';

    /**
     * The attributes that are mass assignable.
     */
    protected array $guarded = ['id'];

    public const UPDATED_AT = null;

}
