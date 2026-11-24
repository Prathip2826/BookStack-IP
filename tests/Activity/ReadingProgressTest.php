<?php

namespace Tests\Activity;

use Tests\TestCase;

class ReadingProgressTest extends TestCase
{
    public function test_progress_shows_book_and_continue_link_after_viewing_page()
    {
        $page = $this->entities->page();
        $this->asEditor()->get($page->getUrl());

        $resp = $this->get('/reading-progress');
        $resp->assertOk();
        $resp->assertSee($page->book->name);
        $resp->assertSee('Continue reading');
        $resp->assertSee($page->name);
    }

    public function test_empty_message_shown_when_no_pages_viewed()
    {
        $viewer = $this->users->viewer();

        $resp = $this->actingAs($viewer)->get('/reading-progress');
        $resp->assertOk();
        $resp->assertSee('You have not read any pages yet.');
    }

    public function test_user_menu_contains_reading_progress_link()
    {
        $resp = $this->asEditor()->get('/');
        $this->withHtml($resp)->assertElementContains('a[href$="/reading-progress"]', 'Reading Progress');
    }
}
