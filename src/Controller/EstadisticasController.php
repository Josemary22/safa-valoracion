<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\RankingCharacterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EstadisticasController extends AbstractController
{
    #[Route('/estadisticas/{id}', name: 'app_estadisticas')]
    public function index(int $id, CategoryRepository $categoryRepository, RankingCharacterRepository $rankingCharacterRepository
    ): Response {
        $category = $categoryRepository->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Categoría no encontrada');
        }

        $characters = $rankingCharacterRepository->ordenarRankingEstadisticas($category);

        return $this->render('estadisticas/estadisticas.html.twig', [
            'category'   => $category,
            'characters' => $characters,
        ]);
    }

    #[Route('/categoryEstadisticas', name: 'app_categoryEstadisticas')]
    public function categoryEstadisticas(CategoryRepository $categoriaRepository): Response
    {
        $categorias = $categoriaRepository->findAll();

        return $this->render('estadisticas/categoryEstadisticas.html.twig', [
            'categorias' => $categorias,
        ]);
    }
}
