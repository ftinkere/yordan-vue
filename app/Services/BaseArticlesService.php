<?php

namespace App\Services;

use App\Models\BaseArticles;
use App\Models\Language;

class BaseArticlesService
{
    public function update(Language $language, $data) {
        if ($language->base_articles === null) {
            $articles = new BaseArticles([
                'language_id' => $language->id,
            ]);
            $articles->save();
        } else {
            $articles = $language->base_articles;
        }

        $articles->update($data);
    }
}