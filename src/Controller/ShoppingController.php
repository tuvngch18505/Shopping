<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShoppingController extends AbstractController
{
    /**
     * @Route("/", name="shopping_index")
     */
    public function indexProducts(ProductRepository $productRepository)
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        return $this->render('Shopping/index.html.twig',[
            'shoppings' => $productRepository->findALl(),
        ]);
    }
    /**
     * @Route("/shopping/detail/{id}", name ="shopping_detail")
     */
    public function detailShopping($id)
    {
        $shopping = $this->getDoctrine()->getRepository(Product::class)->find($id);
        return $this->render(
            "shopping/detail.html.twig",
            [
                "shopping" => $shopping
            ]
        );
    }
      /**
     * @Route("/shopping/about", name ="shopping_about")
     */
    public function shoppingAbout()
    {
        $shopping = "Copyright By Vu Ngoc Tu";
        return $this->render(
            
            "shopping/about.html.twig",
            [
                "shopping" => $shopping
            ]
        );
    }
}
