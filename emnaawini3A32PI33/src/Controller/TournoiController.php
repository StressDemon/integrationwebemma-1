<?php

namespace App\Controller;

use App\Entity\Tournoi;
use App\Form\TournoiType;
use App\Repository\TournoiRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tournoi')]
final class TournoiController extends AbstractController
{
    #[Route('/', name: 'app_tournoi_index', methods: ['GET'])]
    public function index(TournoiRepository $tournoiRepository): Response
    {
        $tournois = $tournoiRepository->findAll();

        if (!$tournois) {
            $this->addFlash('error', 'No tournaments found.');
        }

        return $this->render('tournoi/index.html.twig', [
            'tournois' => $tournois,
        ]);
    }

    #[Route('/new', name: 'app_tournoi_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tournoi = new Tournoi();
        $form = $this->createForm(TournoiType::class, $tournoi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tournoi);
            $entityManager->flush();

            return $this->redirectToRoute('app_tournoi_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tournoi/new.html.twig', [
            'tournoi' => $tournoi,
            'form' => $form,
        ]);
    }

    #[Route('/{id_tournoi}', name: 'app_tournoi_show', methods: ['GET'])]
    public function show(int $id_tournoi, TournoiRepository $tournoiRepository): Response
    {
        $tournoi = $tournoiRepository->find($id_tournoi);

        if (!$tournoi) {
            throw $this->createNotFoundException('The tournoi does not exist.');
        }

<<<<<<< Updated upstream
=======
        $matches = $tournoi->getMatchs();

>>>>>>> Stashed changes
        return $this->render('tournoi/show.html.twig', [
            'tournoi' => $tournoi,
        ]);
    }

    #[Route('/{id_tournoi}/edit', name: 'app_tournoi_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id_tournoi, TournoiRepository $tournoiRepository, EntityManagerInterface $entityManager): Response
    {
        $tournoi = $tournoiRepository->find($id_tournoi);

        if (!$tournoi) {
            throw $this->createNotFoundException('The tournoi does not exist.');
        }

        $form = $this->createForm(TournoiType::class, $tournoi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_tournoi_index', [], Response::HTTP_SEE_OTHER);
        }

<<<<<<< Updated upstream
        return $this->render('tournoi/edit.html.twig', [
            'tournoi' => $tournoi,
            'form' => $form,
=======
        $equipes = $equipeRepository->findAll();
        $terrains = $terrainRepository->findAll();
        $matches = $tournoi->getMatchs(); 

        return $this->render('tournoi/edit.html.twig', [
            'tournoi' => $tournoi,
            'form' => $form,
            'equipes' => $equipes,
            'terrains' => $terrains,
            'matches' => $matches,
>>>>>>> Stashed changes
        ]);
    }

    #[Route('/{id_tournoi}', name: 'app_tournoi_delete', methods: ['POST'])]
    public function delete(Request $request, int $id_tournoi, TournoiRepository $tournoiRepository, EntityManagerInterface $entityManager): Response
    {
        $tournoi = $tournoiRepository->find($id_tournoi);

        if (!$tournoi) {
            throw $this->createNotFoundException('The tournoi does not exist.');
        }

        if ($this->isCsrfTokenValid('delete' . $tournoi->getIdTournoi(), $request->request->get('_token'))) {
            $entityManager->remove($tournoi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_tournoi_index', [], Response::HTTP_SEE_OTHER);
    }
}
