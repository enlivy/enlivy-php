<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents an AI Agent in the Enlivy API.
 *
 * @property string $id
 * @property string $name
 * @property string $description
 * @property string $type
 * @property string $model
 * @property string $prompt
 * @property array $settings
 * @property string $created_at
 * @property string $updated_at
 */
class AiAgent extends ApiResource
{
    public const ?string OBJECT_NAME = 'ai_agent';
}
