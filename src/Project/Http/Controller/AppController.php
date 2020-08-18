<?php

namespace App\Project\Http\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AppController
 * @package App\Project\Http\Controller
 */
class AppController extends AbstractController
{
    /**
     * Simple endpoint for health-check
     * @Route("/health")
     */
    public function health(): Response
    {
        return new Response();
    }
}
