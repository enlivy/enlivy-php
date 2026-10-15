<?php

declare(strict_types=1);

namespace Enlivy\Service;

use Enlivy\EnlivyObject;
use Enlivy\Util\RequestOptions;

class AuthenticationService extends AbstractService
{
    public function logout(?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('GET', '/authentication/logout', null, $opts);
    }
}
