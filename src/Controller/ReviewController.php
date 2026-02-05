<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Character;
use App\Entity\Review;
use App\Repository\CharacterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReviewController extends AbstractController
{
    #[Route('/review/{id}', name: 'app_review')]
    public function index(Character $character, Request $request, EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $review = $em->getRepository(Review::class)->findOneBy([
            'user' => $user,
            'character' => $character,
        ]);

        if ($request->isMethod('POST')) {
            $stars = (int) $request->request->get('stars');
            $comment = trim($request->request->get('comment'));

            if (!$review) {
                $review = new Review();
                $review->setUser($user);
                $review->setCharacter($character);
                $em->persist($review);

                $isNew = true;
            } else {
                $isNew = false;
            }

            $review->setStars($stars);
            $review->setComment($comment);
            $em->flush();

            return $this->redirectToRoute('app_review', [
                'id' => $character->getId(),
                'edit' => 1
            ]);
        }

        $reviews = $character->getReviews();
        $totalStars = 0;

        foreach ($reviews as $r) {
            $totalStars += $r->getStars();
        }

        $countReviews = count($reviews);
        $averageStars = $countReviews > 0
            ? round($totalStars / $countReviews, 1)
            : 0;

        return $this->render('review/review.html.twig', [
            'character' => $character,
            'averageStars' => $averageStars,
            'userReview' => $review,
        ]);
    }

    #[Route('/reviewCharacter', name: 'app_reviewCharacter')]
    public function reviewCharacter(
        Request $request,
        CharacterRepository $characterRepository,
        PaginatorInterface $paginator
    ): Response {
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

        return $this->render('review/reviewCharacter.html.twig', [
            'characters' => $characters,
        ]);
    }
}
