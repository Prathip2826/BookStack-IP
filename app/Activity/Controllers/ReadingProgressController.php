<?php

namespace BookStack\Activity\Controllers;

use BookStack\Entities\Queries\QueryReadingProgress;
use BookStack\Entities\Queries\QueryRecentlyViewed;
use BookStack\Http\Controller;

class ReadingProgressController extends Controller
{
    /**
     * Show the current user's reading progress across books.
     */
    public function index(QueryReadingProgress $readingProgress, QueryRecentlyViewed $recentlyViewed)
    {
        $this->setPageTitle(trans('entities.reading_progress'));

        return view('reading-progress.index', [
            'progress'       => $readingProgress->run(),
            'recentlyViewed' => $recentlyViewed->run(10, 1),
        ]);
    }
}
