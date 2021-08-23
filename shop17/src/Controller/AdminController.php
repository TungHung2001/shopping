<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


use function PHPUnit\Framework\throwException;

class AdminController extends AbstractController
{
    
    // /**
    //  * @Route("/admin/api/viewall",methods={"GET"}, name="view_all_product_api")
    //  */
    // public function viewAllProductAPI(SerializerInterface $serializer)
    // {
    //     $Products = $this->getDoctrine()
    //                     ->getRepository(Product::class)
    //                     ->findAll();
    //     $data = $serializer->serialize($Products,'jsons');
    //     return new Response(
    //         $data,
    //         Response::HTTP_OK,
    //         [
    //             "content-type" => "application/json"
    //         ]
    //     );

    // }
    // /**
    //  * @Route("/admin/api/viewid/{id}",methods={"GET"}, name="view_product_by_id_api")
    //  */
    // public function viewProductByIdAPI(SerializerInterface $serializer, $id)
    // {
    //     $Product = $this->getDoctrine()
    //                     ->getRepository(Product::class)
    //                     ->findAll($id);
       
        

    //     $data = $serializer->serialize($Product,"xml");
    //     return new Response(
    //         $data,
    //         200,
    //         [
    //             "content-type" =>"application/xml"
    //         ]
    //         );
    // }   

    // /**
    //  * @Route("/admin/api/delete/{id}",methods={"DELETE"}, name="delete_product_api")
    //  */
    // public function deleteProductAPI ($id){
    //     try{
    //         $Product = $this->getDoctrine()
    //                         ->getRepository(Product::class)
    //                         ->find($id);
    //         if($Product == null){
    //             return new Response(
    //                 "Product DELETED",
    //                 Response::HTTP_FOUND
    //             );
    //         }
    //         $manager = $this->getDoctrine()
    //                         ->getManager();
    //         $manager->remove($Product);
    //         $manager->flush();
    //         return new Response(
    //             null,
    //             Response::HTTP_BAD_REQUEST
    //         );
    //     }catch(\Exception $e) {
    //         $error = array("Error" => $e->getMessage());
    //         return new Response(
    //             json_encode($error),
    //             Response::HTTP_BAD_REQUEST,
    //             [
    //                 "content-type" => "application/json"
    //             ]
    //             );
    //     }
    // }
    
    // /**
    //  * @Route("/admin/api/create",methods={"POST"}, name="create_product_api")
    //  */
    // public function createProductAPI(Request $request){
    //     try{
    //         $Product = new Product();
    //         $data = json_decode($request->getContent(),true);
    //         $Product->setName($data['name']);
    //         $Product->setCategory($data['category']);
    //         $Product->setDescription($data['description']);
    //         $Product->setPrice($data['price']);
    //         $manager = $this->getDoctrine()
    //                         ->getManager();
    //         $manager ->persist($Product);
    //         $manager->flush();
    //         return new Response(
    //             "Product Created",
    //             Response::HTTP_OK
    //         );
    //     }catch(\Exception $e) {
    //         $error = array("Error" => $e->getMessage());
    //         return new Response(
    //             json_encode($error),
    //             Response::HTTP_BAD_REQUEST,
    //             [
    //                 "content-type" => "application/json"
    //             ]
    //             );
    //     }
    // }
       
    // /**
    //  * @Route("/admin/api/update/{id}",methods={"PUT"}, name="update_product_api")
    //  */
    // public function updateProductAPI(Request $request, $id){
    //     try{
    //         $Product = $this->getDoctrine()->getRepository(Product::class)->find($id);
    //         $data = json_decode($request->getContent(),true);
    //         $Product->setName($data['name']);
    //         $Product->setCategory($data['category']);
    //         $Product->setDescription($data['description']);
    //         $Product->setPrice($data['price']);
    //         $manager = $this->getDoctrine()
    //                         ->getManager();
    //         $manager ->persist($Product);
    //         $manager->flush();
    //         return new Response(
    //             "Product Updated",
    //             Response::HTTP_OK
    //         );
    //     }catch(\Exception $e) {
    //         $error = array("Error" => $e->getMessage());
    //         return new Response(
    //             json_encode($error),
    //             Response::HTTP_BAD_REQUEST,
    //             [
    //                 "content-type" => "application/json"
    //             ]
    //             );
    //     }
    // }


    /**
     * @Route("/admin", name="product_list")
     */

    public function viewAllProduct(){
        $Products = $this->getDoctrine()
                        ->getRepository(Product::class)
                        ->findAll();
        return $this->render(
            "admin/index.html.twig",
            [
                'Products' => $Products
            ]
        );

    }

   
    /**
     * @Route("/admin/detail/{id}", name="product_detail")
     */
    public function viewProductByID($id){
        $Product = $this->getDoctrine()
                        ->getRepository(Product::class)
                        ->find($id);
        if($Product==null){
            $this->addFlash("Error", "product ID in invalid");
            return $this->redirectToRoute("product_list");
        }
        
        return $this->render(
                "admin/detail.html.twig",
                            [
                                'Product' => $Product
                            ]
                );
    }
    /**
     * @Route("/admin/delete/{id}", name="product_delete")
     */

    public function deleteProduct($id){
    $Product = $this->getDoctrine()
                    ->getRepository(Product::class)
                    ->find($id);
    
    
    if ($Product == null) {
        $this->addFlash("Error", "product invalid");
        return $this->redirectToRoute("product_list");
    }
    $manager = $this->getDoctrine()
                    ->getManager();
    $manager->remove($Product);
    $manager->flush();
        
        $this->addFlash("Success", "product deleted");
        return $this->redirectToRoute("product_list");
    }

    /**
     * @Route("/admin/create", name="product_create")
     */
        public function createProduct (Request $request){
            $Product = new Product();
            $form = $this->createForm(ProductType::class,$Product);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                
                 //get image from upload file
                $image = $Product->getImage();
            
                //create unique image name
                $fileName = md5(uniqid());
                //get image extension
                $fileExtension = $image->guessExtension();
                //combine image name + image extension => complete image name
                $imageName = $fileName . '.' . $fileExtension;
            
                //move uploaded image to defined location
                try {
                    $image->move(
                    $this->getParameter('product_image'), $imageName
                );
                } catch (FileException $e) {
                throwException($e);
                }

            //set imageName to database
                $Product->setImage($imageName);

                $manager = $this->getDoctrine()
                                ->getManager();
                $manager->persist($Product);
                $manager->flush();
                $this->addFlash("Success", "Add successfully !");
                return $this->redirectToRoute("product_list");

            }
            return $this->render(
                "admin/create.html.twig",
                [
                    "form" => $form->createView()
                ]    
            );
        }
    
     /**
     * @Route("/admin/update/{id}", name="product_update")
     */

    public function updateProduct(Request $request, $id){
        $Product = $this->getDoctrine()
                        ->getRepository(Product::class)
                        ->find($id);
        $form = $this->createForm(ProductType::class,$Product);
        $form->handleRequest($request);

        if($form->isSubmitted()&& $form->isValid()){
            $uploadedFile = $form['Image']->getData();
            if($uploadedFile !=null){
                //get image from upload file
                $image = $Product->getImage();
                                    
                //create unique image name
                $fileName = md5(uniqid());
                //get image extension
                $fileExtension = $image->guessExtension();
                //combine image name + image extension => complete image name
                $imageName = $fileName . '.' . $fileExtension;

                //move uploaded image to defined location
                try {
                    $image->move(
                        $this->getParameter('product_image'), $imageName
                    );
                } catch (FileException $e) {
                    throwException($e);
                }
                //set imageName to database
                $Product->setImage($imageName);
            }


            $manager = $this->getDoctrine()
                            ->getManager();
            $manager->persist($Product);
            $manager->flush();
            $this->addFlash("Success","Update product successfully !");
            return $this->redirectToRoute("product_list");
        }
        return $this->render(
                "admin/update.html.twig",
                [
                    "form" => $form->createView()
                ]
        );
    }
}
      


