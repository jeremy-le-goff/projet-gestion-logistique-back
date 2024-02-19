<?php

namespace App\Controller\Api;


use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Products;
use App\Entity\Structures;
use App\Entity\Organizations;
use App\Repository\UserRepository;
use App\Repository\StructuresRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\OrganizationsRepository;
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
 * @Route("/api/structures", name="api_structures")
 */

class StructuresController extends AbstractController
{
    /**
     * Shows all structures
     * 
     * @Route("", name="_list", methods={"GET"}))
     * 
     * @Security("is_granted('ROLE_SUPERADMIN')")
     */

    public function list(StructuresRepository $structuresRepository): JsonResponse
    {
        $structures = $structuresRepository->findAll();
        return $this->json($structures, Response::HTTP_OK, [], ["groups" => "structures"]);
    }

    /**
     * Show an structure
     * 
     * @Route("/{id}", name="_show", methods={"GET"})

     * @Security("is_granted('ROLE_ADMIN')")
     */

    public function show(Structures $structures): JsonResponse
    {
        return $this->json($structures, Response::HTTP_OK, [], ["groups" => "structures"]);
    }

    /**
     * Create an structure
     * 
     * @Route("", name="_create", methods={"POST"})
     *
     * @Security("is_granted('ROLE_ADMIN')")
     */

    public function create(Request $request, OrganizationsRepository $organizationsRepository, StructuresRepository $structureRepository, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        // Retrieve the contents of the request
        $content = $request->getContent();
        //get user logged
        $loggedInUser = $this->getUser();
        //get the organization of the logged-in user
        $userOrganization = $loggedInUser->getOrganizations();

        try {
            // Deserialize JSON data to create the structure
            $structure = $serializer->deserialize($content, Structures::class, "json");
            // Find the organization by its ID in the JSON
            $organizationData = json_decode($content, true);
            //if the user is a superadmin then he can create an admin and assign it an organization 
            if ($this->isGranted('ROLE_SUPERADMIN')) {
                //found the organization in the query
                $organizationId = $organizationData['organizations']['id'];
                // Find the organization by its ID
                $organization = $organizationsRepository->find($organizationId);
                if (!$organization) {
                    return $this->json(["message" => "Organization not found"], Response::HTTP_NOT_FOUND);
                }
                //send organization 
                $structure->setOrganizations($organization);
            }

            //if the user is admin, then his organization are retrieved from the logged-in user 

            if ($this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_SUPERADMIN')) {
                $structure->setOrganizations($userOrganization);
            }
            // Check the assertions of the Structure entity
            $errors = $validator->validate($structure);
            if (count($errors) > 0) {
                $dataErrors = [];
                foreach ($errors as $error) {
                    $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
                }
                return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            // Add the structure to the database
            $structureRepository->add($structure, true);

            return $this->json(["message" => "Creation successful"], Response::HTTP_CREATED, [], ["groups" => "structures"]);
        } catch (NotEncodableValueException $err) {
            return $this->json(["message" => "Invalid JSON"], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Update an structure
     * 
     * @Route("/{id}", name="_update", methods={"PATCH"})
     *
     * @Security("is_granted('ROLE_ADMIN')")
     */

    public function update(Request $request, Structures $structures, OrganizationsRepository $organizationsRepository, StructuresRepository $structureRepository, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        // Retrieve the contents of the request
        $content = $request->getContent();
        //get user logged
        $loggedInUser = $this->getUser();
        //get the organization of the logged-in user
        $userOrganization = $loggedInUser->getOrganizations();

        try {
            // Deserialize JSON data to create the structure
            $structure = $serializer->deserialize($content, Structures::class, "json", ['object_to_populate' => $structures]);

            // Find the organization by its ID in the JSON
            $organizationData = json_decode($content, true);

            //if the user is a superadmin then he can create an admin and assign it an organization 
            if ($this->isGranted('ROLE_SUPERADMIN')) {
                //found the organization in the query
                $organizationId = $organizationData['organizations']['id'];
                // Find the organization by its ID
                $organization = $organizationsRepository->find($organizationId);
                if (!$organization) {
                    return $this->json(["message" => "Organization not found"], Response::HTTP_NOT_FOUND);
                }
                //send organization 
                $structure->setOrganizations($organization);
            }

            //if the user is admin, then his organization are retrieved from the logged-in user 

            if ($this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_SUPERADMIN')) {

                $structure->setOrganizations($userOrganization);
            }
            // Check the assertions of the Structure entity
            $errors = $validator->validate($structure);

            if (count($errors) > 0) {
                $dataErrors = [];
                foreach ($errors as $error) {
                    $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
                }
                return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            // Add update date
            $structure->setUpdatedAt(new DateTimeImmutable());

            // Add the structure to the database
            $structureRepository->add($structure, true);

            return $this->json(["message" => "Update successful"], Response::HTTP_OK, [], ["groups" => "structures"]);
        } catch (NotEncodableValueException $err) {
            return $this->json(["message" => "Invalid JSON"], Response::HTTP_BAD_REQUEST);
        }
    }

    /** 
     * Delete an structure
     * 
     * @Route("/{id}", name="_delete", methods={"DELETE"})
     *
     * @Security("is_granted('ROLE_ADMIN')")
     */

    public function delete(int $id, StructuresRepository $structuresRepository, EntityManagerInterface $entityManager): JsonResponse
    {

        // Trying to find an structure with its id
        try {
            $structure = $structuresRepository->find($id);

            // If you don't match create an exception
            if (!$structure) {
                throw new \Exception("Structure not found");
            }

            // Remove associated products
            $products = $structure->getProducts();

            foreach ($products as $product) {
                // Deleting the structure
                $entityManager->remove($product);
            }

            // If found, delete it from the database (true for flush)
            $structuresRepository->remove($structure, true);

            // Response JSON for success
            return $this->json(["message" => "Delete successful"], Response::HTTP_NO_CONTENT, [], ["groups" => "structures"]);
            // Catch the error and displays it as a JSON response
        } catch (\Exception $error) {
            return $this->json(["error" => $error->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }

    /** 
     * Change the status of a structure
     *
     * @Route("/{id}/status", name="_status_change", methods={"PATCH"})
     * 
     * @Security("is_granted('ROLE_ADMIN')")
     */

    public function statusStructure(int $id, Request $request, StructuresRepository $structuresRepository): JsonResponse
    {

        $content = $request->getContent();
        $structureId = $structuresRepository->find($id);
        $Data = json_decode($content, true);
        $statusData = $Data['status'];
        try {

            if (!$statusData) {
                $structureId->setStatus(false);
                $structuresRepository->add($structureId, true);
                return $this->json(["message" => "Disable successful"], Response::HTTP_OK, [], ["groups" => "structures"]);
            }
            if ($statusData == 1) {
                $structureId->setStatus(true);
                $structuresRepository->add($structureId, true);
                return $this->json(["message" => "Enable successful"], Response::HTTP_OK, [], ["groups" => "structures"]);
            }

            throw new \Exception("This action is not possible");
        } catch (NotEncodableValueException $err) {
            return $this->json(["message" => "JSON invalide"], Response::HTTP_BAD_REQUEST);
        }
    }

    /** 
     * show all user by structures
     * 
     * @Route("/{id}/users", name="_usersList", methods={"GET"})
     * 
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function UserViewByStructure(Structures $structures)
    {
        $structures->getUsers();
        return $this->json($structures, Response::HTTP_OK, [], ["groups" => "structuresUser"]);
    }

    /** 
     * show all products by structures
     * 
     * @Route("/{id}/products", name="_productsList", methods={"GET"})
     * 
     * @Security("is_granted('ROLE_LOGISTICIAN')")
     */
    public function ProductByStructure(Structures $structures)
    {
        $structures->getProducts();
        return $this->json($structures, Response::HTTP_OK, [], ["groups" => "products"]);
    }
}
