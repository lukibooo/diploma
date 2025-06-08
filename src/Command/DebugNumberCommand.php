<?php

namespace App\Command;

use App\Entity\Number;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:debug-numbers')]
class DebugNumberCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Виводить спеціальності, предмети і коефіцієнти з таблиці Number');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $numbers = $this->em->getRepository(Number::class)->findAll();

        foreach ($numbers as $number) {
            $specialty = $number->getSpecialty();
            $subject = $number->getSubject();
            $value = $number->getValue();

            $output->writeln(sprintf(
                '📚 %s / %s → %s',
                $specialty?->getCode() ?? 'NULL',
                $subject?->getName() ?? 'NULL',
                $value ?? 'NULL'
            ));
        }

        return Command::SUCCESS;
    }
}
