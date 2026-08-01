<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleBlock;
use App\Models\ComicImage;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ArticleController extends Controller
{
    public function index()
    {
        return view('admin.articles', ['articles' => Article::latest()->get()]);
    }

    public function create()
    {
        return view('admin.article-create');
    }

    public function edit($id)
    {
        return view('admin.article-edit', [
            'article' => Article::with(['comicImages', 'blocks'])->findOrFail($id),
        ]);
    }

    public function store(Request $request, MediaService $mediaService)
    {
        $this->validateArticle($request, true);

        return DB::transaction(function () use ($request, $mediaService) {
            $thumbnail = $mediaService->fromRequest($request, 'thumbnail', true);
            $article = Article::create([
                'category' => $request->category,
                'title' => $request->title,
                'slug' => Str::slug($request->title.'-'.time()),
                'thumbnail' => $thumbnail->secure_url,
                'thumbnail_public_id' => $thumbnail->public_id,
                'author_id' => Auth::id(),
                'published_at' => $request->published_at,
                'content' => '',
                'description' => $request->category === 'comic' ? $request->description : null,
            ]);
            $mediaService->claim($thumbnail, $article, 'thumbnail');

            if ($article->category === 'article') {
                $this->createArticleBlocks($request->input('blocks', []), $article, $mediaService);
            } else {
                $this->appendComicImages(
                    data_get($request->input('uploaded_media', []), 'comic_images', []),
                    $article,
                    $mediaService,
                );
            }

            return redirect('/admin/articles')->with('success', ucfirst($article->category).' uploaded successfully ✨');
        });
    }

    public function update(Request $request, $id, MediaService $mediaService)
    {
        $article = Article::with(['blocks', 'comicImages'])->findOrFail($id);
        $this->validateArticle($request, false);

        return DB::transaction(function () use ($request, $article, $mediaService) {
            $oldCategory = $article->category;
            $oldThumbnail = $article->thumbnail_public_id;
            $thumbnail = $mediaService->fromRequest($request, 'thumbnail');

            $article->fill([
                'category' => $request->category,
                'title' => $request->title,
                'description' => $request->category === 'comic' ? $request->description : null,
                'published_at' => $request->published_at,
            ]);
            if ($article->isDirty('title')) {
                $article->slug = Str::slug($request->title.'-'.time());
            }
            if ($thumbnail) {
                $article->thumbnail = $thumbnail->secure_url;
                $article->thumbnail_public_id = $thumbnail->public_id;
            }
            $article->save();

            if ($thumbnail) {
                $mediaService->claim($thumbnail, $article, 'thumbnail');
                $mediaService->queueDelete($oldThumbnail);
            }

            if ($oldCategory === 'comic' && $article->category === 'article') {
                foreach ($article->comicImages as $comicImage) {
                    $mediaService->queueDelete($comicImage->public_id);
                    $comicImage->delete();
                }
            }
            if ($oldCategory === 'article' && $article->category === 'comic') {
                foreach ($article->blocks as $block) {
                    $mediaService->queueDelete($block->image_public_id);
                    $block->delete();
                }
            }

            if ($article->category === 'article') {
                $this->syncArticleBlocks($request->input('blocks', []), $article, $mediaService);
            } else {
                $this->syncComicImages($request, $article, $mediaService);
                $this->appendComicImages(
                    data_get($request->input('uploaded_media', []), 'comic_images', []),
                    $article,
                    $mediaService,
                );
            }

            return redirect('/admin/articles')->with('success', ucfirst($article->category).' updated successfully ✨');
        });
    }

    public function destroy($id, MediaService $mediaService)
    {
        $article = Article::with(['comicImages', 'blocks'])->findOrFail($id);

        DB::transaction(function () use ($article, $mediaService) {
            foreach ($article->blocks as $block) {
                $mediaService->queueDelete($block->image_public_id);
            }
            foreach ($article->comicImages as $comicImage) {
                $mediaService->queueDelete($comicImage->public_id);
            }
            $mediaService->queueDelete($article->thumbnail_public_id);
            $article->delete();
        });

        return redirect('/admin/articles')->with('success', ucfirst($article->category).' deleted successfully ✨');
    }

    public function userIndex()
    {
        return view('user.articles', ['articles' => Article::latest()->get()]);
    }

    public function show($slug)
    {
        $article = Article::with(['comicImages', 'blocks', 'author'])->where('slug', $slug)->firstOrFail();
        if ($article->category === 'comic') {
            return view('user.comic-show', ['comic' => $article]);
        }

        return view('user.article-show', compact('article'));
    }

    private function validateArticle(Request $request, bool $creating): void
    {
        $request->validate([
            'category' => 'required|in:article,comic',
            'title' => 'required|string|max:255',
            'uploaded_media.thumbnail' => [$creating ? 'required' : 'nullable', 'string', 'exists:media,id'],
            'published_at' => 'nullable|date',
            'blocks' => 'required_if:category,article|array',
            'blocks.*.id' => 'nullable|integer',
            'blocks.*.type' => 'required_with:blocks|in:text,image',
            'blocks.*.content' => 'nullable|string',
            'blocks.*.media_id' => 'nullable|string|exists:media,id',
            'blocks.*.media_ids' => 'nullable|array',
            'blocks.*.media_ids.*' => 'string|exists:media,id',
            'blocks.*.action' => 'nullable|in:keep,replace,delete',
            'description' => 'nullable|string',
            'comic_actions' => 'nullable|array',
            'comic_actions.*' => 'nullable|in:keep,replace,delete',
            'uploaded_media.comic_images' => 'nullable|array',
            'uploaded_media.comic_images.*' => 'string|exists:media,id',
            'uploaded_media.comic_replacements' => 'nullable|array',
            'uploaded_media.comic_replacements.*' => 'string|exists:media,id',
        ]);

        if ($creating && $request->category === 'comic' && empty(data_get($request->input('uploaded_media', []), 'comic_images'))) {
            throw ValidationException::withMessages(['comic_images' => 'Minimal satu gambar comic wajib diunggah.']);
        }
    }

    private function createArticleBlocks(array $blocks, Article $article, MediaService $mediaService): void
    {
        $order = 0;
        foreach (array_values($blocks) as $data) {
            if (($data['type'] ?? null) === 'text' && trim($data['content'] ?? '') !== '') {
                ArticleBlock::create([
                    'article_id' => $article->id,
                    'type' => 'text',
                    'content' => $data['content'],
                    'sort_order' => $order,
                ]);
                $order++;
            }
            if (($data['type'] ?? null) === 'image') {
                foreach ($this->blockMediaIds($data) as $mediaId) {
                    $media = $mediaService->readyOwnedMedia($mediaId);
                    ArticleBlock::create([
                        'article_id' => $article->id,
                        'type' => 'image',
                        'image' => $media->secure_url,
                        'image_public_id' => $media->public_id,
                        'sort_order' => $order,
                    ]);
                    $mediaService->claim($media, $article, 'article_block_image', $order + 1);
                    $order++;
                }
            }
        }
    }

    private function syncArticleBlocks(array $blocks, Article $article, MediaService $mediaService): void
    {
        $submitted = [];
        $order = 0;
        foreach (array_values($blocks) as $data) {
            $block = ! empty($data['id'])
                ? ArticleBlock::where('article_id', $article->id)->find($data['id'])
                : null;

            if ($block && ($data['action'] ?? 'keep') === 'delete') {
                $mediaService->queueDelete($block->image_public_id);
                $block->delete();

                continue;
            }

            if (! $block) {
                if (($data['type'] ?? null) === 'text' && trim($data['content'] ?? '') !== '') {
                    $new = ArticleBlock::create([
                        'article_id' => $article->id,
                        'type' => 'text',
                        'content' => $data['content'],
                        'sort_order' => $order++,
                    ]);
                    $submitted[] = $new->id;
                }
                if (($data['type'] ?? null) === 'image') {
                    foreach ($this->blockMediaIds($data) as $mediaId) {
                        $media = $mediaService->readyOwnedMedia($mediaId);
                        $new = ArticleBlock::create([
                            'article_id' => $article->id,
                            'type' => 'image',
                            'image' => $media->secure_url,
                            'image_public_id' => $media->public_id,
                            'sort_order' => $order,
                        ]);
                        $submitted[] = $new->id;
                        $mediaService->claim($media, $article, 'article_block_image', $order + 1);
                        $order++;
                    }
                }

                continue;
            }

            $submitted[] = $block->id;
            $block->sort_order = $order++;
            if (($data['type'] ?? null) === 'text') {
                $mediaService->queueDelete($block->image_public_id);
                $block->fill(['type' => 'text', 'content' => $data['content'] ?? null, 'image' => null, 'image_public_id' => null]);
            } else {
                $block->type = 'image';
                $mediaIds = $this->blockMediaIds($data);
                if (($data['action'] ?? 'keep') === 'replace' && $mediaIds === []) {
                    throw ValidationException::withMessages([
                        'blocks' => 'Pilih gambar pengganti untuk blok yang ingin diganti.',
                    ]);
                }
                if ($mediaIds !== []) {
                    $media = $mediaService->readyOwnedMedia(array_shift($mediaIds));
                    $mediaService->queueDelete($block->image_public_id);
                    $block->image = $media->secure_url;
                    $block->image_public_id = $media->public_id;
                    $mediaService->claim($media, $article, 'article_block_image', $block->sort_order + 1);

                    foreach ($mediaIds as $mediaId) {
                        $extraMedia = $mediaService->readyOwnedMedia($mediaId);
                        $extra = ArticleBlock::create([
                            'article_id' => $article->id,
                            'type' => 'image',
                            'image' => $extraMedia->secure_url,
                            'image_public_id' => $extraMedia->public_id,
                            'sort_order' => $order,
                        ]);
                        $submitted[] = $extra->id;
                        $mediaService->claim($extraMedia, $article, 'article_block_image', $order + 1);
                        $order++;
                    }
                }
            }
            $block->save();
        }

        foreach (ArticleBlock::where('article_id', $article->id)->whereNotIn('id', $submitted)->get() as $removed) {
            $mediaService->queueDelete($removed->image_public_id);
            $removed->delete();
        }
    }

    private function blockMediaIds(array $data): array
    {
        return array_values(array_filter([
            ...((array) ($data['media_ids'] ?? [])),
            $data['media_id'] ?? null,
        ]));
    }

    private function appendComicImages(array|string|null $mediaIds, Article $article, MediaService $mediaService): void
    {
        $mediaIds = array_values(array_filter((array) $mediaIds));
        $lastOrder = (int) (ComicImage::where('article_id', $article->id)->max('sort_order') ?? -1);
        foreach ($mediaIds as $index => $mediaId) {
            $media = $mediaService->readyOwnedMedia((string) $mediaId);
            ComicImage::create([
                'article_id' => $article->id,
                'image' => $media->secure_url,
                'public_id' => $media->public_id,
                'sort_order' => $lastOrder + $index + 1,
            ]);
            $mediaService->claim($media, $article, 'comic_images', $lastOrder + $index + 1);
        }
    }

    private function syncComicImages(Request $request, Article $article, MediaService $mediaService): void
    {
        $actions = (array) $request->input('comic_actions', []);
        $replacements = (array) data_get($request->input('uploaded_media', []), 'comic_replacements', []);

        foreach ($actions as $comicImageId => $action) {
            $comicImage = ComicImage::where('article_id', $article->id)->find($comicImageId);
            if (! $comicImage || $action === 'keep' || $action === null) {
                continue;
            }

            if ($action === 'delete') {
                $mediaService->queueDelete($comicImage->public_id);
                $comicImage->delete();

                continue;
            }

            $mediaId = $replacements[$comicImageId] ?? null;
            if (! $mediaId) {
                throw ValidationException::withMessages([
                    "comic_replacements.{$comicImageId}" => 'Pilih gambar pengganti untuk panel comic yang ingin diganti.',
                ]);
            }

            $media = $mediaService->readyOwnedMedia((string) $mediaId);
            $oldPublicId = $comicImage->public_id;
            $comicImage->update([
                'image' => $media->secure_url,
                'public_id' => $media->public_id,
            ]);
            $mediaService->claim($media, $article, 'comic_images', $comicImage->sort_order + 1);
            $mediaService->queueDelete($oldPublicId);
        }
    }
}
