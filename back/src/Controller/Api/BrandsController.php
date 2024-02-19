<?php

namespace App\Controller\Api;

use App\Entity\Brands;
use DateTimeImmutable;
use App\Repository\BrandsRepository;
use Doctrine\ORM\EntityManagerInterface;
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
 * @Route("/api/brands", name="api_brands")
 */

class BrandsController extends AbstractController
{
    /**
     * Show all brands
     * 
     * @Route("", name="_list", methods={"GET"})
     * 
     * @Security("is_granted('ROLE_LOGISTICIAN')")
     */
    public function list(BrandsRepository $brandsRepository): JsonResponse
    {
        $brands = $brandsRepository->findAll();
        return $this->json($brands, Response::HTTP_OK, [], ["groups" => "brands"]);
    }

    /**
     * Show a brand
     *
     * @Route("/{id}", name="_show", methods={"GET"})
     * 
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function show(Brands $brands): JsonResponse
    {
        return $this->json($brands, Response::HTTP_OK, [], ["groups" => "brands"]);
    }

    /**
     * Create an brand
     *
     * @Route("", name="_create", methods={"POST"})
     * 
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function create(Request $request, BrandsRepository $brandsRepository, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        // here I retrieve the contents of the request
        $content = $request->getContent();

        try {
            $brands = $serializer->deserialize($content, Brands::class, "json");
        } catch (NotEncodableValueException $err) {
            return $this->json(["message" => "JSON invalide"], Response::HTTP_BAD_REQUEST);
        }

        $errors = $validator->validate($brands);
        if (count($errors) > 0) {
            $dataErrors = [];
            foreach ($errors as $error) {
                $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
            }
            return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Add to database
        $brandsRepository->add($brands, true);
        return $this->json(["message" => "creation successfull"], Response::HTTP_CREATED, [], ["groups" => "brands"]);
    }

    /**
     * Update a brand
     *
     * @Route("/{id}", name="_update", methods={"PATCH"})
     * 
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function update(Brands $brands, Request $request, BrandsRepository $brandsRepository, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        try {
            $updateBrands = $serializer->deserialize($request->getContent(), Brands::class, 'json', ['object_to_populate' => $brands]);
            // Add update date
            $brands->setUpdatedAt(new DateTimeImmutable());
        } catch (NotEncodableValueException $err) {
            return $this->json(["message" => "JSON invalide"], Response::HTTP_BAD_REQUEST);
        }

        //  verifies the entity's asserts
        $errors = $validator->validate($brands);
        if (count($errors) > 0) {
            $dataErrors = [];
            foreach ($errors as $error) {
                $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
            }
            return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Add to database
        $brandsRepository->add($updateBrands, true);
        return $this->json(["message" => "Update successfull"], Response::HTTP_OK, [], ["groups" => "brands"]);
    }

    /**
     * Delete a brand
     *
     * @Route("/{id}", name="_delete", methods={"DELETE"})
     * 
     * @Security("is_granted('ROLE_MANAGER')")
     */

    public function delete(int $id, BrandsRepository $brandsRepository): JsonResponse
    {

        // Trying to find a brand with its id
        try {
            $brand = $brandsRepository->find($id);

            // If you don't match create an exception
            if (!$brand) {
                throw new \Exception("Organization not found");
            }

            // If found, delete it from the database (true for flush)
            $brandsRepository->remove($brand, true);

            // Response JSON for success
            return $this->json(["message" => "Delete successful"], Response::HTTP_OK, [], ["groups" => "brands"]);

            // Catch the error and displays it as a JSON response
        } catch (\Exception $error) {
            return $this->json(["error" => $error->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }
}
