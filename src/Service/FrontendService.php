<?php

declare(strict_types=1);

namespace Enlivy\Service;

use Enlivy\EnlivyObject;
use Enlivy\Util\RequestOptions;

class FrontendService extends AbstractService
{
    public function all(?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('GET', '/frontend', null, $opts);
    }

    public function langMap(?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('GET', '/frontend/lang-map', null, $opts);
    }

    public function langMapByKey(string $key, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('GET', "/frontend/lang-map/{$key}", null, $opts);
    }

    public function countries(?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('GET', '/frontend/countries', null, $opts);
    }

    public function currencies(?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('GET', '/frontend/currencies', null, $opts);
    }

    public function iso3166(string $countryCode, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('GET', "/frontend/iso3166/{$countryCode}", null, $opts);
    }
}
