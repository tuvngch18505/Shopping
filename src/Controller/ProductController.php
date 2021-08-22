<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use function PHPUnit\Framework\throwException;

/**
 * @IsGranted("ROLE_USER")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product_index")
     */
    public function indexProduct()
    {
        $Products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findAll();
        return $this->render(
            'Product/index.html.twig',
            [
                'products' => $Products
            ]
        );
    }
    /**
     * @Route("/product/detail/{id}", name ="product_detail")
     */
    public function detailProduct($id)
    {
        $Product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        return $this->render(
            "Product/detail.html.twig",
            [
                "product" => $Product
            ]
        );
    }
    /**
     * @IsGranted("ROLE_ADMIN")
     *
     * @Route("/Product/create", name="product_create")
     */
    public function createnewProduct(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $product->getImage();
            $fileName = md5(uniqid());
            $fileExtension = $image->guessExtension();
            $imageName = $fileName . '.' . $fileExtension;
            try {
                $image->move(
                    $this->getParameter('guitar_image'),
                    $imageName
                );
            } catch (FileException $e) {
                throwException($e);
            }
            //set imageName to database
            $product->setImage($imageName);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($product);
            $manager->flush();
            $this->addFlash("Info", "Add successfully !");
            return $this->redirectToRoute("product_index");
        }

        return $this->render(
            "Product/create.html.twig",
            [
                'form' => $form->createView()
            ]
        );
    }
    /**
     * @IsGranted("ROLE_ADMIN")
     *
     * @Route ("/Product/update/{id}", name="product_update")
     */
    public function updateProduct(Request $request, $id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadFile = $form['image']->getData();
            if ($uploadFile != null) {
                //get Image from uploaded file
                $image = $product->getImage();

                //create an unique image name
                $fileName = md5(uniqid());
                //get image extension
                $fileExtension = $image->guessExtension();
                //merge image name & image extension => get a complete image name
                $imageName = $fileName . '.' . $fileExtension;

                //move upload file to a predefined location
                try {
                    $image->move(
                        $this->getParameter('guitar_image'),
                        $imageName
                    );
                } catch (FileException $e) {
                    throwException($e);
                }

                //set imageName to database
                $product->setImage($imageName);
            }

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($product);
            $manager->flush();

            $this->addFlash("Info", "Update product succeed !");
            return $this->redirectToRoute("product_index");
        }

        return $this->render(
            'product/update.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @IsGranted("ROLE_STAFF")
     * @Route("/Product/delete/{id}", name="product_delete")
     */

    public function deleteProduct($id)
    {
        $Product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);
        if ($Product == null) {
            $this->addFlash("Error", "Delete failed !");
            return $this->redirectToRoute("product_index");
        }
        //if genre is not null
        $manager = $this->getDoctrine()
            ->getManager();
        $manager->remove($Product);
        $manager->flush();
        $this->addFlash("Info", "Delete succeed !");
        return $this->redirectToRoute("product_index");
    }
}
