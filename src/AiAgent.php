<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents an AI Agent in the Enlivy API.
 *
 * @property string $id
 * @property string $slug
 * @property string $type Either 'standard' or 'pipeline'
 * @property array $title_lang_map
 * @property array $description_lang_map
 * @property bool $has_input_target
 * @property string|null $input_target_entity
 * @property mixed $input_data
 * @property array|null $entity_schemas Admin only
 * @property string|null $deleted_by_user_id Admin only
 * @property string|null $deleted_at Admin only
 * @property string $created_at Admin only
 * @property string $updated_at Admin only
 */
class AiAgent extends ApiResource
{
    public const ?string OBJECT_NAME = 'ai_agent';
}
