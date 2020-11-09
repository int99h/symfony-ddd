<?php

namespace App\Project\Infrastructure\Article;

use App\Project\Infrastructure\AbstractEntityRepository;
use App\Project\Domain\Article\Entity\Article;
use Doctrine\ORM\Query;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends AbstractEntityRepository
{
    /**
     * @param string $order
     * @return Query
     */
    public function getQuery(string $order = 'asc'): Query
    {
        return $this->createQueryBuilder('a')->orderBy('a.id', $order)->getQuery();
    }

    /**
     * @param Article $article
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function store(Article $article)
    {
        $this->_em->persist($article);
        $this->_em->flush();
    }

    /**
     * @param Article $article
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Article $article)
    {
        $this->_em->remove($article);
        $this->_em->flush();
    }
}