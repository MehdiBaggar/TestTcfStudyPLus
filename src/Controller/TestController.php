<?php

namespace App\Controller;

use App\Repository\TestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(): Response
    {
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
    #[Route('/tests', name: 'api_test_list', methods: ['GET'])]
    public function listTests(TestRepository $testRepository): JsonResponse
    {
        $tests = $testRepository->findAll();

        $data = array_map(function ($test) {
            return [
                'id' => $test->getId(),
                'data' => $test->getData() ? $test->getData()->format('Y-m-d') : null,
                'scoreTotal' => $test->getScoreTotal(),
                'niveau' => $test->getNiveau(),
            ];
        }, $tests);

        return new JsonResponse($data);
    }
    #[Route('/allTests', name: 'allTests',)]
    public function allTests(): Response
    {
        return $this->render('test/alltests.html.twig', [
            'controller_name' => 'TcfController',
        ]);
    }
}
