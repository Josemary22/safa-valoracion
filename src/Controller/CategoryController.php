<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CharacterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class CategoryController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/category', name: 'categories')]
    public function index(Request $request,
                          CharacterRepository $characterRepository,
                          EntityManagerInterface $entityManager,
                          PaginatorInterface $paginator
    ): Response {
        if ($request->isMethod('POST')) {

            $category = new Category();
            $category->setName($request->request->get('name'));
            $category->setImage($request->request->get('image'));

            $charactersSelected = $request->request->all('characters');

            foreach ($charactersSelected as $id) {
                $character = $characterRepository->find($id);
                if ($character) {
                    $category->addCharacter($character);
                }
            }

            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('categories');
        }

        $search = $request->query->get('search');
        $qb = $characterRepository->getOrdenarCategoryPagina();

        if ($search) {
            $qb->andWhere('LOWER(c.name) LIKE :search')
                ->setParameter('search', '%' . strtolower($search) . '%');
        }

        $characters = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('category/category.html.twig', [
            'characters' => $characters,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/category/{id}/edit', name: 'category_edit')]
    public function edit(
        Category $category,
        Request $request,
        CharacterRepository $characterRepository,
        EntityManagerInterface $em,
        PaginatorInterface $paginator
    ): Response {
        if ($request->isMethod('POST')) {

            $category->setName($request->request->get('name'));
            $category->setImage($request->request->get('image'));

            foreach ($category->getCharacters() as $character) {
                $category->removeCharacter($character);
            }

            foreach ($request->request->all('characters') as $idChar) {
                if ($character = $characterRepository->find($idChar)) {
                    $category->addCharacter($character);
                }
            }

            $em->flush();

            return $this->redirectToRoute('app_categoryRanking');
        }

        $search = $request->query->get('search');

        $qb = $characterRepository->getOrdenarCategoryPagina();

        if ($search) {
            $qb->andWhere('LOWER(c.name) LIKE :search')
                ->setParameter('search', '%' . strtolower($search) . '%');
        }

        $characters = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('category/editCategory.html.twig', [
            'category' => $category,
            'characters' => $characters,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/category/{id}/delete', name: 'category_delete', methods: ['POST'])]
    public function delete(Category $category, EntityManagerInterface $em): Response
    {
        $em->remove($category);
        $em->flush();

        return $this->redirectToRoute('categories');
    }
}
