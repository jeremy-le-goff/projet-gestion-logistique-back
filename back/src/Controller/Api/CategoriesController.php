<?php

namespace App\Controller\Api;

use DateTimeImmutable;
use App\Entity\Categories;
use App\Repository\CategoriesRepository;
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
 * @Route("/api/categories", name="api_categories")
 */

class CategoriesController extends AbstractController
{
    /**
     * Show all categories
     * 
     * @Route("", name="_list", methods={"GET"}))
     * 
     * @Security("is_granted('ROLE_LOGISTICIAN')")
     */
    public function list(CategoriesRepository $categoriesRepository): JsonResponse
    {
        $categories = $categoriesRepository->findAll();
        return $this->json($categories, Response::HTTP_OK, [], ["groups" => "categories"]);
    }

    /**
     * Show an category
     * 
     * @Route("/{id}", name="_show", methods={"GET"})
     * 
     * @Security("is_granted('ROLE_LOGISTICIAN')")
     */
    public function show(Categories $categories): JsonResponse
    {
        return $this->json($categories, Response::HTTP_OK, [], ["groups" => "categories"]);
    }

    /**
     * Create an category
     * 
     * @Route("", name="_create", methods={"POST"})
     * 
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function create(Request $request, CategoriesRepository $categoriesRepository, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        // here I retrieve the contents of the request
        $content = $request->getContent();
        try {
            $category = $serializer->deserialize($content, Categories::class, "json");
        } catch (NotEncodableValueException $err) {
            return $this->json(["message" => "JSON invalide"], Response::HTTP_BAD_REQUEST);
        }
        //  verifies the entity's asserts
        $errors = $validator->validate($category);
        if (count($errors) > 0) {
            $dataErrors = [];
            foreach ($errors as $error) {
                $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
            }
            return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        //add to database
        $categoriesRepository->add($category, true);
        return $this->json(["message" => "creation successfull"], Response::HTTP_CREATED, [], ["groups" => "categories"]);
    }

    /**
     * Update an category
     * 
     * @Route("/{id}", name="_update", methods={"PATCH"})
     * 
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function update(Categories $categories, Request $request, CategoriesRepository $categoriesRepository, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        // here I retrieve the contents of the request
        $content = $request->getContent();
        try {
            $category = $serializer->deserialize($content, Categories::class, "json", ['object_to_populate' => $categories]);

            // Add update date
            $category->setUpdatedAt(new DateTimeImmutable());
        } catch (NotEncodableValueException $err) {
            return $this->json(["message" => "JSON invalide"], Response::HTTP_BAD_REQUEST);
        }
        //  verifies the entity's asserts
        $errors = $validator->validate($category);
        if (count($errors) > 0) {
            $dataErrors = [];
            foreach ($errors as $error) {
                $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
            }
            return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        //add to database
        $categoriesRepository->add($category, true);
        return $this->json(["message" => "Update successfull"], Response::HTTP_OK, [], ["groups" => "categories"]);
    }

    /** 
     * Delete an category
     * 
     * @Route("/{id}", name="_delete", methods={"DELETE"})
     * 
     * @Security("is_granted('ROLE_MANAGER')")
     */

    public function delete(int $id, CategoriesRepository $categoriesRepository): JsonResponse
    {
        // Trying to find an category with its id
        try {
            $category = $categoriesRepository->find($id);
            // If you don't match create an exception
            if (!$category) {
                throw new \Exception("Category not found");
            }
            // If found, delete it from the database (true for flush)
            $categoriesRepository->remove($category, true);
            // Response JSON for success
            return $this->json(["message" => "Delete successful"], Response::HTTP_NO_CONTENT, [], ["groups" => "categories"]);
            // Catch the error and displays it as a JSON response
        } catch (\Exception $error) {
            return $this->json(["error" => $error->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}
