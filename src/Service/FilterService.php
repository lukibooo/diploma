<?php
namespace App\Service;

use App\Entity\Specialties;
use App\Entity\University;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class FilterService
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function getFilteredResults(array $userSubjects, ?string $city, ?string $specialtyName, ?int $priceFrom, ?int $priceTo, bool $military): array
    {
        $subjectNames = array_column($userSubjects, 'subject');

        $qb = $this->em->createQueryBuilder()
            ->select('u', 's', 'c', 'n', 'subj')
            ->from(University::class, 'u')
            ->join('u.city', 'c')
            ->join('u.specialties', 's')
            ->join('s.numbers', 'n')
            ->join('n.subject', 'subj')
            ->where('subj.name IN (:subjectNames)')
            ->setParameter('subjectNames', $subjectNames);

        if ($city) {
            $qb->andWhere('c.name = :city')->setParameter('city', $city);
        }

        if ($specialtyName) {
            $qb->andWhere('s.name LIKE :specialty')->setParameter('specialty', '%' . $specialtyName . '%');
        }

        if ($priceFrom !== null) {
            $qb->andWhere('s.price >= :priceFrom')->setParameter('priceFrom', $priceFrom);
        }

        if ($priceTo !== null) {
            $qb->andWhere('s.price <= :priceTo')->setParameter('priceTo', $priceTo);
        }

        if ($military) {
            $qb->andWhere('u.hasMilitary = true');
        }

        $results = $qb->getQuery()->getResult();

        $output = [];

        foreach ($results as $university) {
            foreach ($university->getSpecialties() as $specialty) {
                $numbers = $specialty->getNumbers(); // список Number (коефіцієнти з предметами)

                // Розрахунок суми коефіцієнтів
                $coefficientSum = 0;
                foreach ($numbers as $number) {
                    $coefficientSum += $number->getValue();
                }

                // Масштабування коефіцієнтів, якщо перевищують норму
                $normalizationFactor = ($coefficientSum > 1.0) ? 1.0 / $coefficientSum : 1.0;

                $matchedSubjects = [];
                $totalScore = 0;
                $matchedNames = [];

                foreach ($userSubjects as $userSubject) {
                    $subjectName = $userSubject['subject'];
                    $userScore = $userSubject['score'];

                    // Знаходимо коефіцієнт для предмета
                    $coefficient = null;
                    foreach ($numbers as $number) {
                        if ($number->getSubject()->getName() === $subjectName) {
                            $coefficient = $number->getValue();
                            break;
                        }
                    }

                    // Додаємо, якщо знайдено відповідність
                    if ($coefficient !== null && !in_array($subjectName, $matchedNames, true)) {
                        $scaledCoef = $coefficient * $normalizationFactor;
                        $scorePart = $userScore * $scaledCoef;
                        $totalScore += $scorePart;

                        $matchedSubjects[] = [
                            'subject' => $subjectName,
                            'userScore' => $userScore,
                            'coefficient' => round($coefficient, 2),
                            'scaledCoef' => round($scaledCoef, 3),
                            'scorePart' => round($scorePart, 2),
                        ];

                        $matchedNames[] = $subjectName;
                    }
                }

                // Додаємо, якщо всі предмети знайдено
                if (count($matchedSubjects) === count($userSubjects)) {
                    $finalScore = min(round($totalScore, 2), 200);

                    $output[] = [
                        'university' => $university,
                        'specialty' => $specialty,
                        'score' => $finalScore,
                        'matchedSubjects' => $matchedSubjects,
                    ];
                }
            }
        }

        usort($output, fn($a, $b) => $b['score'] <=> $a['score']);

        return $output;
    }
}
