<?php

declare(strict_types=1);

namespace Enlivy\Service;

use Enlivy\EnlivyObject;
use Enlivy\User;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

class UserService extends AbstractService
{
    use HasIncludes;

    protected const ?string RESOURCE_CLASS = User::class;

    public const array AVAILABLE_INCLUDES = [
        'organizations',
    ];

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): User
    {
        $this->validateIncludes($params);

        /** @var User */
        return $this->request('GET', "/users/{$id}", $params, $opts);
    }

    /**
     * Accepts `username`, `first_name`, `last_name`, `name`, `locale`, `country_code` and
     * `timezone`; any other field is ignored. Email, phone and password are changed in the
     * Enlivy app.
     */
    public function update(string $id, array $params, ?RequestOptions $opts = null): User
    {
        $this->validateIncludes($params);

        /** @var User */
        return $this->request('PUT', "/users/{$id}", $params, $opts);
    }

    public function sendSummaryEmail(string $id, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('GET', "/users/{$id}/summary-email", null, $opts, EnlivyObject::class);
    }
}
