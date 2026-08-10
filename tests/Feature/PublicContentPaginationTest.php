<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ArticleBlock;
use App\Models\User;
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

    public function test_article_detail_renders_server_side_cinematic_story_content(): void
    {
        $author = User::factory()->create(['name' => 'Aanaya Author']);
        $article = Article::create([
            'title' => 'A Quiet Story',
            'slug' => 'a-quiet-story',
            'category' => 'article',
            'thumbnail' => 'https://example.com/cover.jpg',
            'content' => '',
            'author_id' => $author->id,
            'published_at' => now(),
        ]);

        ArticleBlock::create([
            'article_id' => $article->id,
            'type' => 'text',
            'content' => 'The opening remains readable without JavaScript.',
            'sort_order' => 0,
        ]);
        ArticleBlock::create([
            'article_id' => $article->id,
            'type' => 'image',
            'image' => 'https://example.com/chapter.jpg',
            'sort_order' => 1,
        ]);
        ArticleBlock::create([
            'article_id' => $article->id,
            'type' => 'text',
            'content' => 'The next chapter is still server rendered.',
            'sort_order' => 2,
        ]);

        $response = $this->get(route('articles.show', $article->slug));

        $response->assertOk();
        $response->assertSee('data-article-experience', false);
        $response->assertSee('data-scrub-video', false);
        $response->assertSee('The opening remains readable without JavaScript.');
        $response->assertSee('The next chapter is still server rendered.');
        $response->assertSee('data-scene="reading"', false);
        $response->assertSee('data-scene="approach-book"', false);
        $response->assertSee('data-scene="enter-book"', false);
        $response->assertSee('data-scene="ending"', false);
        $response->assertSee('data-article-sound', false);
        $response->assertSee('End of story');
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
