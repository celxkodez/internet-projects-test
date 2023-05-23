<?php

namespace App\Controller;

use App\Repository\PlayerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/teams', name: 'teams.')]
class TeamController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('team/index.html.twig', [
            'controller_name' => 'TeamController',
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(PlayerRepository $playerRepository): Response
    {
        return $this->render('team/create.html.twig', [
            'players' => $playerRepository->withOutTeam(),
        ]);
    }

    #[Route('/create', name: 'store', methods: ["post"])]
    public function store(PlayerRepository $playerRepository): Response
    {
        return $this->redirectToRoute('teams.index');
    }
}
