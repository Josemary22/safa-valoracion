<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CharacterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    #[Route('/category', name: 'categories')]
    public function index(Request $request,
                          CharacterRepository $characterRepository,
                          EntityManagerInterface $entityManager): Response
    {

        if($request->isMethod('GET')){

            $characters = $characterRepository->findAll();

            return $this->render('category/category.html.twig',[
                'characters' => $characters,
            ]);

        }else{

            $new_category = new Category();
            $new_category->setName($request->request->get('name'));
            $new_category->setImage($request->request->get('image'));

            $characters_selected = $request->request->all('items');

            foreach($characters_selected as $idCharacter){

                $character = $characterRepository->find($idCharacter);
                if($character){
                    $new_category->addCharacter($character);
                }
            }

            $entityManager->persist($new_category);
            $entityManager->flush();

            return $this->redirectToRoute('admin_site');
        }
    }
}
