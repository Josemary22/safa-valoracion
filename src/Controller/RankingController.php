<?php

namespace App\Controller;

use App\Entity\Ranking;
use App\Entity\RankingCharacter;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\CharacterRepository;
use App\Repository\RankingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RankingController extends AbstractController
{
    #[Route('/ranking/{id}', name: 'app_ranking')]
    public function index(
        int $id,
        CategoryRepository $categoryRepository,
        RankingRepository $rankingRepository
    ): Response {
        $category = $categoryRepository->find($id);

        if (!$category) {
            throw $this->createNotFoundException();
        }

        $userRanking = null;
        $userPositions = [];

        if ($this->getUser()) {
            $userRanking = $rankingRepository->findOneBy([
                'user' => $this->getUser(),
                'category' => $category
            ]);

            if ($userRanking) {
                foreach ($userRanking->getRankingCharacters() as $rc) {
                    $userPositions[$rc->getPosition()] = $rc->getCharacter()->getId();
                }
            }
        }

        return $this->render('ranking/ranking.html.twig', [
            'category' => $category,
            'characters' => $category->getCharacters(),
            'userRanking' => $userRanking,
            'userPositions' => $userPositions
        ]);
    }

    #[Route('/categoryRanking', name: 'app_categoryRanking')]
    public function categoryRanking(CategoryRepository $categoryRepository): Response
    {
        return $this->render('ranking/categoryRanking.html.twig', [
            'categorias' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/ranking/create/{id}', name: 'ranking_create', methods: ['POST'])]
    public function create(
        int $id,
        Request $request,
        CategoryRepository $categoryRepository,
        CharacterRepository $characterRepository,
        RankingRepository $rankingRepository,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $category = $categoryRepository->find($id);
        if (!$category) {
            throw $this->createNotFoundException();
        }

        $existingRanking = $rankingRepository->findOneBy([
            'user' => $user,
            'category' => $category
        ]);

        if ($existingRanking) {
            return $this->redirectToRoute('app_ranking', ['id' => $id]);
        }

        $rankingData = $request->request->all('ranking');

        $ranking = new Ranking();
        $ranking->setUser($user);
        $ranking->setCategory($category);

        foreach ($rankingData as $position => $characterId) {
            if (!$characterId) continue;
            $character = $characterRepository->find($characterId);
            if (!$character) continue;

            $rc = new RankingCharacter();
            $rc->setCharacter($character);
            $rc->setPosition((int)$position);

            $ranking->addRankingCharacter($rc);
        }

        $em->persist($ranking);
        $em->flush();

        return $this->redirectToRoute('app_ranking', ['id' => $id]);
    }

    #[Route('/ranking/update/{id}', name: 'ranking_update', methods: ['POST'])]
    public function update(
        Ranking $ranking,
        Request $request,
        CharacterRepository $characterRepository,
        EntityManagerInterface $em
    ): Response {
        if ($ranking->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $rankingData = $request->request->all('ranking');

        foreach ($ranking->getRankingCharacters() as $rc) {
            $em->remove($rc);
        }

        $em->flush();

        foreach ($rankingData as $position => $characterId) {

            if (!$characterId) continue;

            $character = $characterRepository->find($characterId);
            if (!$character) continue;

            $rc = new RankingCharacter();
            $rc->setCharacter($character);
            $rc->setPosition((int)$position);

            $ranking->addRankingCharacter($rc);
        }

        $em->flush();

        return $this->redirectToRoute('app_ranking', [
            'id' => $ranking->getCategory()->getId()
        ]);
    }
}
