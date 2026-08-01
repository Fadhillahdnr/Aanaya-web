<?php

namespace Tests\Feature;

use App\Jobs\DeleteCloudinaryAsset;
use App\Models\Article;
use App\Models\Gallery;
use App\Models\Media;
use App\Models\Music;
use App\Models\Product;
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
        $productImage = $this->readyMedia($admin, 'image');

        $this->actingAs($admin)->post('/admin/products/store', [
            'name' => 'Dream Shirt',
            'price' => 150000,
            'stock' => 10,
            'uploaded_media' => ['image' => $productImage->id],
        ])->assertRedirect('/admin/products');

        $product = Product::firstOrFail();
        $this->assertSame($productImage->secure_url, $product->image);
        $this->assertSame($product->id, $productImage->fresh()->mediable_id);

        $galleryImage = $this->readyMedia($admin, 'image');
        $this->actingAs($admin)->post('/admin/gallery/store', [
            'title' => 'Live',
            'uploaded_media' => ['image' => $galleryImage->id],
        ])->assertRedirect('/admin/gallery');

        $this->assertSame($galleryImage->secure_url, Gallery::firstOrFail()->image);
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
