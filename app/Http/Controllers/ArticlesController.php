<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleTag;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ArticlesController extends Controller
{
    public function index(Request $request, $code) {
        $language = Language::with('tags')->findOrFail($code);

        $articles = $language->articles()
            ->with('tags');

        if ($tag = $request->query('tag')) {
            $articles->whereIn('id',
                Article::join('article_tags', 'articles.id', '=', 'article_tags.article_id')
                    ->where('article_tags.tag', '=', $tag)
                    ->pluck('articles.id')
            );
        }

        $articles = $articles->paginate(10);

        return Inertia::render('ArticlesIndex', compact('language', 'articles'));
    }

    public function view(Request $request, $code, $article) {
        $language = Language::findOrFail($code);
        $article = Article::with('tags')->findOrFail($article);

        $editMode = $request->has('editMode');

        return Inertia::render('ArticleView', compact('language', 'article', 'editMode'));
    }

    public function store(Request $request, $code) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);

        $data = $request->validate([
            'title' => 'required'
        ], messages: [
                'title.required' => 'Заголовок не может быть пустым.'
            ]
        );

        $article = Article::create([
            ...$data,
            'language_id' => $language->id,
            'article' => '',
        ]);
        $language->updateTimestamps();
        $language->save();

        return redirect()->route('languages.articles.view', ['code' => $language->id, 'article' => $article->id, 'editMode' => '']);
    }

    public function update(Request $request, $code, $article_id) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);

        $article = Article::findOrFail($article_id);
        Gate::authorize('article-is-language', $article);

        $data = $request->validate([
            'title' => 'required',
            'article' => ''
        ], messages: [
            'title.required' => 'Заголовок не может быть пустым.'
        ]);
        if (empty($data['article'])) {
            $data['article'] = '';
        }

        $article->update($data);
        $language->updateTimestamps();
        $language->save();

        return redirect()->route('languages.articles.view', ['code' => $language->id, 'article' => $article->id]);
    }

    public function pushTag(Request $request, $code, $article_id) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);

        $article = Article::findOrFail($article_id);
        Gate::authorize('article-is-language', $article);

        $data = $request->validate([
            'tag' => 'required',
        ], messages: [
            'tag.required' => 'Тег не может быть пустым.'
        ]);

        if ($article->tags()->where('tag', '=', $data['tag'])->count() === 0) {
            ArticleTag::create([
                ...$data,
                'article_id' => $article->id,
            ]);
        }
        $article->updateTimestamps();
        $article->save();

        $language->updateTimestamps();
        $language->save();

        return redirect()->route('languages.articles.view', ['code' => $language->id, 'article' => $article->id]);
    }

    public function deleteTag(Request $request, $code, $article_id, $tag_id) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);

        $article = Article::findOrFail($article_id);
        Gate::authorize('article-is-language', $article);

        $tag = ArticleTag::findOrFail($tag_id);
        Gate::authorize('tag-is-language', $tag);

        $tag->delete();

        $article->updateTimestamps();
        $article->save();

        $language->updateTimestamps();
        $language->save();

        return redirect()->route('languages.articles.view', ['code' => $language->id, 'article' => $article->id]);
    }

    public function publish(Request $request, $code, $article_id) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);

        $article = Article::findOrFail($article_id);
        Gate::authorize('article-is-language', $article);

        $article->published_at = now();
        $article->save();

        $language->updateTimestamps();
        $language->save();

        return redirect()->route('languages.articles.view', ['code' => $language->id, 'article' => $article->id]);
    }

    public function unpublish(Request $request, $code, $article_id) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);

        $article = Article::findOrFail($article_id);
        Gate::authorize('article-is-language', $article);

        $article->published_at = null;
        $article->save();

        $language->updateTimestamps();
        $language->save();

        return redirect()->route('languages.articles.view', ['code' => $language->id, 'article' => $article->id]);
    }

    public function delete(Request $request, $code, $article_id) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);

        $article = Article::findOrFail($article_id);
        Gate::authorize('article-is-language', $article);

        $article->delete();

        return redirect()->route('languages.articles', ['code' => $language->id]);
    }
}
