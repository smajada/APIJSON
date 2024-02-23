<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SortArticlesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_sort_articles_by_title(): void
    {
        Article::factory()->create(['title' => 'C Title']);
        Article::factory()->create(['title' => 'A Title']);
        Article::factory()->create(['title' => 'B Title']);

        $url = route('api.v1.articles.index', ['sort' => 'title']);

        $this->getJson($url)
            ->assertSeeInOrder([
                'A Title',
                'B Title',
                'C Title',
            ]);
    }

    /** @test */
    public function can_sort_articles_by_title_descending(): void
    {
        Article::factory()->create(['title' => 'C Title']);
        Article::factory()->create(['title' => 'A Title']);
        Article::factory()->create(['title' => 'B Title']);

        $url = route('api.v1.articles.index', ['sort' => '-title']);

        $this->getJson($url)
            ->assertSeeInOrder([
                'C Title',
                'B Title',
                'A Title',
            ]);
    }

    /** @test */
    public function can_sort_articles_by_content(): void
    {
        Article::factory()->create(['content' => 'C Content']);
        Article::factory()->create(['content' => 'A Content']);
        Article::factory()->create(['content' => 'B Content']);

        $url = route('api.v1.articles.index', ['sort' => 'content']);

        $this->getJson($url)
            ->assertSeeInOrder([
                'A Content',
                'B Content',
                'C Content',
            ]);
    }

    /** @test */
    public function can_sort_articles_by_content_descending(): void
    {
        Article::factory()->create(['content' => 'C content']);
        Article::factory()->create(['content' => 'A content']);
        Article::factory()->create(['content' => 'B content']);

        $url = route('api.v1.articles.index', ['sort' => '-content']);

        $this->getJson($url)
            ->assertSeeInOrder([
                'C content',
                'B content',
                'A content',
            ]);
    }

    /** @test */
    public function can_sort_articles_by_title_and_content(): void
    {
        Article::factory()->create([
            'title' => 'A title',
            'content' => 'A content'
        ]);

        Article::factory()->create([
            'title' => 'B title',
            'content' => 'B content'
        ]);

        Article::factory()->create([
            'title' => 'A title',
            'content' => 'C content'
        ]);

        // articles?sort=title,-content
        $url = route('api.v1.articles.index', ['sort' => 'title,-content']);

        $this->getJson($url)->assertSeeInOrder([
            'C content',
            'A content',
            'B content',
        ]);
    }

    /** @test */
    public function cannot_sort_articles_by_unknown_fields(): void
    {
        Article::factory(3)->create();

        // articles?sort=title,-content
        $url = route('api.v1.articles.index', ['sort' => 'unknown']);

        $this->getJson($url)->assertStatus(400);
    }

}
