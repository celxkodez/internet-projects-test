<?php

namespace App\Controller;

use App\Entity\Player;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/players', name: 'players.')]
class PlayerController extends AbstractController
{
    public function __construct(
        protected LoggerInterface $logger,
        protected ValidatorInterface $validator
    )
    {
    }

    #[Route('/', name: 'index')]
    public function index(
        PlayerRepository $repository,
        PaginatorInterface $paginator,
        Request $request
    ): Response
    {
        return $this->render('player/index.html.twig', [
            'players' => $paginator->paginate(
                $repository->createQueryBuilder('q'),
                $request->query->getInt('page', 1),
                20
            ),
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET'])]
    public function create(TeamRepository $repository): Response
    {
        return $this->render('player/create.html.twig', [
            'teams' => $repository->findAll(),
        ]);
    }

    #[Route('/create', name: 'store', methods: ['POST'])]
    public function store(
        TeamRepository $teamRepository,
        PlayerRepository $playerRepository,
        Request $request,
    ): Response
    {

        $input = [
            'name' => $request->get('name'),
            'surname' => $request->get('surname'),
            'market_value' => (float)$request->get('market_value')
        ];

        $player = new Player();
        $player->setName($input['name']);
        $player->setSurname($input['surname']);
        $player->setMarketValue($input['market_value']);
        $player->setCreatedAt(new \DateTimeImmutable());

        $validated = $this->validator->validate($player );

        if ($validated->count()) {
            $this->logger->info('validation check');
            $this->logger->error($validated[0]->getMessage());
            $this->logger->error($validated);
            $this->addFlash('error', $validated[0]->getMessage());

            return $this->redirectToRoute('players.create');
        }

        try {
            if ($request->get('team') !== null) {
                $this->logger->info('team');
                $this->logger->info($request->get('team'));
                $player->setTeam($teamRepository->find($request->get('team')));
            }
            $playerRepository->save($player, true);
        } catch (\Throwable $exception) {

            $this->logger->error($exception);

            $this->addFlash('error', "An Error Occurred While Creating Team");

            return $this->redirectToRoute('players.create');
        }

        $this->addFlash('success', "Player Created Successfully!");

        return $this->redirectToRoute('players.index');
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['GET'])]
    public function edit(Player $player, TeamRepository $teamRepository): Response
    {
        return $this->render('player/edit.html.twig', [
            'player' => $player,
            'teams' => $teamRepository->findAll(),
        ]);
    }

    #[Route('/edit/{id}', name: 'update', methods: ['POST'])]
    public function update(
        Player $player,
        PlayerRepository $playerRepository,
        TeamRepository $teamRepository,
        Request $request
    ): Response
    {
        $input = [
            'name' => $request->get('name'),
            'surname' => $request->get('surname'),
            'market_value' => (float)$request->get('market_value')
        ];

        $player->setName($input['name']);
        $player->setSurname($input['surname']);
        $player->setMarketValue($input['market_value']);
        $player->setUpdatedAt(new \DateTimeImmutable());

        $validated = $this->validator->validate($player );

        if ($validated->count()) {
            $this->addFlash('error', $validated[0]->getMessage());

            return $this->redirectToRoute('players.create');
        }

        try {
            if ($request->get('team') !== null) {
                $player->setTeam($teamRepository->find($request->get('team')));
            }

            $playerRepository->save($player, true);
        } catch (\Throwable $exception) {

            $this->logger->error($exception);

            $this->addFlash('error', "An Error Occurred While Updating Player");

            return $this->redirectToRoute('players.edit');
        }

        $this->addFlash('success', "Player Updated Successfully!");

        return $this->redirectToRoute('players.index');
    }

    #[Route('/show/{id}', name: 'show', methods: ['GET'])]
    public function show(Player $player): Response
    {
        return $this->render('player/show.html.twig', [

        ]);
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(
        Request $request,
        Player $player,
        PlayerRepository $playerRepository
    ): Response
    {
        if ($this->isCsrfTokenValid('delete', $request->headers->get('x-csrf-token'))) {
            $playerRepository->remove($player, true);

            $this->addFlash('success', "Player Deleted");
        } else {
            $this->addFlash('error', "Player Could Not Deleted");
        }

        return $this->json(['status' => true]);
    }
}
