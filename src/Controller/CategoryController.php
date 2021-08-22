<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\CategoryType;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use function PHPUnit\Framework\throwException;


class CategoryController extends AbstractController
{


    /** 
     * @Route ("/category", name="category_index")
     */
    public function indexCategory()
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();
        return $this->render(
            "Category/index.html.twig",
            [
                "categories" => $category
            ]
        );
    }
    /**
     * @Route("/category/detail/{id}", name="category_detail")
     */
    public function CategoryDetail($id)
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->find($id);
        return $this->render(
            "category/detail.html.twig",
            [
                "category" => $category
            ]
        );
    }
    /**
     *  @IsGranted("ROLE_ADMIN")
     * 
     * @Route("/category/create", name="category_create")
     */
    public function createnewCategory(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($category);
            $manager->flush();
            $this->addFlash("Info", "Add successfully !");
            return $this->redirectToRoute("category_index");
        }

        return $this->render(
            "category/create.html.twig",
            [
                'form' => $form->createView()
            ]
        );
    }
    /**
     * @IsGranted("ROLE_ADMIN")
     * @IsGranted("ROLE_STAFF")
     * @Route ("/category/update/{id}", name="category_update")
     */
    public function updateGenre(Request $request, $id)
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->find($id);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($category);
            $manager->flush();
            $this->addFlash("Info", "Update successfully !");
            return $this->redirectToRoute("category_index");
        }

        return $this->render(
            "category/update.html.twig",
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @IsGranted("ROLE_STAFF")
     * @Route("/category/delete/{id}", name="category_delete")
     */

    public function deleteCategory($id)
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->find($id);
        if ($category == null) {
            $this->addFlash("Error", "Delete failed !");
            return $this->redirectToRoute("genre_index");
        }
        //if genre is not null
        $manager = $this->getDoctrine()
            ->getManager();
        $manager->remove($category);
        $manager->flush();
        $this->addFlash("Info", "Delete succeed !");
        return $this->redirectToRoute("genre_index");
    }
}
