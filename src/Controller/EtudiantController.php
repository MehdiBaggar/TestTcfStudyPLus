<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Repository\EtudiantRepository;
use App\Repository\TestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class EtudiantController extends AbstractController
{
    #[Route('/etudiant', name: 'app_etudiant')]
    public function index(): Response
    {
        return $this->render('etudiant/index.html.twig', [
            'controller_name' => 'EtudiantController',
        ]);
    }
    #[Route('/etudiant/create', name: 'app_etudiant_create')]
    public function create(Request $request,
                           ManagerRegistry $doctrine,
                           EtudiantRepository $etudiantRepository,
                           UserPasswordHasherInterface $passwordHasher,
                           TokenGeneratorInterface $tokenGenerator,
                           ValidatorInterface $validator,


    ): JsonResponse

    {
        try {
            $data = json_decode($request->getContent(), true);
            $nom = $data['nom'] ?? null;
            $prenom = $data['prenom'] ?? null;
            $ville = $data['ville'] ?? null;
            $email = $data['email'] ?? null;
            $telephone = $data['tel'] ?? null;
            $niveauEtude = $data['niveau_bac'] ?? null;
            $existingEtudiant = $etudiantRepository->findOneBy(['email' => $email]);
            if ($existingEtudiant) {
                return new JsonResponse(['error' => 'email deja utilisé .'], Response::HTTP_CONFLICT);
             }
            $etudiant = new Etudiant();
            $etudiant->setNom($nom);
            $etudiant->setPrenom($prenom);
            $etudiant->setVille($ville);
            $etudiant->setEmail($email);
            $etudiant->setTelephone($telephone);
            $etudiant->setNiveauEtude($niveauEtude);

            $etudiant->setRoles(['ROLE_USER']);
            $randomPassword = $this->generateRandomString(12);




            $hashedPassword = $passwordHasher->hashPassword(
                $etudiant,
                $randomPassword
            );
            $etudiant->setPassword($hashedPassword);
            $resetToken = $tokenGenerator->generateToken();
            $etudiant->setResetToken($resetToken);
            $etudiant->setResetTokenExpiresAt(new \DateTimeImmutable('+24 hours'));


            $errors = $validator->validate($etudiant);
            $errors = $validator->validate($etudiant);


            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[$error->getPropertyPath()] = $error->getMessage();
                }
                return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
            }



            $manager = $doctrine->getManager();
            $manager->persist($etudiant);
            $manager->flush();
            return new JsonResponse([
                'message' => 'Etudiant created successfully!',
                'etudiantId' => $etudiant->getId(), // Send the etudiant ID
            ], Response::HTTP_CREATED);
        }
        catch (\Exception $e) {
            error_log($e->getMessage());
            return new JsonResponse(['error' => 'Failed to create Etudiant: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    private function generateRandomString(int $length = 12): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    #[Route('/get/etudiants', name: 'all_Etudiants1', methods: ['GET'])]
    public function getAllForms(EtudiantRepository $etudiantRepository): JsonResponse
    {
        $etudiants = $etudiantRepository->findAll();
        $data = array_map(function ($etudiant) {
            return [
                'id' => $etudiant->getId(),
                'nom' => $etudiant->getNom(),
                'prenom' => $etudiant->getPrenom(),
                'email' => $etudiant->getEmail(),
                'tel' => $etudiant->getTelephone(),
                'niveauBac' => $etudiant->getNiveauEtude(),
                'ville' => $etudiant->getVille(),
                // Add other properties as needed
            ];
        }, $etudiants);

        return new JsonResponse($data);
    }
    #[Route('/etudiant/{id}', name: 'api_etudiant_show', methods: ['GET'])]
    public function show(int $id, EtudiantRepository $etudiantRepository): JsonResponse
    {
        $etudiant = $etudiantRepository->find($id);

        if (!$etudiant) {
            return new JsonResponse(['message' => 'Etudiant not found'], 404);
        }

        $data = [
            'id' => $etudiant->getId(),
            'nom' => $etudiant->getNom(),
            'prenom' => $etudiant->getPrenom(),
            'email' => $etudiant->getEmail(),
            'tel' => $etudiant->getTelephone(),
            'niveauBac' => $etudiant->getNiveauEtude(),
            'ville' => $etudiant->getVille(),
            // Add other properties as needed
        ];

        return new JsonResponse($data);
    }
    #[Route('/delete/etudiant/{id}', name: 'etudiant_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $etudiant = $entityManager->getRepository(Etudiant::class)->find($id);

        if (!$etudiant) {
            return new JsonResponse(['message' => 'Etudiant not found'], Response::HTTP_NOT_FOUND);
        }
        $entityManager->remove($etudiant);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Form deleted successfully'], Response::HTTP_OK);
    }
    #[Route('/edit/etudiant/{id}', name: 'api_etudiant_edit', methods: ['POST'])]
    public function edit(int $id, Request $request, EtudiantRepository $etudiantRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $etudiant = $etudiantRepository->find($id);

        if (!$etudiant) {
            return new JsonResponse(['message' => 'Etudiant not found'], 404);
        }

        // Process the form data
        $tel = $request->request->get('tel');
        $email = $request->request->get('email');
        $nom = $request->request->get('nom');
        $prenom = $request->request->get('prenom');
        $niveauBac = $request->request->get('niveau_bac');
        $ville = $request->request->get('ville');

        // Update the entity
        $etudiant->setTelephone($tel);
        $etudiant->setEmail($email);
        $etudiant->setNom($nom);
        $etudiant->setPrenom($prenom);
        $etudiant->setNiveauEtude($niveauBac);
        $etudiant->setVille($ville);

        // Save the changes
        $entityManager->persist($etudiant);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Etudiant updated successfully'], 200);
    }
    #[Route('/allEtudiants', name: 'app_etudiant_AllEtudiants')]
    public function etudiants(): Response
    {
        return $this->render('etudiant/allEtudiant.html.twig', [
            'controller_name' => 'TcfController',
        ]);
    }
    #[Route('/editEtudiant/{id}', name: 'app_tcf_editEtudiantVue')]
    public function editEtudiant($id): Response
    {
        return $this->render('etudiant/editetudiant.html.twig', [
            'controller_name' => 'TcfController',
        ]);
    }
    #[Route('/etudiant/{id}/tests', name: 'api_etudiant_tests', methods: ['GET'])]
    public function getTestsForEtudiant(int $id, EtudiantRepository $etudiantRepository, TestRepository $testRepository): JsonResponse
    {
        $etudiant = $etudiantRepository->find($id);

        if (!$etudiant) {
            return new JsonResponse(['message' => 'Etudiant not found'], 404);
        }

        $tests = $testRepository->findBy(['user' => $etudiant]); // Find tests for this Etudiant

        $data = array_map(function ($test) {
            return [
                'id' => $test->getId(),
                'data' => $test->getData() ? $test->getData()->format('Y-m-d') : null,
                'scoreTotal' => $test->getScoreTotal(),
                'niveau' => $test->getNiveau(),
                // Add other test properties as needed
            ];
        }, $tests);

        return new JsonResponse($data);
    }
    #[Route('/etudiant/{id}/tests', name: 'api_etudiant_testsVue')]
    public function etudiantAllTests($id): Response
    {
        return $this->render('etudiant/etudiantTests.html.twig', [
            'controller_name' => 'TcfController',
        ]);
    }
    #[Route('/checkExistingTest', name: 'api_check_emailVue',)]
    public function etudiantAllTestsVue(): Response
    {
        return $this->render('etudiant/checkEmail.html.twig', [
            'controller_name' => 'TcfController',
        ]);
    }
    #[Route('/checkEmail', name: 'api_check_email', methods: ['POST'])]
    public function checkEmail(Request $request, EtudiantRepository $etudiantRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);  // Decode JSON payload

        $email = $data['email'] ?? null; // Get email from the decoded data

        if (!$email) {
            return new JsonResponse(['message' => 'Email is required'], 400);
        }

        $etudiant = $etudiantRepository->findOneBy(['email' => $email]);

        if ($etudiant) {
            // Email found, return the Etudiant ID
            return new JsonResponse(['etudiantId' => $etudiant->getId()], 200);
        } else {
            // Email not found
            return new JsonResponse(['message' => 'No test found with that email address.'], 404);
        }
    }

    #[Route('/admin', name: 'adminPage')]
    public function adminPage(): Response
    {
        // Render admin page
        return $this->render('etudiant/admin.html.twig');
    }
    #[Route('/api/user', name: 'api_user')]
    public function getCurrentUser(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['message' => 'No user logged in'], 401); // Or redirect to login
        }

        // Customize the data you want to return
        $userData = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'ville' => $user->getVille(),
            'age' => $user->getAge(),
            'telephone' => $user->getTelephone(),
            'niveauEtude' => $user->getNiveauEtude(),

        ];

        return new JsonResponse($userData);
    }
    // Helper function to decode the JWT


}
