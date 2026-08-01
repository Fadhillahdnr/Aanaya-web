<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class PublicContentPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_article_page_uses_pagination(): void
    {
        $response = $this->get('/articles');

        $response->assertOk();
        $this->assertInstanceOf(LengthAwarePaginator::class, $response->viewData('articles'));
    }

    public function test_public_gallery_page_uses_pagination(): void
    {
        $response = $this->get('/gallery');

        $response->assertOk();
        $this->assertInstanceOf(LengthAwarePaginator::class, $response->viewData('galleries'));
    }

    public function test_public_merchandise_page_uses_pagination(): void
    {
        $response = $this->get('/merchandise');

        $response->assertOk();
        $this->assertInstanceOf(LengthAwarePaginator::class, $response->viewData('products'));
    }
}
