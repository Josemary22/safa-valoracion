<?php

namespace App\Controller;

use App\Entity\Character;
use App\Entity\Phrases;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_site')]
    public function index(): Response
    {
        return $this->render('admin/admin.html.twig');
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/character/load', name: 'app_cargarDatos')]
    public function data_load(HttpClientInterface $httpClient, EntityManagerInterface $entityManager): Response
    {
        $url = 'https://thesimpsonsapi.com/api/characters';

        do {
            $response = $httpClient->request('GET', $url);
            $content = $response->toArray();

            foreach ($content['results'] as $element) {
                // Esto es para evitar duplicados usando el code
                $existing = $entityManager
                    ->getRepository(Character::class)
                    ->findOneBy(['code' => $element['id']]);
                if ($existing) {
                    continue;
                }

                $character = new Character();
                $character->setAge($element['age'] ?? null);
                if (!empty($element['birthdate'])) {
                    $character->setBirthdate(new \DateTime($element['birthdate']));
                } else {
                    $character->setBirthdate(null);
                }
                $character->setGender($element['gender']);
                $character->setName($element['name']);
                $character->setOccupation($element['occupation']);
                $character->setPortraitPath($element['portrait_path']);
                $character->setStatus($element['status']);
                $character->setCode($element['id']);

                if (!empty($element['phrases'])) {
                    foreach ($element['phrases'] as $phraseText) {
                        $phrase = new Phrases();
                        $phrase->setPhrase($phraseText);
                        $character->addPhrase($phrase);
                    }
                }

                $entityManager->persist($character);
            }

            // Esto es para pasar a la siguiente página
            $url = $content['next'];

        } while ($url !== null);

        $entityManager->flush();

        return $this->render('admin/cargarDatos.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}



