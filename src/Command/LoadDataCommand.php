<?php

namespace App\Command;

use App\Entity\City;
use App\Entity\Interest;
use App\Entity\News;
use App\Entity\Number;
use App\Entity\Region;
use App\Entity\Specialties;
use App\Entity\SpecialtyPrice;
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
//
//


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
//        $file = fopen(__DIR__ . '/../../public/News.csv', 'r');
//
//        $lineNumber = 0;
//
//        while (($data = fgetcsv($file)) !== false) {
//            $lineNumber++;
//            if ($lineNumber === 1) {
//                continue;
//            }
//
//            $title = $data[0];
//            $description = $data[1];
//            $date = $data[2];
//
//            $news = new News();
//            $news->setTitle($title);
//            $news->setContent($description);
//            $news->setPublishedAt($date);
//
//            $this->entityManager->persist($news);
//        }
//
//        $this->entityManager->flush();
//        fclose($file);
//
//        $io->success('Loaded');
//        return self::SUCCESS;
//    }


//

//        $file = fopen(__DIR__ . '/../../public/Specialt.csv', 'r');
//        $lineNumber = 0;
//        $specialtyMap = []; // [name + code] => Specialties
//
//        while (($data = fgetcsv($file))) {
//            $lineNumber++;
//            if ($lineNumber === 1) continue;
//
//            $universityName = trim($data[0]);
//            $specName = trim($data[4]);
//            $specCode = trim($data[3]);
//
//            if (!$specName || !$specCode) continue;
//
//            $university = $this->entityManager
//                ->getRepository(University::class)
//                ->findOneBy(['name' => $universityName]);
//
//            if (!$university) continue;
//
//            $key = $specCode . '|' . $specName;
//
//            if (!isset($specialtyMap[$key])) {
//                $specialty = new Specialties();
//                $specialty->setName($specName);
//                $specialty->setCode($specCode);
//                $this->entityManager->persist($specialty);
//
//                $specialtyMap[$key] = $specialty;
//            }
//
//            $specialty = $specialtyMap[$key];
//            $specialty->addUniversity($university);
//        }
//
//        $this->entityManager->flush();
//        $file = fopen(__DIR__ . '/../../public/Specialt.csv', 'r');
//        $lineNumber = 0;
//
//        while (($data = fgetcsv($file))) {
//            $lineNumber++;
//            if ($lineNumber === 1) continue;
//
//            $universityName = trim($data[0]);
//            $specCode = trim($data[3]);
//            $specName = trim($data[4]);
//            $price = $data[6];
//
//            if (!$specCode || !$specName || !$price) continue;
//
//            $university = $this->entityManager
//                ->getRepository(University::class)
//                ->findOneBy(['name' => $universityName]);
//
//            if (!$university) continue;
//
//            $specialty = $this->entityManager
//                ->getRepository(Specialties::class)
//                ->findOneBy([
//                    'code' => $specCode,
//                    'name' => $specName
//                ]);
//
//            if (!$specialty) continue;
//
//            // перевірка: чи вже є така звʼязка?
//            $existingOffer = $this->entityManager->getRepository(SpecialtyPrice::class)
//                ->findOneBy([
//                    'specialty' => $specialty,
//                    'university' => $university,
//                ]);
//
//            if (!$existingOffer) {
//                $offer = new SpecialtyPrice();
//                $offer->setSpecialty($specialty);
//                $offer->setUniversity($university);
//                $offer->setPrice($price);
//                $this->entityManager->persist($offer);
//            }
//        }
//
//        $this->entityManager->flush();
//        return Command::SUCCESS;
//    }


        $file = fopen(__DIR__ . '/../../public/Coef.csv', 'r');
        $header = fgetcsv($file, 0, ','); // перший рядок — назви предметів
        $subjectNames = array_slice($header, 1); // припускаємо [0] — code

        while (($row = fgetcsv($file, 0, ',')) !== false) {
            $code = trim($row[0]);

            $specialties = $this->entityManager->getRepository(Specialties::class)
                ->findBy(['code' => $code]);

            if (!$specialties) continue;

            for ($i = 1; $i < count($row); $i++) {
                $subjectName = $subjectNames[$i - 1];
                $valueStr = str_replace(',', '.', $row[$i]);
                $value = floatval($valueStr);
                if ($value === 0.0) continue;

                $subject = $this->entityManager->getRepository(Subject::class)
                    ->findOneBy(['name' => $subjectName]);

                if (!$subject) continue;

                // Перевіряємо, чи вже є Number з таким subject + value
                $existing = $this->entityManager->getRepository(Number::class)
                    ->createQueryBuilder('n')
                    ->leftJoin('n.subjects', 's')
                    ->where('s.id = :subjectId')
                    ->andWhere('n.value = :value')
                    ->setParameter('subjectId', $subject->getId())
                    ->setParameter('value', $value)
                    ->getQuery()
                    ->getOneOrNullResult();

                if ($existing) {
                    $number = $existing;
                } else {
                    $number = new Number();
                    $number->setValue($value);
                    $number->addSubject($subject);
                    $this->entityManager->persist($number);
                }

                foreach ($specialties as $specialty) {
                    if (!$number->getSpecialties()->contains($specialty)) {
                        $number->addSpecialty($specialty);
                    }
                }
            }
        }

        $this->entityManager->flush();
        return Command::SUCCESS;
    }

}
