<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\TeamType;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/teams', name: 'teams.')]
class TeamController extends AbstractController
{
    public function __construct(
        protected LoggerInterface $logger,
        protected ValidatorInterface $validator
    )
    {
    }

    #[Route('/', name: 'index')]
    public function index(
        TeamRepository $teamRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response
    {

        return $this->render('team/index.html.twig', [
            'teams' => $paginator->paginate(
                $teamRepository->createQueryBuilder('q'),
                $request->query->getInt('page', 1),
                20
            ),
            'context' => [ ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($obj){ return $obj->getId(); }]
        ]);
    }

    #[Route('/create', name: 'create', methods: ["GET"])]
    public function create(PlayerRepository $playerRepository): Response
    {
        return $this->render('team/create.html.twig', [
            'players' => $playerRepository->withOutTeam(),
        ]);
    }

    #[Route('/create', name: 'store', methods: ["POST"])]
    public function store(
        TeamRepository $teamRepository,
        PlayerRepository $playerRepository,
        ValidatorInterface $validator,
        Request $request
    ): Response
    {

//        $form = $this->createForm(TeamType::class, new Team());

        $input = [
            'name' => $request->get('name'),
            'country' => $request->get('country'),
            'money_balance' => $request->get('money_balance')
        ];

        $team = new Team();
        $team->setName($input['name']);
        $team->setCountry($input['country']);
        $team->setMoneyBalance($input['money_balance']);
        $team->setCreatedAt(new \DateTimeImmutable());

        $validated = $validator->validate($team );

        if ($validated->count()) {
            $this->logger->info('validation check');
            $this->logger->error($validated[0]->getMessage());
            $this->logger->error($validated);
            $this->addFlash('error', $validated[0]->getMessage());

            return $this->redirectToRoute('teams.create');
        }

        try {
            if ($request->get('player_ids') !== null) {
                $this->logger->info('player ids');
                $this->logger->info($request->get('player_ids'));
                foreach ($request->get('player_ids') as $playerId) {
                    $team->addPlayer($playerRepository->find($playerId));
                }
            }
            $teamRepository->save($team, true);
        } catch (\Throwable $exception) {

            $this->logger->error($exception);

            $this->addFlash('error', "An Error Occurred While Creating Team");

            return $this->redirectToRoute('teams.create');
        }

        $this->addFlash('success', "Team Created Successfully!");

        return $this->redirectToRoute('teams.index');
    }


    #[Route('/edit/{id}', name: 'edit', methods: ["GET"])]
    public function edit(
        Team $team,
        PlayerRepository $playerRepository,
        ValidatorInterface $validator,
        Request $request
    ): Response
    {
        return $this->render('team/edit.html.twig', [
            'team' => $team,
            'players' => $playerRepository->findAll()
        ]);
    }

    #[Route('/edit/{id}', name: 'update', methods: ["POST"])]
    public function update(
        Team $team,
        PlayerRepository $playerRepository,
        TeamRepository $teamRepository,
        Request $request
    ): Response
    {
        $input = [
            'name' => $request->get('name'),
            'country' => $request->get('country'),
            'money_balance' => (float)$request->get('money_balance')
        ];

        $team->setName($input['name']);
        $team->setCountry($input['country']);
        $team->setMoneyBalance($input['money_balance']);
        $team->setUpdatedAt(new \DateTimeImmutable());

        $validated = $this->validator->validate($team );

        if ($validated->count()) {
            $this->addFlash('error', $validated[0]->getMessage());

            return $this->redirectToRoute('teams.create');
        }

        try {
            if ($request->get('player_ids') !== null) {
                foreach ($request->get('player_ids') as $playerId) {
                    $team->addPlayer($playerRepository->find($playerId));
                }
            }

            $teamRepository->save($team, true);
        } catch (\Throwable $exception) {

            $this->logger->error($exception);

            $this->addFlash('error', "An Error Occurred While Updating Team");

            return $this->redirectToRoute('teams.edit');
        }

        $this->addFlash('success', "Team Updated Successfully!");

        return $this->redirectToRoute('teams.index');
    }

    #[Route('/show/{id}', name: 'show', methods: ["GET"])]
    public function show(Team $team): Response
    {
        return $this->render('team/show.html.twig', [
            'team' => $team,
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(
        Request $request,
        Team $team,
        TeamRepository $teamRepository
    ): Response
    {
        if ($this->isCsrfTokenValid('delete', $request->headers->get('x-csrf-token'))) {

            foreach ($team->getPlayers() as $player) {
                $team->removePlayer($player);
            }

            $teamRepository->remove($team, true);

            $this->addFlash('success', "Team Deleted");
        } else {
            $this->addFlash('error', "Team Could Not Deleted");
        }

        return $this->json(['status' => true]);
    }
}
