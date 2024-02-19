<?php

namespace App\Controller\Api;

use App\Entity\User;
use DateTimeImmutable;
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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



/**
 * @Route("/api/users", name="api_user")
 */
class UserController extends AbstractController
{
    /**
     * Shows all users
     * 
     * @Route("", name="_list", methods={"GET"}))
     * 
     * @Security("is_granted('ROLE_SUPERADMIN')")
     */
    public function list(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findAll();
        return $this->json($users, Response::HTTP_OK, [], ["groups" => "user"]);
    }

    /**
     * Show an user
     * 
     * @Route("/{id}", name="_show", methods={"GET"})
     * 
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function show(User $user): JsonResponse
    {
        return $this->json($user, Response::HTTP_OK, [], ["groups" => "user"]);
    }

    /**
     * Create an user
     * 
     * @Route("", name="_create", methods={"POST"})
     * 
     * @Security("is_granted('ROLE_MANAGER')")
     */

    public function create(Request $request, OrganizationsRepository $organizationsRepository, StructuresRepository $structuresRepository, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        // here I retrieve the contents of the request
        $content = $request->getContent();
        // Find the user by its ID in the JSON
        $userData = json_decode($content, true);
        //get user logged
        $loggedInUser = $this->getUser();
        //get the organization of the logged-in user
        $userOrganization = $loggedInUser->getOrganizations();
        //get the structure of the logged-in user
        $userStructure = $loggedInUser->getStructures();

        try {
            $user = $serializer->deserialize($content, User::class, "json");

            //if the user is a superadmin then he can create an admin and assign it an organization and structure

            if ($this->isGranted('ROLE_SUPERADMIN')) {

                //found the organization in the query
                $organizationId = $userData['organizations']['id'];
                //found the structure in the query
                $structureId = $userData['structures']['id'];
                // Find the structure by its ID
                $structure = $structuresRepository->find($structureId);
                // Find the organization by its ID
                $organization = $organizationsRepository->find($organizationId);
                //send organization and structure
                $user->setOrganizations($organization);
                $user->setStructures($structure);
            }

            //if the user is admin, then his structure and organization are retrieved from the logged-in user 

            if ($this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_SUPERADMIN')) {

                if (in_array('ROLE_SUPERADMIN', $userData['roles'])) {
                    return $this->json(["message" => "accès refusé , vous n'avez pas les droits"], Response::HTTP_FORBIDDEN);
                } else {

                    //found the structure in the query
                    $structureId = $userData['structures']['id'];
                    // Find the structure by its ID
                    $structure = $structuresRepository->find($structureId);
                    // Find the organization by its ID
                    $user->setOrganizations($userOrganization);
                    $user->setStructures($structure);
                }
            }

            //if the user is manager, then his structure and organization are retrieved from the logged-in user 

            if ($this->isGranted('ROLE_MANAGER') && !$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_SUPERADMIN')) {


                if (in_array('ROLE_ADMIN', $userData['roles']) || in_array('ROLE_SUPERADMIN', $userData['roles'])) {
                    return $this->json(["message" => "accès refusé , vous n'avez pas les droits"], Response::HTTP_FORBIDDEN);
                } else {
                    //send organization and structure from the logged-in user
                    $user->setStructures($userStructure);
                    $user->setOrganizations($userOrganization);
                }
            }

            //get password in the request
            $plaintextPassword = $userData['password'];
            //  verifies the entity's asserts
            $errors = $validator->validate($user);
            if (count($errors) > 0) {
                $dataErrors = [];
                foreach ($errors as $error) {
                    $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
                }
                return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            //hash password 
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );
            //send hasher password 
            $user->setPassword($hashedPassword);
            //add to database
            $userRepository->add($user, true);
            return $this->json(["message" => "creation successfull"], Response::HTTP_CREATED, [], ["groups" => "user"]);
        } catch (NotEncodableValueException $err) {
            return $this->json(["message" => "JSON invalide"], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Update an user
     *
     * @Route("/{id}", name="_update", methods={"PATCH"})
     * 
     * @Security("is_granted('ROLE_MANAGER')")
     */

    public function update($id, User $user, Request $request, UserRepository $userRepository, OrganizationsRepository $organizationsRepository, StructuresRepository $structuresRepository, UserPasswordHasherInterface $passwordHasher, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        // Here I retrieve the contents of the request
        $content = $request->getContent();

        // Find the user by its ID in the JSON
        $userData = json_decode($content, true);
        //get user logged
        $loggedInUser = $this->getUser();
        //get the organization of the logged-in user
        $userOrganization = $loggedInUser->getOrganizations();
        //get the structure of the logged-in user
        $userStructure = $loggedInUser->getStructures();
        //found user to modify
        $userId = $userRepository->find($id);
        //found its role
        $currentUserRole = $userId->getRoles();


        try {
            $user = $serializer->deserialize($content, User::class, "json", ['object_to_populate' => $user]);

            //if the user is a superadmin then he can create an admin and assign it an organization and structure

            if ($this->isGranted('ROLE_SUPERADMIN')) {

                //found the organization in the query
                $organizationId = $userData['organizations']['id'];
                //found the structure in the query
                $structureId = $userData['structures']['id'];
                // Find the structure by its ID
                $structure = $structuresRepository->find($structureId);
                // Find the organization by its ID
                $organization = $organizationsRepository->find($organizationId);
                //send organization and structure
                $user->setOrganizations($organization);
                $user->setStructures($structure);
            }

            //if the user is admin, then his structure and organization are retrieved from the logged-in user and if the role of the user to be modified is admin or superadmin, he can't modify it 

            if ($this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_SUPERADMIN')) {
                if (in_array('ROLE_SUPERADMIN', $currentUserRole)) {
                    return $this->json(["message" => "La modification de l'utilisateur 'ROLE_SUPERADMIN' n'est pas autorisée."], Response::HTTP_FORBIDDEN);
                }
            }

            //if the user is manager, then his structure and organization are retrieved from the logged-in user and if the role of the user to be modified is admin or superadmin or manager , he can't modify it 

            if ($this->isGranted('ROLE_MANAGER') && !$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_SUPERADMIN')) {
                if (in_array('ROLE_SUPERADMIN', $currentUserRole) || (in_array('ROLE_ADMIN', $currentUserRole) || (in_array('ROLE_MANAGER', $currentUserRole)))) {
                    return $this->json(["message" => "Accès à la modification du role refusé"], Response::HTTP_FORBIDDEN);
                } else {
                    $user->setStructures($userStructure);
                    $user->setOrganizations($userOrganization);
                }
            }

            //get password in the request
            $plaintextPassword = $userData['password'];
            //  verifies the entity's asserts
            $errors = $validator->validate($user);
            if (count($errors) > 0) {
                $dataErrors = [];
                foreach ($errors as $error) {
                    $dataErrors[$error->getPropertyPath()][] = $error->getMessage();
                }
                return $this->json($dataErrors, Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            //hash password 
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $plaintextPassword
            );
            //send hasher password 
            $user->setPassword($hashedPassword);
            //add to database
            $userRepository->add($user, true);
            return $this->json(["message" => "Update successfull"], Response::HTTP_OK, [], ["groups" => "user"]);
        } catch (NotEncodableValueException $err) {
            return $this->json(["message" => "JSON invalide"], Response::HTTP_BAD_REQUEST);
        }
    }

    /** 
     * Delete an user
     *
     * @Route("/{id}", name="_delete", methods={"DELETE"})
     * 
     * @Security("is_granted('ROLE_MANAGER')")
     */

    public function delete(int $id, UserRepository $userRepository): JsonResponse
    {

        // Trying to find an user with its id
        try {
            $user = $userRepository->find($id);
            // If you don't match create an exception
            $currentUser = $this->getUser();

            // if user exists
            if (!$user) {
                throw new \Exception("User not found");
            }

            //if the user is superadmin he cannot suppress itself
            if ($this->isGranted('ROLE_SUPERADMIN')) {
                if ($currentUser->getId() === $user->getId()) {
                    return $this->json(["message" => "Vous ne pouvez pas vous supprimer vous-même"], Response::HTTP_FORBIDDEN);
                }
            }

            //if the user is admin, he can't delete a superadmin and cannot suppress itself
            if ($this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_SUPERADMIN')) {
                if ($currentUser->getId() === $user->getId()) {
                    return $this->json(["message" => "Vous ne pouvez pas vous supprimer vous-même"], Response::HTTP_FORBIDDEN);
                }
                if (in_array('ROLE_SUPERADMIN', $user->getRoles())) {
                    return $this->json(["message" => "Accès à la suppression refusé"], Response::HTTP_FORBIDDEN);
                }
            }

            //if the user is manager, he can't delete a admin and superadmin and cannot suppress itself
            else {
                if ($this->isGranted('ROLE_MANAGER') && !$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_SUPERADMIN')) {
                    if ($currentUser->getId() === $user->getId()) {
                        return $this->json(["message" => "Vous ne pouvez pas vous supprimer vous-même"], Response::HTTP_FORBIDDEN);
                    }
                    if (in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_SUPERADMIN', $user->getRoles())) {
                        return $this->json(["message" => "Accès à la suppression refusé"], Response::HTTP_FORBIDDEN);
                    }
                }
            }

            // If found, delete it from the database (true for flush)
            $userRepository->remove($user, true);

            // Response JSON for success
            return $this->json(["message" => "Delete successful"], Response::HTTP_NO_CONTENT, [], ["groups" => "user"]);
            // Catch the error and displays it as a JSON response
        } catch (NotEncodableValueException $err) {
            return $this->json(["message" => "JSON invalide"], Response::HTTP_BAD_REQUEST);
        }
    }

    /** 
     * Change the status of a user
     *
     * @Route("/{id}/status", name="_status_change", methods={"PATCH"})
     * 
     * @Security("is_granted('ROLE_SUPERADMIN')")
     */

    public function statusUser(int $id, Request $request, UserRepository $userRepository): JsonResponse
    {

        $content = $request->getContent();
        $userId = $userRepository->find($id);
        $Data = json_decode($content, true);
        $statusData = $Data['status'];
        try {

            if (!$statusData) {
                $userId->setStatus(false);
                $userRepository->add($userId, true);
                return $this->json(["message" => "Disable successful"], Response::HTTP_OK, [], ["groups" => "user"]);
            }
            if ($statusData == 1) {
                $userId->setStatus(true);
                $userRepository->add($userId, true);
                return $this->json(["message" => "Enable successful"], Response::HTTP_OK, [], ["groups" => "user"]);
            }

            throw new \Exception("This action is not possible");
        } catch (NotEncodableValueException $err) {
            return $this->json(["message" => "JSON invalide"], Response::HTTP_BAD_REQUEST);
        }
    }
}
