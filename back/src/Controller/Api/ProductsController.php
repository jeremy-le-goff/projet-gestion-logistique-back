<?php

namespace App\Controller\Api;

use DateTimeImmutable;
use App\Entity\Products;
use App\Repository\BrandsRepository;
use App\Repository\ProductsRepository;
use App\Repository\CategoriesRepository;
use App\Repository\StructuresRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;


/**
 *
 * @Route("/api/products", name="api_products")
 */

class ProductsController extends AbstractController
{
    /**
     * Shows all products
     * 
     * @Route("", name="_list", methods={"GET"}))
     * 
     * @Security("is_granted('ROLE_SUPERADMIN')")
     */

    public function list(ProductsRepository $productsRepository): JsonResponse
    {
        $products = $productsRepository->findAll();
        return $this->json($products, Response::HTTP_OK, [], ["groups" => "products"]);
    }

    /**
     * Show an product
     *
     * @Route("/{id}", name="_show", methods={"GET"})
     * 
     * @Security("is_granted('ROLE_LOGISTICIAN')")
     */
    public function show(Products $products): JsonResponse
    {
        return $this->json($products, Response::HTTP_OK, [], ["groups" => "products"]);
    }

    /**
     * Create an product
     *
     * @Route("", name="_create", methods={"POST"})
     * 
     * @Security("is_granted('ROLE_LOGISTICIAN')")
     */
    public function create(Request $request, ProductsRepository $productsRepository, StructuresRepository $structuresRepository, BrandsRepository $brandsRepository, CategoriesRepository $categoriesRepository, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        // Retrieve the contents of the request
        $content = $request->getContent();
        //get user logged
        $loggedInUser = $this->getUser();
        //get the structure of the logged-in user
        $userStructure = $loggedInUser->getStructures();

        try {
            // Deserialize JSON data to create the product
            $product = $serializer->deserialize($content, Products::class, "json");

            // Find the brands and categories by its ID in the JSON
            $data = json_decode($content, true);

            // Check if 'brand' and categories is an array and if 'id' is present
            if (!isset($data['brands']['id']) & ($data['categories']['id'])) {
                return $this->json(["message" => "BRANDS OR CATEGORY ID not provided"], Response::HTTP_BAD_REQUEST);
            }
            $brandId = $data['brands']['id'];
            $categorieId = $data['categories']['id'];
            // Find the brand and category by its ID
            $brand = $brandsRepository->find($brandId);
            $category = $categoriesRepository->find($categorieId);
            if (!$brand & !$category) {
                return $this->json(["message" => "brand not found"], Response::HTTP_NOT_FOUND);
            }
            // Associate the product with the brand and category
            $product->setStructures($userStructure);
            $product->setBrands($brand);
            $product->setCategories($category);

            // Check the assertions of the Product entity
            $errors = $validator->validate($product);

            if (count($errors) > 0) {
                $dataErrors = [];
                foreach ($errors as $error) {
                    $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
                }
                return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            // Add the product to the database
            $productsRepository->add($product, true);

            return $this->json(["message" => "Creation successful"], Response::HTTP_CREATED, [], ["groups" => "products"]);
        } catch (NotEncodableValueException $err) {
            return $this->json(["message" => "Invalid JSON"], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Update an product
     *
     * @Route("/{id}", name="_update", methods={"PATCH"})
     * 
     * @Security("is_granted('ROLE_LOGISTICIAN')")
     */
    public function update(Products $products, Request $request, ProductsRepository $productsRepository, BrandsRepository $brandsRepository, CategoriesRepository $categoriesRepository, StructuresRepository $structuresRepository, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        // Retrieve the contents of the request
        $content = $request->getContent();
        //get user logged
        $loggedInUser = $this->getUser();
        //get the structure of the logged-in user
        $userStructure = $loggedInUser->getStructures();

        try {
            // Deserialize JSON data
            $product = $serializer->deserialize($content, Products::class, "json", ['object_to_populate' => $products]);

            $data = json_decode($content, true);

            // Check if 'organizations' is an array and if 'id' is present
            if (!isset($data['brands']['id']) & ($data['categories']['id'])) {
                return $this->json(["message" => "BRANDS OR CATEGORY ID not provided"], Response::HTTP_BAD_REQUEST);
            }

            $brandId = $data['brands']['id'];
            $categorieId = $data['categories']['id'];
            $quantityProduct = $data['quantity'];


            // Find the organization by its ID
            $brand = $brandsRepository->find($brandId);
            $category = $categoriesRepository->find($categorieId);




            if (!$brand & !$category) {
                return $this->json(["message" => "brand or category not found"], Response::HTTP_NOT_FOUND);
            }

            // Associate the organization with the structure
            $product->setQuantity($quantityProduct);
            $product->setBrands($brand);
            $product->setCategories($category);
            $product->setStructures($userStructure);

            // Check the assertions of the Product entity
            $errors = $validator->validate($product);

            if (count($errors) > 0) {
                $dataErrors = [];
                foreach ($errors as $error) {
                    $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
                }
                return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Add the Product to the database
            $productsRepository->add($product, true);

            return $this->json(["message" => "Update successful"], Response::HTTP_CREATED, [], ["groups" => "products"]);
        } catch (NotEncodableValueException $err) {
            return $this->json(["message" => "Invalid JSON"], Response::HTTP_BAD_REQUEST);
        }
    }
    /** 
     * Delete an product
     *
     * @Route("/{id}", name="_delete", methods={"DELETE"})
     * 
     * @Security("is_granted('ROLE_LOGISTICIAN')")
     */
    public function delete(int $id, ProductsRepository $productsRepository): JsonResponse
    {

        // Trying to find an product with its id
        try {
            $product = $productsRepository->find($id);
            // If you don't match create an exception
            if (!$product) {
                throw new \Exception("Product not found");
            }
            // If found, delete it from the database (true for flush)
            $productsRepository->remove($product, true);
            // Response JSON for success
            return $this->json(["message" => "Delete successful"], Response::HTTP_NO_CONTENT, [], ["groups" => "products"]);
            // Catch the error and displays it as a JSON response
        } catch (\Exception $error) {
            return $this->json(["error" => $error->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update quantity
     *
     * @Route("/{id}/quantity", name="_updateQuantity", methods={"patch"})
     * 
     * @Security("is_granted('ROLE_LOGISTICIAN')")
     */
    public function updateQuantity(Products $product, Request $request, ProductsRepository $productsRepository, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        // Retrieve the contents of the request
        $content = $request->getContent();

        try {
            // Deserialize JSON data
            $data = json_decode($content, true);

            // Ensure that only the allowed fields are present in the data
            $allowedFields = ['quantity', 'expirationDate'];
            $filteredData = array_intersect_key($data, array_flip($allowedFields));

            // Deserialize the filtered data to update the quantity or expirationDate
            $updatedProduct = $serializer->deserialize(json_encode($filteredData), Products::class, 'json', ['object_to_populate' => $product]);

            // Add update date
            $product->setUpdatedAt(new DateTimeImmutable());

            // Validate the updated data
            $errors = $validator->validate($updatedProduct);


            if (count($errors) > 0) {
                $dataErrors = [];
                foreach ($errors as $error) {
                    $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
                }
                return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Add the quantity to the database
            $productsRepository->add($product, true);

            return $this->json(["message" => "Updated successful"], Response::HTTP_CREATED, [], ["groups" => "products"]);
        } catch (NotEncodableValueException $err) {
            return $this->json(["message" => "Invalid JSON"], Response::HTTP_BAD_REQUEST);
        }
    }
}
