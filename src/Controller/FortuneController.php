<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\FortuneCookieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FortuneController extends AbstractController
{

    public function __construct(private EntityManagerInterface $entityManager)
    {

    }

    #[Route('/', name: 'app_homepage')]
    public function index(CategoryRepository $categoryRepository, Request $request): Response
    {

        $this->entityManager->getFilters()
            ->enable('fortuneCookie_discontinued')
            //->setParameter('discontinued', true)
        ;

        $searchString = $request->query->get('q');

        if ($searchString) {
            $categories = $categoryRepository->search($searchString);
        } else {
            $categories = $categoryRepository->findAllOrdered();
        }
        

        return $this->render('fortune/homepage.html.twig',[
            'categories' => $categories
        ]);
    }

    #[Route('/category/{id}', name: 'app_category_show')]
    public function showCategory(int $id, CategoryRepository $categoryRepository, FortuneCookieRepository $fortuneCookieRepository): Response
    {
        $category = $categoryRepository->find($id);

        if (!$category) {
            throw $this->createNotFoundException('No such category');
        }

        $fortuneStats = $fortuneCookieRepository->fortunePrinted($category);

        //$fortunePrinted = 10321021;

        return $this->render('fortune/showCategory.html.twig',[
            'category' => $category,
            'fortuneStats' => $fortuneStats,
        ]);
    }
}
