@extends('layouts.simple')

@section('body')
    <div class="container small pt-xl">
        <main class="card content-wrap">
            <h1 class="list-heading">{{ trans('entities.reading_progress') }}</h1>

            @forelse($progress as $item)
                <div class="py-m">
                    <div class="flex-container-row justify-space-between items-center">
                        <a href="{{ $item['book']->getUrl() }}" class="text-book bold">@icon('book') {{ $item['book']->name }}</a>
                        <span class="text-muted">{{ trans('entities.reading_progress_count', ['viewed' => $item['viewed'], 'total' => $item['total']]) }}</span>
                    </div>
                    <progress value="{{ $item['percent'] }}" max="100" class="reading-progress-bar">{{ $item['percent'] }}%</progress>
                    <div class="flex-container-row justify-space-between items-center">
                        <strong>{{ $item['percent'] }}%</strong>
                        <a href="{{ $item['lastPage']->getUrl() }}" class="button outline small">
                            {{ trans('entities.reading_progress_continue') }}: {{ $item['lastPage']->name }}
                        </a>
                    </div>
                </div>
            @empty
                <p class="text-muted italic">{{ trans('entities.reading_progress_empty') }}</p>
            @endforelse
        </main>

        <section class="card content-wrap mt-m">
            <h2 class="list-heading">{{ trans('entities.recently_viewed') }}</h2>
            @include('entities.list', ['entities' => $recentlyViewed, 'style' => 'compact'])
        </section>
    </div>
@stop
