<?php

namespace App\Command;

use App\Entity\City;
use App\Entity\Interest;
use App\Entity\News;
use App\Entity\Number;
use App\Entity\Region;
use App\Entity\Specialties;
use App\Entity\Subject;
use App\Entity\University;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:load-data',
    description: 'Add a short description for your command',
)]
class LoadDataCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);


//        $file = fopen(__DIR__.'/../../public/Univer.csv', 'r');
//
//        $lineNumber = 0;
//        $allCities = [];
//
//        while($data = fgetcsv($file)) {
//            $lineNumber++;
//
//            if($lineNumber === 1) {
//                continue;
//            }
//
//            $region = $data[2];
//
//            if(!isset($allCities[$region])) {
//                $allCities[$region] = [];
//            }
//
//            $city = $data[1];
//            if(!in_array($city,$allCities[$region])) {
//                $allCities[$region][] = $city;
//            }
//        }
//        foreach($allCities as $region => $cities) {
//            $regionEntity = new Region();
//            $regionEntity->setName($region);
//            $this->entityManager->persist($regionEntity);
//
//            foreach($cities as $cityName) {
//                $cityEntity = new City();
//                $cityEntity->setName($cityName);
//                $cityEntity->setRegion($regionEntity);
//                $this->entityManager->persist($cityEntity);
//            }
//
//        }
//        $this->entityManager->flush();

//

//        $file = fopen(__DIR__ . '/../../public/Univer.csv', 'r');
//
//        $lineNumber = 0;
//
//        while($data = fgetcsv($file)) {
//            $lineNumber++;
//
//            if ($lineNumber === 1) {
//                continue;
//            }
//
//            $cityName = $data[1];
//            $city = $this->entityManager->getRepository(City::class)->findOneBy(['name' => $cityName]);
//
//            $university = new University();
//            $university->setCity($city);
//            $university->setName($data[0]);
//
//            $university->setEmail($data[5]);
//            $university->setPhone($data[4]);
//            $university->setSort($data[6]);
//            $university->setLink($data[7]);
//            $university->setDescription($data[3]);
//
//            $this->entityManager->persist($university);
//        }
//        $this->entityManager->flush();

//        $file = fopen(__DIR__ . '/../../public/Specialt.csv', 'r');
//
//        $lineNumber = 0;
//
//        while (($data = fgetcsv($file))) {
//            $lineNumber++;
//            if ($lineNumber === 1) continue;
//
//            $universityName = trim($data[0]);
//            $specName = trim($data[4]);
//            $specCode = trim($data[3]);
//            $description = trim($data[5]);
//            $price = $data[6];
//
//            $university = $this->entityManager
//                ->getRepository(University::class)
//                ->findOneBy(['name' => $universityName]);
//
//            if (!$university) continue;
//
//            $specialty = new Specialties();
//            $specialty->setUniversity($university);
//            $specialty->setName($specName);
//            $specialty->setCode($specCode);
//            $specialty->setDescription($description);
//            $specialty->setPrice($price);
//
//            $this->entityManager->persist($specialty);
//        }
//
//        $this->entityManager->flush();

//

//        $file = fopen(__DIR__ . '/../../public/All.csv', 'r');
//        fgetcsv($file, 1000, ',');
//        fgetcsv($file, 1000, ',');
//
//        $subjectNames = [
//            'Українська мова',
//            'Математика',
//            'Історія України',
//            'Англійська мова',
//            'Німецька мова',
//            'Французька мова',
//            'Іспанська мова',
//            'Біологія',
//            'Фізика',
//            'Хімія',
//            'Українська література',
//            'Географія',
//        ];
//
//
//        $subjectRepo = $this->entityManager->getRepository(Subject::class);
//        $specialtyRepo = $this->entityManager->getRepository(Specialties::class);
//
//        $subjects = [];
//        foreach ($subjectNames as $name) {
//            $subject = $subjectRepo->findOneBy(['name' => $name]);
//            if (!$subject) {
//                $subject = (new Subject())->setName($name);
//                $this->entityManager->persist($subject);
//            }
//            $subjects[] = $subject;
//        }
//
//
//        while (($row = fgetcsv($file, 1000, ',')) !== false) {
//            $code = trim($row[0]);
//            $specialty = $specialtyRepo->findOneBy(['code' => $code]);
//
//            if (!$specialty) {
//                $output->writeln("❗ Спеціальність не знайдена: $code");
//                continue;
//            }
//
//            foreach ($subjects as $index => $subject) {
//                $raw = $row[$index + 1] ?? '';
//                $value = floatval(str_replace(',', '.', trim($raw)));
//
//                if ($raw === '' || !is_numeric($value)) {
//                    $output->writeln("⚠️  Пропущено ($code / {$subject->getName()}): '$raw'");
//                    continue;
//                }
//
//                $number = new Number();
//                $number->setSpecialty($specialty);
//                $number->setSubject($subject);
//                $number->setValue($value);
//                $this->entityManager->persist($number);
//            }
//        }
//
//
//        $this->entityManager->flush();
//        $this->entityManager->clear();
//
//        $io->success('Loaded');
//
//        return self::SUCCESS;

//        $handle = fopen(__DIR__ . '/../../public/Interest.csv', 'r');
//
//        if (!$handle) {
//            $io->error('Не вдалося відкрити файл Interest.csv');
//            return Command::FAILURE;
//        }
//
//        $header = fgetcsv($handle); // Пропускаємо заголовок
//        $schemaTool = new SchemaTool($this->entityManager);
//        $schemaTool->createSchema([$this->entityManager->getClassMetadata(Interest::class)]);
//        $count = 0;
//        while (($data = fgetcsv($handle)) !== false) {
//            $name = $data[0];
//            if (!$name) continue;
//
//            $interest = new Interest();
//            $interest->setName($name);
//            $this->entityManager->persist($interest);
//            $count++;
//        }
//
//        fclose($handle);
//        $this->entityManager->flush();
//        $io->success('Loaded');
//
        $file = fopen(__DIR__ . '/../../public/News.csv', 'r');

        $lineNumber = 0;

        while (($data = fgetcsv($file)) !== false) {
            $lineNumber++;
            if ($lineNumber === 1) {
                continue;
            }

            $title = $data[0];
            $description = $data[1];
            $date = $data[2];

            $news = new News();
            $news->setTitle($title);
            $news->setContent($description);
            $news->setPublishedAt($date);

            $this->entityManager->persist($news);
        }

        $this->entityManager->flush();
        fclose($file);

        $io->success('Loaded');
        return self::SUCCESS;
    }
}
