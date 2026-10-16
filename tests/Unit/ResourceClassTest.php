<?php

declare(strict_types=1);

namespace Enlivy\Tests\Unit;

use Enlivy\ApiResource;
use Enlivy\EnlivyClient;
use Enlivy\Organization\ProjectMember;
use Enlivy\Organization\ReportSchemaField;
use Enlivy\Organization\TaxFilingPeriodPayment;
use Enlivy\Service\AbstractService;
use Enlivy\Tests\Mock\MockHttpClient;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;

final class ResourceClassTest extends TestCase
{
    // a response carries no `object` key, so a service declares which class it builds
    public function testEveryMethodReturningAResourceKnowsItsClass(): void
    {
        $missing = [];

        foreach ($this->serviceClasses() as $service) {
            $reflection = new ReflectionClass($service);
            $declared = $reflection->getReflectionConstant('RESOURCE_CLASS')?->getValue();

            foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
                $type = $method->getReturnType();
                if (!$type instanceof ReflectionNamedType || $type->isBuiltin() || !is_subclass_of($type->getName(), ApiResource::class)) {
                    continue;
                }
                if ($declared === $type->getName() || str_contains($this->body($method), $this->shortName($type->getName()) . '::class')) {
                    continue;
                }
                $missing[] = $service . '::' . $method->getName() . '(): ' . $type->getName();
            }
        }

        $this->assertSame([], $missing);
    }

    /** @return iterable<string, array{string, class-string}> */
    public static function nestedServices(): iterable
    {
        yield 'report schema field' => ['reportSchemaFields', ReportSchemaField::class];
        yield 'project member' => ['projectMembers', ProjectMember::class];
        yield 'tax filing period payment' => ['taxFilingPeriodPayments', TaxFilingPeriodPayment::class];
    }

    #[DataProvider('nestedServices')]
    public function testANestedResourceIsTypedWithoutAnObjectKey(string $service, string $class): void
    {
        $http = new MockHttpClient();
        $http->addResponse(201, ['data' => ['id' => 'org_child_1', 'title' => 'Report']]);
        $client = new EnlivyClient(['api_key' => '1|test_token', 'organization_id' => 'org_default', 'http_client' => $http]);

        $created = $client->{$service}->create('org_parent_1', ['title' => 'Report']);

        $this->assertInstanceOf($class, $created);
        $this->assertSame('org_child_1', $created->id);
    }

    /** @return list<class-string<AbstractService>> */
    private function serviceClasses(): array
    {
        $root = dirname(__DIR__, 2) . '/src/Service';
        $classes = [];

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root)) as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }
            $class = 'Enlivy\\Service\\' . str_replace(['/', '.php'], ['\\', ''], substr($file->getPathname(), strlen($root) + 1));
            if (class_exists($class) && is_subclass_of($class, AbstractService::class) && !(new ReflectionClass($class))->isAbstract()) {
                $classes[] = $class;
            }
        }

        return $classes;
    }

    private function body(ReflectionMethod $method): string
    {
        $lines = file((string) $method->getFileName());

        return implode('', array_slice($lines, $method->getStartLine() - 1, $method->getEndLine() - $method->getStartLine() + 1));
    }

    private function shortName(string $class): string
    {
        return substr($class, strrpos($class, '\\') + 1);
    }
}
