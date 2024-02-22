<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_update_articles()
    {
        $article = Article::factory()->create();

        $response = $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Actualizado artículo',
            'slug' => 'actualizado-articulo',
            'content' => 'Contenido del artículo actualizado'
        ])->assertOk();

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
                    'title' => 'Actualizado artículo',
                    'slug' => 'actualizado-articulo',
                    'content' => 'Contenido del artículo actualizado'
                ],
                'links' => [
                    'self' => route('api.v1.articles.show', $article)
                ]
            ]
        ]);
    }

    /** @test */
    public function title_is_required()
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article), [

            'slug' => 'actualizado-articulo',
            'content' => 'Contenido del artículo actualizado'

        ])->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function title_must_be_at_least_4_characters()
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article), [

            'title' => 'Nue',
            'slug' => 'actualizado-articulo',
            'content' => 'Contenido del artículo actualizado'

        ])->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function slug_is_required()
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article), [

            'title' => 'Actualizado artículo',
            'content' => 'Contenido del artículo actualizado'

        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function content_is_required()
    {
        $article = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article), [

            'title' => 'Actualizado artículo',
            'slug' => 'actualizado-articulo actualizado'

        ])->assertJsonApiValidationErrors('content');
    }
}
