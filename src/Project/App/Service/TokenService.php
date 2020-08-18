<?php

namespace App\Project\App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class TokenService
 * @package App\Project\App\Service
 */
class TokenService
{
    /** @var string */
    private $token;

    /**
     * TokenService constructor.
     * @param ParameterBagInterface $bag
     * @todo load tokens from storage
     */
    public function __construct(ParameterBagInterface $bag)
    {
        $this->token = $bag->get('access.token');
        if (!$this->token) {
            throw new \ValueError('Access token not defined');
        }
    }

    /**
     * @param Request $request
     */
    public function checkToken(Request $request)
    {
        $token = $request->headers->get('Token');
        if (!$token || $token !== $this->token) {
            throw new UnauthorizedHttpException('Unauthorized', 'Access Token Needed');
        }
    }
}