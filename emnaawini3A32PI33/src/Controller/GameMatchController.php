<?php

namespace App\Controller;

use App\Entity\GameMatch;
use App\Repository\GameMatchRepository;
use App\Form\GameMatchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
// Removed duplicate import to resolve naming conflict

#[Route('/match')]
final class GameMatchController extends AbstractController
{
    #[Route(name: 'app_game_match_index', methods: ['GET'])]
    public function index(GameMatchRepository $gameMatchRepository): Response
    {
<<<<<<< Updated upstream
        return $this->render('game_match/index.html.twig', [
            'game_matches' => $gameMatchRepository->findAll(),
=======
        $queryBuilder = $gameMatchRepository->createQueryBuilder('gm')
            ->leftJoin('gm.equipe1', 'e1')
            ->leftJoin('gm.equipe2', 'e2')
            ->addSelect('e1', 'e2');

        if ($team = $request->query->get('team')) {
            $queryBuilder->andWhere('e1.nom_equipe LIKE :team OR e2.nom_equipe LIKE :team')
                ->setParameter('team', '%' . $team . '%');
        }

        if ($status = $request->query->get('status')) {
            $queryBuilder->andWhere('gm.statut_match = :status')
                ->setParameter('status', $status);
        }

        if ($date = $request->query->get('date')) {
            $queryBuilder->andWhere('gm.dateMatch = :date')
                ->setParameter('date', $date);
        }

        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1),
            10 
        );

        return $this->render('game_match/index.html.twig', [
            'game_matches' => $pagination, 
>>>>>>> Stashed changes
        ]);
    }

    #[Route('/new', name: 'app_game_match_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $gameMatch = new GameMatch();
        $form = $this->createForm(GameMatchType::class, $gameMatch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($gameMatch);
            $entityManager->flush();

<<<<<<< Updated upstream
=======
            if ($gameMatch->getStatutMatch() === 'completed') {
                $this->statistiquesEquipeUpdater->update($gameMatch);
            }

>>>>>>> Stashed changes
            return $this->redirectToRoute('app_game_match_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('game_match/new.html.twig', [
<<<<<<< Updated upstream
=======
            'form' => $form,
        ]);
    }

    #[Route('/user', name: 'app_game_match_user_index', methods: ['GET'])]
    public function userIndex(Request $request, GameMatchRepository $gameMatchRepository, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $gameMatchRepository->createQueryBuilder('gm')
            ->leftJoin('gm.equipe1', 'e1')
            ->leftJoin('gm.equipe2', 'e2')
            ->addSelect('e1', 'e2');

        if ($team = $request->query->get('team')) {
            $queryBuilder->andWhere('e1.nom_equipe LIKE :team OR e2.nom_equipe LIKE :team')
                ->setParameter('team', '%' . $team . '%');
        }

        if ($status = $request->query->get('status')) {
            $queryBuilder->andWhere('gm.statut_match = :status')
                ->setParameter('status', $status);
        }

        if ($date = $request->query->get('date')) {
            $queryBuilder->andWhere('gm.dateMatch = :date')
                ->setParameter('date', $date);
        }

        $pagination = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->getInt('page', 1), 
            10
        );

        return $this->render('game_match/index_user.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/user/{id}', name: 'app_game_match_user_show', methods: ['GET'])]
    public function userShow(GameMatch $gameMatch, EntityManagerInterface $entityManager): Response
    {
        $gameMatch = $entityManager->getRepository(GameMatch::class)
            ->createQueryBuilder('gm')
            ->leftJoin('gm.tournoi', 't')
            ->addSelect('t')
            ->where('gm.id = :id')
            ->setParameter('id', $gameMatch->getId())
            ->getQuery()
            ->getOneOrNullResult();

        if (!$gameMatch) {
            throw $this->createNotFoundException('Game match not found.');
        }

        $statRepo = $entityManager->getRepository(StatistiquesEquipe::class);
        $statsEquipe1 = $statRepo->findOneBy(['equipe' => $gameMatch->getEquipe1()]);
        $statsEquipe2 = $statRepo->findOneBy(['equipe' => $gameMatch->getEquipe2()]);

        $probabilities = $this->calculateProbabilities($statsEquipe1, $statsEquipe2);

        return $this->render('game_match/show_user.html.twig', [
>>>>>>> Stashed changes
            'game_match' => $gameMatch,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_game_match_show', methods: ['GET'])]
    public function show(GameMatch $gameMatch): Response
    {
        return $this->render('game_match/show.html.twig', [
            'game_match' => $gameMatch,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_game_match_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GameMatch $gameMatch, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GameMatchType::class, $gameMatch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_game_match_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('game_match/edit.html.twig', [
            'game_match' => $gameMatch,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_game_match_delete', methods: ['POST'])]
    public function delete(Request $request, GameMatch $gameMatch, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $gameMatch->getId(), $request->request->get('_token'))) {
            $entityManager->remove($gameMatch);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_game_match_index', [], Response::HTTP_SEE_OTHER);
    }
<<<<<<< Updated upstream
=======

    private function calculateProbabilities(?StatistiquesEquipe $statsEquipe1, ?StatistiquesEquipe $statsEquipe2): array
    {
        if (!$statsEquipe1 || !$statsEquipe2) {
            return ['equipe1' => 'N/A', 'equipe2' => 'N/A'];
        }

        $offensiveStrengthEquipe1 = $statsEquipe1->getButsMarques() / max($statsEquipe1->getMatchsJoues(), 1);
        $defensiveStrengthEquipe1 = $statsEquipe1->getButsEncaisses() / max($statsEquipe1->getMatchsJoues(), 1);

        $offensiveStrengthEquipe2 = $statsEquipe2->getButsMarques() / max($statsEquipe2->getMatchsJoues(), 1);
        $defensiveStrengthEquipe2 = $statsEquipe2->getButsEncaisses() / max($statsEquipe2->getMatchsJoues(), 1);

        $strengthEquipe1 = $offensiveStrengthEquipe1 / max($defensiveStrengthEquipe2, 1);
        $strengthEquipe2 = $offensiveStrengthEquipe2 / max($defensiveStrengthEquipe1, 1);

        $totalStrength = $strengthEquipe1 + $strengthEquipe2;
        if ($totalStrength > 0) {
            $probEquipe1 = ($strengthEquipe1 / $totalStrength) * 100;
            $probEquipe2 = ($strengthEquipe2 / $totalStrength) * 100;
        } else {
            $probEquipe1 = $probEquipe2 = 50; 
        }

        return [
            'equipe1' => round($probEquipe1, 2),
            'equipe2' => round($probEquipe2, 2),
        ];
    }
>>>>>>> Stashed changes
}
