<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_cannot_create_articles()
    {
        $this->postJson(route('api.v1.articles.store'))->assertUnauthorized();


//        $response->assertJsonApiError();

        $this->assertDatabaseCount('articles', 0);
    }

    /** @test */
    public function can_create_articles()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo artículo',
            'slug' => 'nuevo-articulo',
            'content' => 'Contenido del artículo'
        ])->assertCreated();

        $article = Article::first();

        $response->assertHeader('Location', route('api.v1.articles.show', $article));


        $response->assertExactJson(['data' =>
            ['type' =>
                'articles',
                'id' => (string)$article->getRouteKey(),
                'attributes' =>
                    [
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
    public function title_is_required()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [

            'slug' => 'nuevo-articulo', 'content' => 'Contenido del artículo'

        ])->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function title_must_be_at_least_4_characters()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [

            'title' => 'Nue', 'slug' => 'nuevo-articulo', 'content' => 'Contenido del artículo'

        ])->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function slug_is_required()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [

            'title' => 'Nuevo Articulo', 'content' => 'Contenido del artículo'

        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_is_unique()
    {
        Sanctum::actingAs(User::factory()->create());

        $article = Article::factory()->create();

        $this->postJson(route('api.v1.articles.store'), [

            'title' => 'Nuevo Articulo', 'slug' => $article->slug, 'content' => 'Contenido del artículo'

        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_contain_letters_numbers_and_dashes()
    {
        Sanctum::actingAs(User::factory()->create());


        $this->postJson(route('api.v1.articles.store'), [

            'title' => 'Nuevo Articulo', 'slug' => '$·%&/()=', 'content' => 'Contenido del artículo'

        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_contain_underscores()
    {
        Sanctum::actingAs(User::factory()->create());


        $this->postJson(route('api.v1.articles.store'), [

            'title' => 'Nuevo Articulo', 'slug' => 'with_underscores', 'content' => 'Contenido del artículo'

        ])->assertSee(__('validation.no_underscore', ['attribute' => 'slug']))->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_start_with_dashes()
    {
        Sanctum::actingAs(User::factory()->create());
        $this->postJson(route('api.v1.articles.store'), [

            'title' => 'Nuevo Articulo', 'slug' => '-starts-with-dash', 'content' => 'Contenido del artículo'

        ])->assertSee(__('validation.no_starting_dashes', ['attribute' => 'slug']))->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_end_with_dashes()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [

            'title' => 'Nuevo Articulo', 'slug' => 'starts-with-dash-', 'content' => 'Contenido del artículo'

        ])->assertSee(__('validation.no_ending_dashes', ['attribute' => 'slug']))->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function content_is_required()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.articles.store'), [

            'title' => 'Nuevo Articulo', 'slug' => 'nuevo-articulo'

        ])->assertJsonApiValidationErrors('content');
    }
}
