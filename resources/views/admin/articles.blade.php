@extends('admin.layouts.admin')


@php
    use Illuminate\Support\Str;
@endphp

@section('content')

<div class="articles-page">

    <!-- HEADER -->
    <div class="page-top">

        <div>
            <h1>Articles</h1>

            <p class="page-subtitle">
                Manage news, stories, and updates from Aanaya ✨
            </p>
        </div>

        <a href="/admin/articles/create" class="pink-btn">

            <i class="fas fa-plus"></i>

            Create Article

        </a>

    </div>

    <!-- GRID -->
    <div class="articles-grid">

        @forelse ($articles as $article)

            <div class="article-card">

                <!-- IMAGE -->
                <div class="article-thumb-wrapper">

                    <img
                        src="{{ asset('uploads/articles/' . $article->thumbnail) }}"
                        class="article-thumb">

                </div>

                <!-- CONTENT -->
                <div class="article-content">

                    <span class="article-date">

                        <i class="fas fa-calendar"></i>

                        {{ $article->created_at->format('d M Y') }}

                    </span>

                    <h2>
                        {{ $article->title }}
                    </h2>

                    <p>
                        {{ Str::limit(strip_tags($article->content), 120) }}
                    </p>

                </div>

                <!-- ACTION -->
                <div class="card-actions">

                    <a href="/admin/articles/{{ $article->id }}/edit"
                       class="edit-btn">

                        <i class="fas fa-pen"></i>

                        Edit

                    </a>

                    <form
                        action="/admin/articles/{{ $article->id }}"
                        method="POST">

                        @csrf
                        @method('DELETE')

                        <button class="delete-btn">

                            <i class="fas fa-trash"></i>

                            Delete

                        </button>

                    </form>

                </div>

            </div>

        @empty

            <div class="empty-state">

                <i class="fas fa-newspaper"></i>

                <h2>No Articles Yet</h2>

                <p>
                    Start publishing dreamy stories ✨
                </p>

            </div>

        @endforelse

    </div>

</div>

@endsection