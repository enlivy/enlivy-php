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

    public function sockets(?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('GET', '/frontend/sockets', null, $opts);
    }

    /**
     * The per-country information schema: which person and organization fields that country wants.
     */
    public function informationSchema(string $countryCode, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('GET', "/frontend/information-schema/{$countryCode}", $params, $opts);
    }

    /**
     * The timezone list the API accepts. Public, like the rest of the bootstrap data.
     */
    public function timezones(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('GET', '/misc/timezones', $params, $opts);
    }

    /**
     * Where the support widget lives and who is asking, for a first-party surface mounting it.
     *
     * Answers nulls when no inbox is serving, which is the signal to mount nothing.
     */
    public function supportWidgetToken(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('GET', '/misc/support-widget-token', $params, $opts);
    }
}
