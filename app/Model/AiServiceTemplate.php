<?php

declare(strict_types=1);

namespace App\Model;



/**
 * @property int $id 
 * @property string $name 
 * @property string $template 
 * @property string $ask_template 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class AiServiceTemplate extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'ai_service_templates';

    /**
     * The attributes that are mass assignable.
     */
    protected array $guarded = ['id'];

}
