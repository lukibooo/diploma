<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    #[Route('/main', name: 'main')]
    public function main(): Response
    {
        $number = random_int(0, 100);

        return $this->render('main.html.twig', [

        ]);
    }

    #[Route('/about', name: 'about')]
    public function about(): Response
    {
        $number = random_int(0, 100);

        return $this->render('about.html.twig', [

        ]);
    }

    #[Route('/search', name: 'search')]
    public function search(): Response
    {
        $number = random_int(0, 100);

        return $this->render('search.html.twig', [

        ]);
    }

    #[Route('/profile', name: 'profile')]
    public function profile(): Response
    {
        $number = random_int(0, 100);

        return $this->render('profile.html.twig', [

        ]);
    }

    #[Route('/news', name: 'news')]
    public function news(): Response
    {
        $number = random_int(0, 100);

        return $this->render('news.html.twig', [

        ]);
    }
}
