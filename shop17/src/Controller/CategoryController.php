<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Container1WCRSro\getDoctrine_UlidGeneratorService;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category_list")
     */
    public function listCategory(){
        $Categorys = $this->getDoctrine()->getRepository(Category::class)->findAll();
        return $this->render(
            'category/index.html.twig',
            [
                'Categorys' => $Categorys
            ]
            );
    }
   /**
 * @Route("/category/detail/{id}", name="category_detail")
 */
    public function detailCategory($id){
        $Category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        return $this->render(
            'category/detail.html.twig',
            [
                'Category' => $Category,
            ]
            );
    }
 /**
 * @Route("/category/delete/{id}", name="category_delete")
 */
    public function deleteCategory($id){
        $Category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        
        if($Category == null){
            $this->addFlash("Error", "Invalid ID");
            return $this->redirectToRoute("category_list");

        }
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($Category);
        $manager->flush();

        $this -> addFlash("Success", "delete succeed");
        return $this->redirectToRoute('category_list');
    }
 /**
 * @Route("/category/create", name="category_create")
 */
    public function createCategory(Request $request){
        $Category = new Category();
        $form = $this->createForm(CategoryType::class, $Category);
        $form->handleRequest($request);

        if($form->isSubmitted()&& $form->isValid()){
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($Category);
            $manager->flush();

            $this->addFlash("Success", "Create success");
            return $this->redirectToRoute("category_list");
        }
        return $this->render(
            'category/create.html.twig',
            [
                'form' => $form->createView()
            ]
            );
    }
    /**
 * @Route("/category/update/{id}", name="category_update")
 */
public function updateCategory(Request $request, $id){
    $Category = $this->getDoctrine()->getRepository(Category::class)->find($id);
    $form = $this->createForm(CategoryType::class, $Category);
    $form->handleRequest($request);

    if($form->isSubmitted()&& $form->isValid()){
        $manager = $this->getDoctrine()->getManager();
        $manager->persist($Category);
        $manager->flush();

        $this->addFlash("Success", "Create success");
        return $this->redirectToRoute("category_list");
    }
    return $this->render(
        'category/create.html.twig',
        [
            'form' => $form->createView()
        ]
        );
}

}
