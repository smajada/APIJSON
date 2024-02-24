<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveArticleRequest;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\Response;

class ArticleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')
            ->only(['store', 'update', 'destroy']);
    }

    public function show(Article $article): ArticleResource
    {
        return ArticleResource::make($article);
    }

    public function index(): ArticleCollection
    {

        $articles = Article::query()
            ->allowedSorts(['title', 'content'])
            ->allowedFilters(['title', 'content', 'year', 'month'])
            ->jsonPaginate();

        return ArticleCollection::make($articles);

    }

    public function store(SaveArticleRequest $request): ArticleResource
    {

        $article = Article::create($request->validated() + ['user_id' =>auth()->id()]);
        return ArticleResource::make($article);
    }

    public function update(SaveArticleRequest $request, Article $article): ArticleResource
    {
        $article->update($request->validated());

        return ArticleResource::make($article);
    }

    public function destroy(Article $article): Response
    {
        $article->delete();

        return response()->noContent();
    }
}
