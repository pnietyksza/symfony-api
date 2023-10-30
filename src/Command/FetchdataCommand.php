<?php

namespace App\Command;

use App\Entity\Currency;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;

#[AsCommand(
    name: 'fetchdata',
    description: 'Fetch data from source',
)]
class FetchdataCommand extends Command
{
    public function __construct(
        private HttpClientInterface $client,
        public EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $io = new SymfonyStyle($input, $output);

        try {
            $data = $this->client->request(
                'GET',
                'http://api.nbp.pl/api/exchangerates/tables/a/?format=json'
            );
        } catch (ClientExceptionInterface $e) {
            exit('Exception: ' . $e);
        }

        $statusCode = $data->getStatusCode();

        if ($statusCode === 200) {
            $content = json_decode($data->getContent());
            $items = $content[0]->rates;

            foreach ($items as $key => $value) {
                $existingCurrency = $this->entityManager
                    ->getRepository(Currency::class)
                    ->findOneBy(['code' => $value->code]);

                if (!$existingCurrency) {
                    $currency = new Currency();
                    $currency->setName($value->currency);
                    $currency->setCode($value->code);
                    $currency->setValue($value->mid);
                    $this->entityManager->persist($currency);
                }
            }
            $this->entityManager->flush();

            $io->success('Now data are update');

            return Command::SUCCESS;
        } else {
            $io->error('Something went wrog');

            return Command::FAILURE;
        }
    }
}
