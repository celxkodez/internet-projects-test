<?php

namespace App\Controller;

use App\Entity\Player;
use App\Entity\Team;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

#[Route('/transfer', name: 'app_transfer.')]
class TransferController extends AbstractController
{
    public function __construct(
        protected TeamRepository $teamRepository,
        protected PlayerRepository $playerRepository
    )
    {
    }

    #[Route('/list-players', name: 'list_players')]
    public function index(): Response
    {
        return $this->render('transfer/index.html.twig', [
            'players' => $this->playerRepository->findAll(),
            'teams' => $this->teamRepository->findAll(),
            'context' => [ ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($obj){ return $obj->getId(); }]
        ]);
    }

    #[Route('/transfer/{player}/{team}', name: 'transfer')]
    public function transfer(
        Player $player,
        Team $team,
        TeamRepository $teamRepository,
        Request $request
    ): Response
    {
        $playerTeam = $player->getTeam();

        if ((float)$team->getMoneyBalance() < (float)$player->getMarketValue()) {
            return $this->json(['status' => false, 'message' => $team->getName().' Has Insufficient Funds']);
        }

        $buyingTeamBalance = (float)$team->getMoneyBalance();
        $team->setMoneyBalance($buyingTeamBalance - (float)$player->getMarketValue());
        $team->addPlayer($player);

        $playerTeam->setMoneyBalance((float)$playerTeam->getMoneyBalance() + (float)$player->getMarketValue());

        $teamRepository->save($team, true);
        $teamRepository->save($playerTeam, true);

        return $this->json(['status' => true, 'message' => 'Player Sold to '. $team->getName()]);
    }
}
