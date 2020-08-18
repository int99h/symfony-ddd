<?php

namespace App\Project\Domain\Article;

use App\Project\Domain\Article\Entity\Article;
use League\Fractal\TransformerAbstract;

/**
 * Class ArticleTransformer
 * @package App\Project\Domain\Article
 */
class ArticleTransformer extends TransformerAbstract
{
    /**
     * @param Article $article
     * @return array
     */
    public function transform(Article $article): array
    {
        return [
            'id' => $article->getId(),
            'title' => $article->getTitle(),
            'body' => $article->getBody(),
            'createdAt' => $article->getCreatedAt()->format(DATE_ISO8601),
            'updatedAt' => $article->getUpdatedAt() ? $article->getUpdatedAt()->format(DATE_ISO8601) : null,
        ];
    }
}