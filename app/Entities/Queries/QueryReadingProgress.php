<?php

namespace BookStack\Entities\Queries;

use BookStack\Activity\Models\View;
use Illuminate\Support\Collection;

/**
 * Builds per-book reading progress for the current user,
 * using the pages they have viewed (stored in the views table).
 */
class QueryReadingProgress
{
    public function __construct(
        protected PageQueries $pageQueries,
        protected BookQueries $bookQueries,
    ) {
    }

    public function run(): Collection
    {
        $user = user();
        if ($user->isGuest()) {
            return collect();
        }

        // page id => last time this user viewed it
        $viewTimes = View::query()
            ->where('user_id', '=', $user->id)
            ->where('viewable_type', '=', 'page')
            ->pluck('updated_at', 'viewable_id');

        if ($viewTimes->isEmpty()) {
            return collect();
        }

        // Viewed pages the user can still see (respects permissions, excludes drafts)
        $viewedPages = $this->pageQueries->visibleForList()
            ->where('draft', '=', false)
            ->whereIn('id', $viewTimes->keys())
            ->get();

        $bookIds = $viewedPages->pluck('book_id')->unique()->values();

        // book id => total visible pages in that book
        $totalsByBook = $this->pageQueries->visibleForList()
            ->where('draft', '=', false)
            ->whereIn('book_id', $bookIds)
            ->get()
            ->countBy('book_id');

        $books = $this->bookQueries->visibleForList()
            ->whereIn('id', $bookIds)
            ->get()
            ->keyBy('id');

        return $viewedPages->groupBy('book_id')
            ->filter(fn (Collection $pages, int $bookId) => $books->has($bookId))
            ->map(function (Collection $pages, int $bookId) use ($books, $totalsByBook, $viewTimes) {
                $lastPage = $pages->sortByDesc(fn ($page) => $viewTimes[$page->id])->first();
                $total = $totalsByBook[$bookId] ?? 0;
                $viewed = $pages->count();

                return [
                    'book'         => $books[$bookId],
                    'viewed'       => $viewed,
                    'total'        => $total,
                    'percent'      => $total > 0 ? (int) round(($viewed / $total) * 100) : 0,
                    'lastPage'     => $lastPage,
                    'lastViewedAt' => $viewTimes[$lastPage->id],
                ];
            })
            ->sortByDesc('lastViewedAt')
            ->values();
    }
}
