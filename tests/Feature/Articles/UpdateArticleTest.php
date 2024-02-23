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
            'slug' =>  $article->slug,
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
                    'slug' => $article->slug,
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
    public function slug_must_contain_letters_numbers_and_dashes()
    {
        $article = Article::factory()->create();
        $this->patchJson(route('api.v1.articles.update', $article), [

            'title' => 'Nuevo Articulo',
            'slug' => '$·%&/()=',
            'content' => 'Contenido del artículo'

        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_contain_underscores()
    {
        $article = Article::factory()->create();
        $this->patchJson(route('api.v1.articles.update', $article), [

            'title' => 'Nuevo Articulo',
            'slug' => 'with_underscores',
            'content' => 'Contenido del artículo'

        ])->assertSee(__('validation.no_underscore', ['attribute' => 'slug']))
            ->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_start_with_dashes()
    {
        $article = Article::factory()->create();
        $this->patchJson(route('api.v1.articles.update', $article), [

            'title' => 'Nuevo Articulo',
            'slug' => '-starts-with-dash',
            'content' => 'Contenido del artículo'

        ])->assertSee(__('validation.no_starting_dashes', ['attribute' => 'slug']))
            ->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_end_with_dashes()
    {
        $article = Article::factory()->create();
        $this->patchJson(route('api.v1.articles.update', $article), [

            'title' => 'Nuevo Articulo',
            'slug' => 'starts-with-dash-',
            'content' => 'Contenido del artículo'

        ])->assertSee(__('validation.no_ending_dashes', ['attribute' => 'slug']))
            ->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_is_unique()
    {
        $article1 = Article::factory()->create();
        $article2 = Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article1), [

            'title' => 'Nuevo Articulo',
            'slug' => $article2->slug,
            'content' => 'Contenido del artículo'

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
