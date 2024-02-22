<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_create_articles(): void
    {
        $this->withoutExceptionHandling();
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'Nuevo artículo',
                    'slug' => 'nuevo-articulo',
                    'content' => 'Contenido del artículo'
                ]
            ]
        ]);

        $response->assertCreated();

        $article = Article::first();

        $response->assertHeader(
            'Location',
            route('api.v1.articles.show', $article)
        );

        $response->assertExactJson([
            'data' => [
                'type' => 'articles',
                'id' => (string)$article->getRouteKey(),
                'attributes' => [
                    'title' => 'Nuevo artículo',
                    'slug' => 'nuevo-articulo',
                    'content' => 'Contenido del artículo'
                ],
                'links' => [
                    'self' => route('api.v1.articles.show', $article)
                ]
            ]
        ]);
    }

    /** @test */
    public function title_is_required(): void
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'fd',
                    'slug' => 'nuevo-articulo',
                    'content' => 'Contenido del artículo'
                ]
            ]
        ])->dump();

        $response->assertJsonStructure([
            'errors' => [
               ['title', 'detail', 'source' => ['pointer']]
            ]
        ])->assertJsonFragment([
            'source' => [
                'pointer' => '/data/attributes/title'
            ]
        ])->assertHeader(
            'Content-Type',
            'application/vnd.api+json'
        )->assertStatus(422);

//        $response->assertJsonValidationErrors('data.attributes.title');

    }

    /** @test */
    public function title_must_be_at_least_4_characters(): void
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'Nue',
                    'slug' => 'nuevo-articulo',
                    'content' => 'Contenido del artículo'
                ]
            ]
        ]);

        $response->assertJsonValidationErrors('data.attributes.title');

    }

    /** @test */
    public function slug_is_required(): void
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'Nuevo Artículo',
                    'content' => 'Contenido del artículo'
                ]
            ]
        ])->dump();

        $response->assertJsonValidationErrors('data.attributes.slug');

    }

    /** @test */
    public function content_is_required(): void
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'Nuevo Artículo',
                    'slug' => 'nuevo-articulo'
                ]
            ]
        ])->dump();

        $response->assertJsonValidationErrors('data.attributes.content');

    }


}
