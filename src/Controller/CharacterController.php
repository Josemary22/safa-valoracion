<?php

namespace App\Controller;

use App\Repository\CharacterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CharacterController extends AbstractController
{
    #[Route('/character', name: 'app_character')]
    public function index(CharacterRepository $repository): Response
    {
        $character_list = $repository->findAll();

        return $this->render('character/character.html.twig', [
            'controller_name' => 'CharacterController',
            'list' => $character_list,
        ]);
    }
}
