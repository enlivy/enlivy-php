<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $entity
 * @property string $liveness
 * @property mixed $item
 */
class Connection extends ApiResource
{
    public const ?string OBJECT_NAME = 'connection';
}
