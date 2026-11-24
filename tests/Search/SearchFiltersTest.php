<?php

namespace Tests\Search;

use Tests\TestCase;

class SearchFiltersTest extends TestCase
{
    public function test_in_shelf_filter_shows_books_on_shelf()
    {
        $shelf = $this->entities->shelfHasBooks();
        $book = $shelf->books()->first();

        $resp = $this->asEditor()->get('/search?term=' . urlencode($book->name . ' {in_shelf:' . $shelf->slug . '}'));
        $resp->assertOk();
        $resp->assertSee($book->name);
    }

    public function test_in_book_filter_excludes_other_books()
    {
        $page = $this->entities->page();
        $otherBook = $this->entities->book(fn ($query) => $query->where('id', '!=', $page->book_id));

        $resp = $this->asEditor()->get('/search?term=' . urlencode($page->name . ' {in_book:' . $otherBook->slug . '}'));
        $resp->assertOk();
        $resp->assertDontSee($page->getUrl());
    }

    public function test_author_filter_matches_creator()
    {
        $editor = $this->users->editor();
        $page = $this->entities->page();
        $page->forceFill(['created_by' => $editor->id])->save();

        $resp = $this->asEditor()->get('/search?term=' . urlencode($page->name . ' {author:' . $editor->slug . '}'));
        $resp->assertOk();
        $resp->assertSee($page->getUrl());
    }

    public function test_search_sidebar_shows_new_filter_dropdowns()
    {
        $resp = $this->asEditor()->get('/search?term=a');
        $html = $this->withHtml($resp);
        $html->assertElementExists('select[name="filters[in_shelf]"]');
        $html->assertElementExists('select[name="filters[in_book]"]');
        $html->assertElementExists('select[name="filters[author]"]');
    }
}
