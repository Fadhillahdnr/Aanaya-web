<?php

namespace Tests\Feature;

use App\Jobs\DeleteCloudinaryAsset;
use App\Models\Article;
use App\Models\Gallery;
use App\Models\Media;
use App\Models\Music;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class DirectMediaCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_and_gallery_store_only_media_metadata(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $productImage = $this->readyMedia($admin, 'product_images');
        $secondProductImage = $this->readyMedia($admin, 'product_images');

        $this->actingAs($admin)->post('/admin/products/store', [
            'name' => 'Dream Shirt',
            'price' => 150000,
            'stock' => 10,
            'uploaded_media' => ['product_images' => [$productImage->id, $secondProductImage->id]],
        ])->assertRedirect('/admin/products');

        $product = Product::firstOrFail();
        $this->assertSame($productImage->secure_url, $product->image);
        $this->assertSame($product->id, $productImage->fresh()->mediable_id);
        $this->assertSame($product->id, $secondProductImage->fresh()->mediable_id);
        $this->assertCount(2, $product->galleryMedia);

        $galleryImage = $this->readyMedia($admin, 'image');
        $this->actingAs($admin)->post('/admin/gallery/store', [
            'title' => 'Live',
            'uploaded_media' => ['image' => $galleryImage->id],
        ])->assertRedirect('/admin/gallery');

        $this->assertSame($galleryImage->secure_url, Gallery::firstOrFail()->image);
    }

    public function test_admin_can_create_product_variants_with_independent_stock_price_and_photo(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $productImage = $this->readyMedia($admin, 'product_images');
        $variantImage = $this->readyMedia($admin, 'product_variant_image');

        $this->actingAs($admin)->post('/admin/products/store', [
            'name' => 'Dream Bracelet',
            'price' => 75000,
            'stock' => 0,
            'has_variants' => 1,
            'variant_label' => 'Model',
            'variants' => [
                ['name' => 'Unfold', 'sku' => 'BR-UNFOLD', 'price' => 85000, 'stock' => 2, 'is_active' => 1],
                ['name' => 'Hanayo', 'stock' => 3, 'is_active' => 1],
            ],
            'uploaded_media' => [
                'product_images' => [$productImage->id],
                'variants' => [0 => ['image' => $variantImage->id]],
            ],
        ])->assertRedirect('/admin/products');

        $product = Product::with('variants')->firstOrFail();
        $unfold = $product->variants->firstWhere('name', 'Unfold');

        $this->assertSame('Model', $product->variant_label);
        $this->assertSame(5, $product->stock);
        $this->assertCount(2, $product->variants);
        $this->assertSame('85000.00', $unfold->price);
        $this->assertSame($variantImage->secure_url, $unfold->image);
        $this->assertSame(ProductVariant::class, $variantImage->fresh()->mediable_type);
        $this->assertSame($unfold->id, $variantImage->fresh()->mediable_id);
    }

    public function test_music_store_claims_cover_and_audio(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cover = $this->readyMedia($admin, 'cover_image');
        $audio = $this->readyMedia($admin, 'audio_file', 'audio', 'video', 'audio/mpeg');

        $this->actingAs($admin)->post('/admin/music/store', [
            'title' => 'New Song',
            'artist' => 'Aanaya',
            'uploaded_media' => ['cover_image' => $cover->id, 'audio_file' => $audio->id],
        ])->assertRedirect(route('admin.music'));

        $music = Music::firstOrFail();
        $this->assertSame($cover->secure_url, $music->cover_image);
        $this->assertSame($audio->secure_url, $music->audio_file);
        $this->assertCount(2, $music->media);
    }

    public function test_product_gallery_can_keep_delete_and_add_photos(): void
    {
        Queue::fake();
        $admin = User::factory()->create(['role' => 'admin']);
        $first = $this->readyMedia($admin, 'product_images');
        $second = $this->readyMedia($admin, 'product_images');

        $this->actingAs($admin)->post('/admin/products/store', [
            'name' => 'Gallery Hoodie',
            'price' => 250000,
            'stock' => 5,
            'uploaded_media' => ['product_images' => [$first->id, $second->id]],
        ]);

        $product = Product::with('galleryMedia')->firstOrFail();
        $third = $this->readyMedia($admin, 'product_images');

        $this->actingAs($admin)->put("/admin/products/{$product->id}", [
            'name' => $product->name,
            'price' => $product->price,
            'stock' => $product->stock,
            'delete_media_ids' => [$first->id],
            'uploaded_media' => ['product_images' => [$third->id]],
        ])->assertRedirect('/admin/products');

        $product->refresh()->load('galleryMedia');

        $this->assertCount(2, $product->galleryMedia);
        $this->assertSame([$second->id, $third->id], $product->galleryMedia->pluck('id')->all());
        $this->assertSame($second->secure_url, $product->image);
        $this->assertSoftDeleted('media', ['id' => $first->id]);
        Queue::assertPushed(DeleteCloudinaryAsset::class, 1);
    }

    public function test_product_detail_renders_all_gallery_photos(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $first = $this->readyMedia($admin, 'product_images');
        $second = $this->readyMedia($admin, 'product_images');

        $this->actingAs($admin)->post('/admin/products/store', [
            'name' => 'Dream Gallery Shirt',
            'price' => 175000,
            'stock' => 8,
            'uploaded_media' => ['product_images' => [$first->id, $second->id]],
        ]);

        $product = Product::firstOrFail();

        $response = $this->get(route('merchandise.show', $product->slug));

        $response
            ->assertOk()
            ->assertSee('data-product-gallery', false)
            ->assertSee('data-product-gallery-previous', false)
            ->assertSee('data-product-lightbox', false);

        $this->assertSame(2, substr_count($response->getContent(), 'data-product-gallery-slide'));
    }

    public function test_product_gallery_accepts_video_and_renders_a_lazy_player(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cover = $this->readyMedia($admin, 'product_images');
        $video = $this->readyMedia($admin, 'product_images', 'video', 'video', 'video/mp4');
        $video->update(['thumbnail_url' => 'https://res.cloudinary.com/demo/image/upload/product-video-cover.jpg']);

        $this->actingAs($admin)->post('/admin/products/store', [
            'name' => 'Bracelet With Video',
            'price' => 85000,
            'stock' => 4,
            'uploaded_media' => ['product_images' => [$cover->id, $video->id]],
        ])->assertRedirect('/admin/products');

        $product = Product::firstOrFail();
        $this->assertSame($cover->secure_url, $product->image);

        $this->get(route('merchandise.show', $product->slug))
            ->assertOk()
            ->assertSee('data-media-type="video"', false)
            ->assertSee('data-product-video-play', false)
            ->assertSee('Play Bracelet With Video product video', false)
            ->assertSee('preload="metadata"', false)
            ->assertSee('controls', false)
            ->assertDontSee('autoplay', false);
    }

    public function test_product_creation_rejects_video_without_a_cover_photo(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $video = $this->readyMedia($admin, 'product_images', 'video', 'video', 'video/mp4');

        $this->actingAs($admin)
            ->from('/admin/products/create')
            ->post('/admin/products/store', [
                'name' => 'Video Only Product',
                'price' => 85000,
                'stock' => 4,
                'uploaded_media' => ['product_images' => [$video->id]],
            ])
            ->assertRedirect('/admin/products/create')
            ->assertSessionHasErrors('product_images');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_product_gallery_rejects_more_than_eight_total_photos(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $existing = collect(range(1, 2))->map(fn () => $this->readyMedia($admin, 'product_images'));

        $this->actingAs($admin)->post('/admin/products/store', [
            'name' => 'Limited Gallery Product',
            'price' => 125000,
            'stock' => 3,
            'uploaded_media' => ['product_images' => $existing->pluck('id')->all()],
        ]);

        $product = Product::firstOrFail();
        $additional = collect(range(1, 7))->map(fn () => $this->readyMedia($admin, 'product_images'));

        $this->actingAs($admin)
            ->from("/admin/products/{$product->id}/edit")
            ->put("/admin/products/{$product->id}", [
                'name' => $product->name,
                'price' => $product->price,
                'stock' => $product->stock,
                'uploaded_media' => ['product_images' => $additional->pluck('id')->all()],
            ])
            ->assertRedirect("/admin/products/{$product->id}/edit")
            ->assertSessionHasErrors('product_images');

        $this->assertCount(2, $product->fresh()->galleryMedia);
    }

    public function test_article_store_claims_thumbnail_and_ordered_image_block(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $thumbnail = $this->readyMedia($admin, 'thumbnail');
        $blockImage = $this->readyMedia($admin, 'article_block_image');
        $secondBlockImage = $this->readyMedia($admin, 'article_block_image');

        $this->actingAs($admin)->post('/admin/articles/store', [
            'category' => 'article',
            'title' => 'Behind The Song',
            'uploaded_media' => ['thumbnail' => $thumbnail->id],
            'blocks' => [
                ['type' => 'text', 'content' => 'Opening'],
                ['type' => 'image', 'media_ids' => [$blockImage->id, $secondBlockImage->id]],
                ['type' => 'text', 'content' => 'Closing'],
            ],
        ])->assertRedirect('/admin/articles');

        $article = Article::with('blocks')->firstOrFail();
        $this->assertCount(4, $article->blocks);
        $this->assertSame($blockImage->secure_url, $article->blocks[1]->image);
        $this->assertSame($secondBlockImage->secure_url, $article->blocks[2]->image);
        $this->assertSame('Closing', $article->blocks[3]->content);
        $this->assertSame([0, 1, 2, 3], $article->blocks->pluck('sort_order')->all());
    }

    public function test_comic_store_claims_multiple_direct_uploads(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $thumbnail = $this->readyMedia($admin, 'thumbnail');
        $pageOne = $this->readyMedia($admin, 'comic_images');
        $pageTwo = $this->readyMedia($admin, 'comic_images');

        $this->actingAs($admin)->post('/admin/articles/store', [
            'category' => 'comic',
            'title' => 'Aanaya Comic',
            'uploaded_media' => [
                'thumbnail' => $thumbnail->id,
                'comic_images' => [$pageOne->id, $pageTwo->id],
            ],
        ])->assertRedirect('/admin/articles');

        $article = Article::with('comicImages')->firstOrFail();
        $this->assertCount(2, $article->comicImages);
        $this->assertSame([0, 1], $article->comicImages->pluck('sort_order')->all());
    }

    public function test_article_images_can_be_kept_replaced_and_deleted_individually(): void
    {
        Queue::fake();
        $admin = User::factory()->create(['role' => 'admin']);
        $thumbnail = $this->readyMedia($admin, 'thumbnail');
        $kept = $this->readyMedia($admin, 'article_block_image');
        $replaced = $this->readyMedia($admin, 'article_block_image');
        $deleted = $this->readyMedia($admin, 'article_block_image');

        $this->actingAs($admin)->post('/admin/articles/store', [
            'category' => 'article',
            'title' => 'Editable Article',
            'uploaded_media' => ['thumbnail' => $thumbnail->id],
            'blocks' => [
                ['type' => 'image', 'media_id' => $kept->id],
                ['type' => 'image', 'media_id' => $replaced->id],
                ['type' => 'image', 'media_id' => $deleted->id],
            ],
        ]);

        $article = Article::with('blocks')->firstOrFail();
        [$keepBlock, $replaceBlock, $deleteBlock] = $article->blocks->all();
        $replacement = $this->readyMedia($admin, 'article_block_image');

        $this->actingAs($admin)->put("/admin/articles/{$article->id}", [
            'category' => 'article',
            'title' => $article->title,
            'blocks' => [
                ['id' => $keepBlock->id, 'type' => 'image', 'action' => 'keep'],
                ['id' => $replaceBlock->id, 'type' => 'image', 'action' => 'replace', 'media_id' => $replacement->id],
                ['id' => $deleteBlock->id, 'type' => 'image', 'action' => 'delete'],
            ],
        ])->assertRedirect('/admin/articles');

        $images = $article->fresh()->blocks()->orderBy('sort_order')->get();
        $this->assertCount(2, $images);
        $this->assertSame($kept->secure_url, $images[0]->image);
        $this->assertSame($replacement->secure_url, $images[1]->image);
        $this->assertDatabaseMissing('article_blocks', ['id' => $deleteBlock->id]);
        Queue::assertPushed(DeleteCloudinaryAsset::class, 2);
    }

    public function test_comic_panels_can_be_kept_replaced_and_deleted_individually(): void
    {
        Queue::fake();
        $admin = User::factory()->create(['role' => 'admin']);
        $thumbnail = $this->readyMedia($admin, 'thumbnail');
        $first = $this->readyMedia($admin, 'comic_images');
        $second = $this->readyMedia($admin, 'comic_images');
        $third = $this->readyMedia($admin, 'comic_images');

        $this->actingAs($admin)->post('/admin/articles/store', [
            'category' => 'comic',
            'title' => 'Editable Comic',
            'uploaded_media' => [
                'thumbnail' => $thumbnail->id,
                'comic_images' => [$first->id, $second->id, $third->id],
            ],
        ]);

        $article = Article::with('comicImages')->firstOrFail();
        [$keepPanel, $replacePanel, $deletePanel] = $article->comicImages->all();
        $replacement = $this->readyMedia($admin, 'comic_images');

        $this->actingAs($admin)->put("/admin/articles/{$article->id}", [
            'category' => 'comic',
            'title' => $article->title,
            'comic_actions' => [
                $keepPanel->id => 'keep',
                $replacePanel->id => 'replace',
                $deletePanel->id => 'delete',
            ],
            'uploaded_media' => [
                'comic_replacements' => [$replacePanel->id => $replacement->id],
            ],
        ])->assertRedirect('/admin/articles');

        $panels = $article->fresh()->comicImages()->orderBy('sort_order')->get();
        $this->assertCount(2, $panels);
        $this->assertSame($first->secure_url, $panels[0]->image);
        $this->assertSame($replacement->secure_url, $panels[1]->image);
        $this->assertDatabaseMissing('comic_images', ['id' => $deletePanel->id]);
        Queue::assertPushed(DeleteCloudinaryAsset::class, 2);
    }

    private function readyMedia(
        User $user,
        string $purpose,
        string $mediaType = 'image',
        string $resourceType = 'image',
        string $mimeType = 'image/jpeg',
    ): Media {
        $id = (string) Str::ulid();

        return Media::create([
            'uploaded_by' => $user->id,
            'public_id' => "development/tests/{$id}",
            'resource_type' => $resourceType,
            'media_type' => $mediaType,
            'purpose' => $purpose,
            'original_name' => "{$id}.jpg",
            'mime_type' => $mimeType,
            'size_bytes' => 1000,
            'secure_url' => "https://res.cloudinary.com/demo/{$resourceType}/upload/{$id}",
            'status' => 'ready',
        ]);
    }
}
