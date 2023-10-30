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

    #[Route('/currencies', name: 'currencies', methods: ['GET'])]
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

    #[Route('/currency/{code}', name: 'currency', methods: ['GET'])]
    public function currency(
        ManagerRegistry $doctrine,
        Request $request,
        string $code,
    ): JsonResponse {
        dump('get');
        exit;
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

    #[Route('/currency', name: 'create currency', methods: ['POST', 'GET'])]
    public function postCurrency(
        ManagerRegistry $doctrine,
        Request $request,
    ): JsonResponse {

        if (!$request->isMethod('POST'))
            return $this->json(['Send POST request or read documentation on /']);

        $name = $request->request->filter('name');
        $code = $request->request->filter('code');
        $value = $request->request->filter('value');

        // on this step we've got problem with types, when we send in request
        // value "0" interpreter can see it like a empty string and empty($value) will return 0
        if (
            empty($name) ||
            empty($code) ||
            empty($value)
        ) {
            return $this->json(['No one fields could be empty']);
        }

        if (!strlen($code) === 3) {
            return $this->json(['Code shoud have 3 chars']);
        }

        if (
            !floatval($value) ||
            !is_float(floatval($value))
        ) {
            return $this->json(['Value should be integer or float']);
        }

        return $this->json(['this is post request']);
    }
}
