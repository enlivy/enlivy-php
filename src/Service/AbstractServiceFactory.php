<?php

declare(strict_types=1);

namespace Enlivy\Service;

use Enlivy\EnlivyClientInterface;

abstract class AbstractServiceFactory
{
    /** @var array<string, AbstractService> */
    private array $services = [];

    public function __construct(
        private readonly EnlivyClientInterface $client,
    ) {}

    public function getService(string $name): AbstractService
    {
        if (!isset($this->services[$name])) {
            $map = $this->getServiceMap();

            if (!isset($map[$name])) {
                throw new \InvalidArgumentException("Unknown service: {$name}");
            }

            $class = $map[$name];
            $this->services[$name] = new $class($this->client);
        }

        return $this->services[$name];
    }

    /**
     * @return array<string, class-string<AbstractService>>
     */
    abstract protected function getServiceMap(): array;
}
