<?php

namespace App\Controller;

use App\Entity\Ranking;
use App\Entity\RankingCharacter;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\CharacterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RankingController extends AbstractController
{
    #[Route('/ranking/{id}', name: 'app_ranking')]
    public function index(int $id, CategoryRepository $categoryRepository
    ): Response {
        $category = $categoryRepository->find($id);

        if (!$category) {
            throw $this->createNotFoundException();
        }

        return $this->render('ranking/ranking.html.twig', [
            'category' => $category,
            'characters' => $category->getCharacters(),
        ]);
    }

    #[Route('/categoryRanking', name: 'app_categoryRanking')]
    public function categoryRanking(CategoryRepository $categoriaRepository): Response
    {
        $categorias = $categoriaRepository->findAll();

        return $this->render('ranking/categoryRanking.html.twig', [
            'categorias' => $categorias,
        ]);
    }

    #[Route('/ranking/create/{id}', name: 'ranking_create', methods: ['POST'])]
    public function create(
        int $id,
        Request $request,
        CategoryRepository $categoryRepository,
        CharacterRepository $characterRepository,
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

        $rankingData = $request->request->all('ranking');

        $ranking = new Ranking();
        $ranking->setUser($user);
        $ranking->setCategory($category);

        foreach ($rankingData as $position => $characterId) {
            $character = $characterRepository->find($characterId);

            if (!$character) continue;

            $rc = new RankingCharacter();
            $rc->setCharacter($character);
            $rc->setPosition((int)$position);
            $ranking->addRankingCharacter($rc);
        }

        $em->persist($ranking);
        $em->flush();

        return $this->render('home/home.html.twig');
    }
}
