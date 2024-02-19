<?php

namespace App\Controller\Api;

use DateTimeImmutable;
use App\Entity\Structures;
use App\Entity\Organizations;
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
 * @Route("/api/organizations", name="api_organizations")
 */
class OrganizationsController extends AbstractController
{

    /**
     * Shows all organizations
     * 
     * @Route("", name="_list", methods={"GET"})
     * 
     * @Security("is_granted('ROLE_SUPERADMIN')")
     */

    public function list(OrganizationsRepository $organizationsRepository): JsonResponse
    {
        $organizations = $organizationsRepository->findAll();

        return $this->json($organizations, Response::HTTP_OK, [], ["groups" => "organizations"]);
    }

    /**
     * Show an organization
     * 
     * @Route("/{id}", name="_show", methods={"GET"})
     * 
     * @Security("is_granted('ROLE_SUPERADMIN')")
     */
    public function show(Organizations $organizations): JsonResponse
    {
        return $this->json($organizations, Response::HTTP_OK, [], ["groups" => "organizations"]);
    }

    /**
     * Create an organization 
     * 
     * @Route("", name="_create", methods={"POST"})
     * 
     * @Security("is_granted('ROLE_SUPERADMIN')")
     */

    public function create(Request $request, OrganizationsRepository $organizationsRepository, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        // Here I retrieve the contents of the request
        $content = $request->getContent();

        try {
            $organizations = $serializer->deserialize($content, Organizations::class, "json");
        } catch (NotEncodableValueException $err) {
            return $this->json(["message" => "JSON invalide"], Response::HTTP_BAD_REQUEST);
        }

        //  Verifies the entity's asserts
        $errors = $validator->validate($organizations);
        if (count($errors) > 0) {
            $dataErrors = [];
            foreach ($errors as $error) {
                $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
            }
            return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        // Add to database
        $organizationsRepository->add($organizations, true);
        return $this->json(["message" => "creation successfull"], Response::HTTP_CREATED, [], ["groups" => "organizations"]);
    }

    /**
     * update an organization 
     * 
     * @Route("/{id}", name="_update", methods={"PATCH"})
     * 
     * @Security("is_granted('ROLE_SUPERADMIN')")
     */

    public function update(Organizations $organizations, Request $request, OrganizationsRepository $organizationsRepository, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {

        try {
            $updateOrganizations = $serializer->deserialize($request->getContent(), Organizations::class, 'json', ['object_to_populate' => $organizations]);

            // Add update date
            $updateOrganizations->setUpdatedAt(new DateTimeImmutable());
        } catch (NotEncodableValueException $err) {
            return $this->json(["message" => "JSON invalide"], Response::HTTP_BAD_REQUEST);
        }

        // Verifies the entity's asserts
        $errors = $validator->validate($organizations);
        if (count($errors) > 0) {
            $dataErrors = [];
            foreach ($errors as $error) {
                $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
            }
            return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        //add to database
        $organizationsRepository->add($updateOrganizations, true);
        return $this->json(["message" => "Update successfull"], Response::HTTP_OK, [], ["groups" => "organizations"]);
    }


    /** 
     * Delete an organization
     * 
     * @Route("/{id}", name="_delete", methods={"DELETE"})
     * 
     * @Security("is_granted('ROLE_SUPERADMIN')")
     */

    public function delete(int $id, OrganizationsRepository $organizationsRepository, EntityManagerInterface $entityManager): JsonResponse
    {

        // Trying to find an organization with its id
        try {
            $organization = $organizationsRepository->find($id);

            // If you don't match create an exception
            if (!$organization) {
                throw new \Exception("Organization not found");
            }

            // Remove associated structures
            $structures = $organization->getStructures();

            foreach ($structures as $structure) {

                // Check if the product exists
                $productExists = $structure->getProducts()->isEmpty();
                if (!$productExists) {
                    throw new \Exception("Products in your structures! Empty your structures' stocks to remove organization.");
                };

                // Deleting the structure
                $entityManager->remove($structure);
            }

            // If found, delete it from the database (true for flush)
            $organizationsRepository->remove($organization, true);

            // Response JSON for success
            return $this->json(["message" => "Delete successful"], Response::HTTP_NO_CONTENT, [], ["groups" => "organizations"]);

            // Catch the error and displays it as a JSON response
        } catch (\Exception $error) {
            return $this->json(["error" => $error->getMessage()], Response::HTTP_NOT_FOUND);
        }
    }


    /** 
     * Change the status of a Organizations
     * 
     * @Route("/{id}/status", name="_status_change", methods={"PATCH"})
     * 
     * @Security("is_granted('ROLE_SUPERADMIN')")
     */

    public function statusOrganization(int $id, Request $request, OrganizationsRepository $organizationsRepository): JsonResponse
    {

        $content = $request->getContent();
        $organizationId = $organizationsRepository->find($id);
        $Data = json_decode($content, true);
        $statusData = $Data['status'];
        try {

            if ($statusData == 0) {
                $organizationId->setStatus(0);
                $organizationsRepository->add($organizationId, true);
                return $this->json(["message" => "Disable successful"], Response::HTTP_OK, [], ["groups" => "organizations"]);
            }
            if ($statusData == 1) {
                $organizationId->setStatus(1);
                $organizationsRepository->add($organizationId, true);
                return $this->json(["message" => "Enable successful"], Response::HTTP_OK, [], ["groups" => "organizations"]);
            }
            if ($statusData == 2) {
                $organizationId->setStatus(2);
                $organizationsRepository->add($organizationId, true);
                return $this->json(["message" => "In process"], Response::HTTP_OK, [], ["groups" => "organizations"]);
            }
            throw new \Exception("This action is not possible");
        } catch (NotEncodableValueException $err) {
            return $this->json(["message" => "JSON invalide"], Response::HTTP_BAD_REQUEST);
        }
    }

    /** 
     * take all structures by organization
     * 
     * @Route("/{id}/structures", name="_status_change", methods={"GET"})
     * 
     * @Security("is_granted('ROLE_MANAGER')")
     */

    public function structuresByOrganization(Organizations $organizations)
    {
        $organizations->getStructures();
        return $this->json($organizations, Response::HTTP_OK, [], ["groups" => "structuresByOrganization"]);
    }

    /** 
     * show all user by organization
     * 
     * @Route("/{id}/users", name="_usersList", methods={"GET"})
     * 
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function UserViewByOrganization(Organizations $organizations)
    {
        $organizations->getUsers();
        return $this->json($organizations, Response::HTTP_OK, [], ["groups" => "structuresUser"]);
    }

    /** 
     * show all products by Organization
     * 
     * @Route("/{id}/products", name="_productsList", methods={"GET"})
     * 
     * @Security("is_granted('ROLE_ADMIN')")
     */

    public function ProductByOrganization(Structures $structures)
    {
        $structures->getProducts();
        return $this->json($structures, Response::HTTP_OK, [], ["groups" => "products"]);
    }
}
