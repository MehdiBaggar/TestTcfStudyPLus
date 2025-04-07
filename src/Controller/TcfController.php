<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Entity\Question;
use App\Entity\ReponseEtudiant;
use App\Entity\Test;
use App\Repository\EtudiantRepository;
use App\Repository\QuestionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use setasign\Fpdi\Fpdi;
use setasign\Fpdf\Fpdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class TcfController extends AbstractController
{
    #[Route('/', name: 'app_formulaire')]
    public function formulaire(): Response
    {
        return $this->render('tcf/formulaire.html.twig', [
            'controller_name' => 'TcfController',
        ]);
    }

    #[Route('/Tcf/{id}', name: 'app_tcf')]
    public function index(int $id): Response
    {
        return $this->render('tcf/index.html.twig', [
            'controller_name' => 'TcfController',
        ]);
    }
    #[Route('/AllQuestions', name: 'app_tcf_AllQuestions')]
    public function questions(): Response
    {
        return $this->render('tcf/AllQuestions.html.twig', [
            'controller_name' => 'TcfController',
        ]);
    }
    #[Route('/test/questions', name: 'app_question', methods: ['GET'])]
    public function getQuestions(ManagerRegistry $doctrine): JsonResponse
    {
        $questionRepository = $doctrine->getRepository(Question::class);


        $typesWithLimits = [
            'Compréhension orale' => 15,
            'Maîtrise des structures de la langue' => 10,
            'Compréhension de la langue écrite' => 15
        ];


        $questions = $questionRepository->findRandomQuestionsByTypeAndLimit($typesWithLimits);

        if (empty($questions)) {
            return new JsonResponse(["error" => "Aucune question trouvée"], 404);
        }


        $questionsData = array_map(function ($question) {
            return [
                'id' => $question->getId(),
                'question' => $question->getQuestion(),
                'type' => $question->getType(),
                'difficulte' => $question->getDifficulteeeee(),
                'reponse_correcte' => $question->getReponseCorrecte(),
                'reponses' => $question->getReponses(),
                'audio' => $question->getAudio(),
                'user_reponse' => $question->getUserReponse()
            ];
        }, $questions);

        return new JsonResponse($questionsData);
    }

    #[Route('/test/submit/{etudiantId}', name: 'api_test_submit', methods: ['POST'])]
    public function submitTest(Request $request, ManagerRegistry $doctrine,int $etudiantId,EtudiantRepository $etudiantRepository,MailerInterface $mailer): JsonResponse
    {
        $manager = $doctrine->getManager();
        $data = json_decode($request->getContent(), true);
        $user = $etudiantRepository->find($etudiantId);
        if (!$user) {
            return new JsonResponse(['error' => 'Étudiant non trouvé'], Response::HTTP_NOT_FOUND);
        }
        $test = new Test();
        $test->setUser($user);
        $test->setData(new \DateTime());


        if (!isset($data['answers']) || !is_array($data['answers'])) {
            return new JsonResponse(['error' => 'Invalid data format'], 400);
        }

        $reponseCorrecte = 0;
        $responsesDetails = [];

        foreach ($data['answers'] as $answer) {
            $question = $manager->getRepository(Question::class)->find($answer['questionId']);

            if (!$question) {
                continue;
            }


            $isCorrect = $question->getReponseCorrecte() === $answer['response'];
            if ($isCorrect) {
                $reponseCorrecte++;
            }


            $responsesDetails[] = [
                'questionId' => $answer['questionId'],
                'isCorrect' => $isCorrect,

            ];


            $reponseEtudiant = new ReponseEtudiant();
            $reponseEtudiant->setTest($test);
            $reponseEtudiant->setQuestion($question);
            $reponseEtudiant->setUserAnswer($answer['response']);
            $reponseEtudiant->setIsCorrect($isCorrect); // Boolean true/false

            $manager->persist($reponseEtudiant);
        }


        $manager->flush();


        $simplifiedResponsesDetails = array_map(function ($responseDetail) {
            return [
                'questionId' => $responseDetail['questionId'],
                'isCorrect' => $responseDetail['isCorrect'],

            ];
        }, $responsesDetails);


        $score = $reponseCorrecte * 17.5;
        $niveau = $this->determineNiveau($score);


        $test->setScoreTotal($score);
        $test->setNiveau($niveau);

        $manager->persist($test);
        $manager->flush();

        $response = new JsonResponse([
            'score' => $score,
            'niveau' => $niveau,
            'responsesDetails' => $simplifiedResponsesDetails,
        ]);
        return $response;



    }
    #[Route('/addQuestion', name: 'addQuestion')]
    public function addQuestion(): Response
    {
        return $this->render('tcf/question.html.twig', [
            'controller_name' => 'TcfController',
        ]);
    }
    #[Route('/editQuestion/{id}', name: 'app_tcf_editquestionVue')]
    public function editQuestion($id): Response
    {
        return $this->render('tcf/question.html.twig', [
            'controller_name' => 'TcfController',
        ]);
    }
    #[Route('/questions', name: 'add_questionApi', methods: ['POST'])]
    public function addQuestionApi(Request $request, QuestionRepository $questionRepository,ManagerRegistry $doctrine): JsonResponse
    {
        $manager = $doctrine->getManager();
        // Récupérer les données POST (y compris les fichiers)
        $data = $request->request->all();

        // Créer la question
        $question = new Question();
        $question->setQuestion($data['question']);
        $question->setType($data['type']);
        $question->setDifficulteeeee($data['difficulteeeee']);
        $question->setReponseCorrecte($data['reponseCorrecte']);
        $question->setReponses(json_decode($data['reponses'], true)); // Convertir le JSON en tableau

        // Gérer le fichier audio si le type est "Compréhension orale"
        if ($data['type'] === 'Compréhension orale' && $request->files->has('audio')) {
            /** @var UploadedFile $audioFile */
            $audioFile = $request->files->get('audio');
            $audioFileName = uniqid() . '.' . $audioFile->guessExtension();


            try {
                // Déplacer le fichier dans le répertoire public_html/audio
                $audioFile->move($this->getParameter('kernel.project_dir') . '/public_html/audio', $audioFileName);
                // Sauvegarder l'URL dans la base de données
                $question->setAudio('/audio/' . $audioFileName);
            } catch (FileException $e) {
                // Gérer l'erreur de téléchargement du fichier
                return new JsonResponse(['status' => 'error', 'message' => 'Erreur lors du téléchargement de l\'audio'], 400);
            }
        }

        // Enregistrer la question dans la base de données

        $manager->persist($question);
        $manager->flush();

        // Retourner une réponse JSON de succès
        return new JsonResponse(['status' => 'success', 'data' => $question], 201);
    }

    private function determineNiveau(int $score): string
    {


        if ($score > 0 && $score < 100) {
            return 'Aucun niveau';
        } elseif ($score >= 100 && $score < 200) {
            return 'A1';
        }
        elseif ($score >= 200 && $score < 300) {
            return 'A2';
        }
        elseif ($score >= 300 && $score < 400) {
            return 'B1';
        }
        elseif ($score >= 400 && $score < 500) {
            return 'B2';
        }
        elseif ($score >= 500 && $score < 600) {
            return 'C1';
        }
        elseif ($score >= 600 && $score < 699) {
            return 'C2';
        }
        else {
            return 'null';
        }
    }
    #[Route('/get/questions', name: 'all_forms', methods: ['GET'])]
    public function getAllForms(QuestionRepository $questionRepository, SerializerInterface $serializer): JsonResponse
    {
        $questions = $questionRepository->findAll();

        // Use Serializer to convert objects to JSON
        $data = $serializer->serialize($questions, 'json', ['groups' => ['question']]);
        return new JsonResponse($data, Response::HTTP_OK, [], true); // Return JSON response
    }
    #[Route('/delete/question/{id}', name: 'form_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $question = $entityManager->getRepository(Question::class)->find($id);

        if (!$question) {
            return new JsonResponse(['message' => 'Form not found'], Response::HTTP_NOT_FOUND);
        }
        $entityManager->remove($question);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Form deleted successfully'], Response::HTTP_OK);
    }
    #[Route('/questions/{id}', name: 'app_question_show', methods: ['GET'])]
    public function show(Question $question, SerializerInterface $serializer): JsonResponse
    {
        $data = $serializer->serialize($question, 'json', ['groups' => ['question']]);

        return new JsonResponse($data, Response::HTTP_OK, [], true); // Use Response::HTTP_OK (200)
    }
    #[Route('/edit/questions/{id}', name: 'app_question_update', methods: ['POST'])]
    public function update(
        Request $request,
        Question $question,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        LoggerInterface $logger // Inject the logger
    ): Response {
        $questionData = $request->request->all(); // Get all data except files
        $logger->info('Question Data: ' . json_encode($questionData));

        $audioFile = $request->files->get('audio'); // Get the audio file

        // Check if the question exists
        if (!$question) {
            return new Response('Question not found', Response::HTTP_NOT_FOUND);
        }

        // Validate required fields (you might want to use Symfony's Form component for more robust validation)
        if (empty($questionData['question']) || empty($questionData['type']) || empty($questionData['difficulteeeee']) || empty($questionData['reponseCorrecte']) || empty($questionData['reponses'])) {
            return new Response('Missing required fields', Response::HTTP_BAD_REQUEST);
        }

        //Update the question
        $question->setQuestion($questionData['question']);
        $question->setType($questionData['type']);
        $question->setDifficulteeeee($questionData['difficulteeeee']);
        $question->setReponseCorrecte($questionData['reponseCorrecte']);
        $question->setReponses(json_decode($questionData['reponses'], true)); // Decode the JSON string to array

        // Handle audio file upload
        if ($audioFile) {
            $originalFilename = pathinfo($audioFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = '/audio/' . $safeFilename . '.' . $audioFile->guessExtension(); // Set the new filename

            try {
                $audioFile->move(
                    $this->getParameter('audio_directory'), // Define this parameter in services.yaml
                    $newFilename
                );

                $question->setAudio($newFilename); // Save the filename in the database
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
                $logger->error('File upload error: ' . $e->getMessage()); // Log the error
                return new Response('Failed to upload audio file', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        $entityManager->persist($question);
        $entityManager->flush();

        return new Response('Question updated successfully', Response::HTTP_OK);
    }
    #[Route('/pdf/test/{etudiantId}', name: 'app_pdf_test')]
    public function editPdf(int $etudiantId, EtudiantRepository $etudiantRepository): Response
    {
        $etudiant = $etudiantRepository->find($etudiantId);

        if (!$etudiant) {
            return new Response('Etudiant not found!', Response::HTTP_NOT_FOUND);
        }

        $nom = $etudiant->getNom();
        $prenom = $etudiant->getPrenom();

        $test = $etudiant->getTests()->last();
        if (!$test) {
            return new Response('No test results found for this etudiant!', Response::HTTP_NOT_FOUND);
        }

        $score = $test->getScoreTotal();
        $niveau = $test->getNiveau();

        $pdfPath = $this->getParameter('kernel.project_dir') . '/public_html/pdf/TCFCertficat.pdf';

        if (!file_exists($pdfPath)) {
            return new Response('PDF file not found!', Response::HTTP_NOT_FOUND);
        }

        // Extend FPDI (which extends FPDF)
        $pdf = new class extends Fpdi {
        };

        $pdf->setSourceFile($pdfPath);
        $tplId = $pdf->importPage(1);

        // Get the dimensions of the original PDF
        $size = $pdf->getTemplateSize($tplId);

        // Create a new page with the same size as the original
        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);

        // Use the imported page
        $pdf->useTemplate($tplId, 0, 0, $size['width'], $size['height']);

        $pdf->AddFont('AlexBrush', '', 'AlexBrush-Regular.php');
        $pdf->AddFont('Poppins-Bold', '', 'Poppins-Bold.php');

        // Write text on PDF
        $pdf->SetTextColor(43, 45, 66);
        $pdf->SetFont('AlexBrush', '', 50);
        $pdf->SetXY(98, 102);
        $pdf->Cell(100, 10, $nom . ' ' . $prenom, 0, 1, 'C');

        $pdf->SetFont('Poppins-Bold', '', 16);
        $pdf->SetXY(100, 122.3);
        $pdf->Cell(100, 10, $score, 0, 1, 'C');

        $pdf->SetFont('Poppins-Bold', '', 36);
        $pdf->SetXY(98.5, 140);
        $pdf->Cell(100, 10, $niveau, 0, 1, 'C');

        $pdfContent = $pdf->Output('S');

        $response = new Response($pdfContent);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="TCF_Certficat.pdf"');

        return $response;
    }




}
