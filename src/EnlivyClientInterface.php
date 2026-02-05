<?php

declare(strict_types=1);

namespace Enlivy;

interface EnlivyClientInterface
{
    public function getRequestor(): ApiRequestor;

    public function getOrganizationId(): ?string;

    public function getApiBase(): string;
}
