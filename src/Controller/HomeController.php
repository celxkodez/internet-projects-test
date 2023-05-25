<?php

namespace App\Controller;

use App\Repository\TeamRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(TeamRepository $teamRepository): Response
    {
        return $this->render('pages/index.html.twig', [
            'teams' => $teamRepository->createQueryBuilder('q')
                ->orderBy('q.money_balance', 'DESC')
                ->setMaxResults(6)
                ->getQuery()
                ->getResult()
        ]);
    }

}
