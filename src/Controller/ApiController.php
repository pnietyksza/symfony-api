<?php

namespace App\Controller;

use App\Entity\Currency;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/', name: 'app_api')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Hello! Welcome to restAPI with currencies',
            'routes:' =>
            [
                '(GET)/currencies - is the endpoint with whole informations about all currencies',
                '(GET)/currency/code - is with informations about one currency *(code(3 string)',
                '(POST)/currency - you can create your own currency *(name(255 string),code(3 string),value(float))',
                '(PUT)/currency - for actualization one currency *(name(255 string),code(3 string),value(float))',
                '(DELETE)/currency - for delete currency *(code(3 string))'
            ]
        ]);
    }

    #[Route('/currencies', name: 'currencies', methods: ['get'])]
    public function currencies(
        ManagerRegistry $doctrine,
    ): JsonResponse {
        $currencies = $doctrine
            ->getRepository(Currency::class)
            ->findAll();

        $data = [];

        foreach ($currencies as $currency) {
            $data[] = [
                'name' => $currency->getName(),
                'code' => $currency->getCode(),
                'value' => $currency->getValue(),
            ];
        }
        return $this->json($data);
    }

    #[Route('/currency/{code}', name: 'currency', methods: ['get'])]
    public function currency(
        ManagerRegistry $doctrine,
        Request $request,
        string $code,
    ): JsonResponse {
        if (strlen($code) !== 3)
            return $this->json([
                "Please put right currency code"
            ]);

        $currency = $doctrine
            ->getRepository(Currency::class)
            ->findOneBy([
                'code' => $code,
            ]);

        if (!$currency)
            return ($this->json([
                "Sorry we don't have that currency"
            ]));

        $data = [
            'name' => $currency->getName(),
            'code' => $currency->getCode(),
            'value' => $currency->getValue(),
        ];

        return $this->json($data);
    }
}
