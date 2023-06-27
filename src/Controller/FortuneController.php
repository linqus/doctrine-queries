<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FortuneController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(CategoryRepository $categoryRepository, Request $request): Response
    {
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
    public function showCategory(int $id, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findWithFortunesJoin($id);

        if (!$category) {
            throw $this->createNotFoundException('No such category');
        }

        return $this->render('fortune/showCategory.html.twig',[
            'category' => $category
        ]);
    }
}
