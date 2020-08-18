<?php

namespace App\Project\Domain\Article;

use App\Project\App\Service\FractalService;
use App\Project\Domain\Article\Entity\Article;
use App\Project\Infrastructure\Article\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use League\Fractal\Pagination\PagerfantaPaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\ResourceAbstract;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class ArticleService
 * @package App\Project\Domain\Article
 */
class ArticleService
{
    private const TRANSFORMER_KEY = 'article';

    /** @var EntityManagerInterface */
    private $entityManager;
    /** @var FractalService */
    private $fractalService;
    /** @var ArticleTransformer */
    private $transformer;
    /** @var ArticleRepository */
    private $repository;

    /**
     * ArticleService constructor.
     * @param EntityManagerInterface $em
     * @param FractalService $fs
     * @param ArticleTransformer $at
     */
    public function __construct(EntityManagerInterface $em, FractalService $fs, ArticleTransformer $at)
    {
        $this->entityManager = $em;
        $this->fractalService = $fs;
        $this->transformer = $at;
        $this->repository = $em->getRepository(Article::class);
    }

    /**
     * @param Request $request
     * @param RouterInterface $router
     * @return Collection
     */
    public function getAll(Request $request, RouterInterface $router): ResourceAbstract
    {
        // prepare params
        $page = intval($request->get('page', 1));
        $perPage = intval($request->get('perPage', 5));
        $order = $request->get('order', 'asc');

        // setup paginator
        $paginator = new Pagerfanta(new QueryAdapter($this->repository->getQuery($order)));
        $paginator->setCurrentPage($page);
        $paginator->setMaxPerPage($perPage);

        // prepare result & add paginator
        $items = $paginator->getCurrentPageResults();
        $paginatorAdapter = new PagerfantaPaginatorAdapter($paginator, function(int $page) use ($request, $router) {
            $route = $request->attributes->get('_route');
            $requestParams = $request->attributes->get('_route_params');
            $params = array_merge($requestParams, $request->query->all());
            $params['page'] = $page;
            return $router->generate($route, $params, UrlGeneratorInterface::ABSOLUTE_URL);
        });

        // finalize
        $resource = new Collection($items, $this->transformer, self::TRANSFORMER_KEY);
        $resource->setPaginator($paginatorAdapter);

        return $resource;
    }

    /**
     * @param $id
     * @return ResourceAbstract
     * @throws EntityNotFoundException
     */
    public function getOne($id): ResourceAbstract
    {
        $article = $this->repository->find($id);
        if (!$article) {
            throw new EntityNotFoundException("Article #{$id} not found ");
        }

        return new Item($article, $this->transformer, self::TRANSFORMER_KEY);
    }

    /**
     * @param Request $request
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(Request $request): void
    {
        $article = (new Article())
            ->setTitle($request->get('title'))
            ->setBody($request->get('body'))
            ->setCreatedAt(new \DateTimeImmutable())
        ;
        $this->repository->store($article);
    }

    /**
     * @param Request $request
     * @throws EntityNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(Request $request): void
    {
        $id = $request->get('id');
        $article = $this->repository->find($id);
        if (!$article) {
            throw new EntityNotFoundException("Article #{$id} not found ");
        }
        $changed = false;
        if ($request->get('title')) {
            $article->setTitle($request->get('title'));
            $changed = true;
        }
        if ($request->get('body')) {
            $article->setBody($request->get('body'));
            $changed = true;
        }
        if ($changed) {
            $article->setUpdatedAt(new \DateTimeImmutable());
        }
        $this->repository->store($article);
    }

    /**
     * @param $id
     * @throws EntityNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete($id): void
    {
        $article = $this->repository->find($id);
        if (!$article) {
            throw new EntityNotFoundException("Article #{$id} not found ");
        }
        $this->repository->delete($article);
    }
}