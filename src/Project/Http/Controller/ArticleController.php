<?php

namespace App\Project\Http\Controller;

use App\Project\App\Service\FractalService;
use App\Project\App\Service\TokenService;
use App\Project\Domain\Article\ArticleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class ArticleController
 * @package App\Project\Http\Controller
 */
class ArticleController extends AbstractController
{
    /** @var RouterInterface */
    private $router;
    /** @var FractalService */
    private $fractalService;
    /** @var ArticleService */
    private $articleService;
    /** @var TokenService */
    private $tokenService;

    /**
     * ArticleController constructor.
     * @param RouterInterface $r
     * @param FractalService $fs
     * @param ArticleService $as
     * @param TokenService $ts
     */
    public function __construct(RouterInterface $r, FractalService $fs, ArticleService $as, TokenService $ts)
    {
        $this->router = $r;
        $this->fractalService = $fs;
        $this->articleService = $as;
        $this->tokenService = $ts;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function list(Request $request): Response
    {
        $articles = $this->articleService->getAll($request, $this->router);

        return new JsonResponse($this->fractalService->transform($articles));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    public function one(Request $request): Response
    {
        $article = $this->articleService->getOne($request->get('id'));

        return new JsonResponse($this->fractalService->transform($article));
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function create(Request $request): Response
    {
        $this->tokenService->checkToken($request);
        $this->articleService->create($request);

        return new JsonResponse(['success' => true]);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function update(Request $request): Response
    {
        $this->tokenService->checkToken($request);
        $this->articleService->update($request);

        return new JsonResponse(['success' => true]);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Doctrine\ORM\EntityNotFoundException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function delete(Request $request): Response
    {
        $this->tokenService->checkToken($request);
        $this->articleService->delete($request->get('id'));

        return new JsonResponse(['success' => true]);
    }
}