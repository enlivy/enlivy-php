<?php

declare(strict_types=1);

namespace Enlivy\Tests\Integration\View;

use Enlivy\Collection;
use Enlivy\Organization\Product;
use Enlivy\Tests\Integration\IntegrationTestCase;

/**
 * Integration tests for Product-related endpoints.
 */
class ProductTest extends IntegrationTestCase
{
    public function testListProducts(): void
    {
        $products = $this->getClient()->products->list();

        $this->assertInstanceOf(Collection::class, $products);
        $this->assertIsArray($products->data);

        if (count($products->data) > 0) {
            $product = $products->data[0];
            $this->assertInstanceOf(Product::class, $product);
            $this->assertIdPrefix('org_prod_', $product->id);
            $this->assertNotNull($product->organization_id);
        }
    }

    public function testListProductsWithPagination(): void
    {
        $products = $this->getClient()->products->list(['page' => 1]);

        $this->assertInstanceOf(Collection::class, $products);
        $this->assertNotNull($products->meta);
    }

    public function testRetrieveProduct(): void
    {
        $products = $this->getClient()->products->list(['per_page' => 1]);

        if (count($products->data) === 0) {
            $this->markTestSkipped('No products available for testing');
        }

        $productId = $products->data[0]->id;
        $product = $this->getClient()->products->retrieve($productId);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals($productId, $product->id);
        $this->assertNotNull($product->name_lang_map);
    }
}
