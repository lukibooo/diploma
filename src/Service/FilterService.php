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

    public function getFilteredResults(Request $request): array
    {
//        $city = $request->request->get('city');
//        $specialtyName = $request->request->get('specialty');
//        $priceFrom = $request->request->get('price_from');
//        $priceTo = $request->request->get('price_to');
//        $military = $request->request->get('military');
//        $subjects = $request->request->all();
//
//        $qb = $this->em->createQueryBuilder()
//            ->select('s', 'u', 'c', 'n', 'subj', 'sp')
//            ->from(Specialties::class, 's')
//            ->leftJoin('s.universities', 'u')
//            ->leftJoin('u.city', 'c')
//            ->leftJoin('s.numbers', 'n')
//            ->leftJoin('n.subjects', 'subj')
//            ->leftJoin('s.specialtyPrice', 'sp')
//
//        ;
//
//        if ($specialtyName) {
//            $qb->andWhere('LOWER(TRIM(s.name)) = :specialty')
//                ->setParameter('specialty', mb_strtolower(trim($specialtyName)));
//        }
//
//        if ($city) {
//            $qb->andWhere('c.name = :city')->setParameter('city', $city);
//        }
//
//        if (is_numeric($priceFrom)) {
//            $qb->andWhere('sp.price >= :priceFrom')->setParameter('priceFrom', $priceFrom);
//        }
//
//        if (is_numeric($priceTo)) {
//            $qb->andWhere('sp.price <= :priceTo')->setParameter('priceTo', $priceTo);
//        }
//
//        if ($military) {
//            $qb->andWhere('u.military = true');
//        }
//
//        return $qb;
        $city = $request->request->get('city');
        $specialtyName = $request->request->get('specialty');
        $priceFrom = $request->request->get('price_from');
        $priceTo = $request->request->get('price_to');
        $military = $request->request->get('military');
        $subjects = $request->request->all();

        $qb = $this->em->createQueryBuilder()
            ->select('s', 'u', 'c', 'n', 'subj')
            ->from(Specialties::class, 's')
            ->leftJoin('s.universities', 'u')
            ->leftJoin('u.city', 'c')
            ->leftJoin('s.numbers', 'n')
            ->leftJoin('n.subjects', 'subj');

        if ($specialtyName) {
            $qb->andWhere('LOWER(TRIM(s.name)) = :specialty')
                ->setParameter('specialty', mb_strtolower(trim($specialtyName)));
        }

        if ($city) {
            $qb->andWhere('c.name = :city')->setParameter('city', $city);
        }
        if (is_numeric($priceFrom)) {
            $qb->andWhere('s.price >= :priceFrom')->setParameter('priceFrom', $priceFrom);
        }

        if (is_numeric($priceTo)) {
            $qb->andWhere('s.price <= :priceTo')->setParameter('priceTo', (int)$priceTo);
        }

        if ($military) {
            $qb->andWhere('u.military = true');
        }
        return $qb->getQuery()->getResult();

        $output = [];

        foreach ($specialties as $specialty) {
            $university = $specialty->getUniversity();
            $numbers = $specialty->getNumbers();
            $coefficientSum = 0;
            foreach ($numbers as $number) {
                $coefficientSum += $number->getValue();
            }

            $normalizationFactor = ($coefficientSum > 1.0) ? 1.0 / $coefficientSum : 1.0;

            $matchedSubjects = [];
            $matchedNames = [];
            $totalScore = 0;

            foreach ($specialty->getNumbers() as $number) {
                $subjectName = mb_strtolower($number->getSubject()->getName());
                $coefficient = $number->getValue(); // або правильний геттер

                foreach ($userSubjects as $userSubject) {
                    if (
                        mb_strtolower($userSubject['subject']) === $subjectName &&
                        is_numeric($userSubject['score'])
                    ) {
                        $scorePart = $userSubject['score'] * $coefficient;
                        $totalScore += $scorePart;
                    }
                }
            }
        }
        foreach ($results as $specialty) {
            foreach ($specialty->getUniversities() as $university) {
                $output[] = [
                    'specialty' => $specialty,
                    'university' => $university,
                    'city' => $city,

                ];
            }
        }

        return $output;
    }

//        $specialties = $qb->getQuery()->getResult();
//        $output = [];
//
//        foreach ($specialties as $specialty) {
//            $university = $specialty->getUniversity();
//            $numbers = $specialty->getNumbers();
//            $coefficientSum = 0;
//            foreach ($numbers as $number) {
//                $coefficientSum += $number->getValue();
//            }
//
//            $normalizationFactor = ($coefficientSum > 1.0) ? 1.0 / $coefficientSum : 1.0;
//
//            $matchedSubjects = [];
//            $matchedNames = [];
//            $totalScore = 0;
//
//            foreach ($specialty->getNumbers() as $number) {
//                $subjectName = mb_strtolower($number->getSubject()->getName());
//                $coefficient = $number->getValue(); // або правильний геттер
//
//                foreach ($userSubjects as $userSubject) {
//                    if (
//                        mb_strtolower($userSubject['subject']) === $subjectName &&
//                        is_numeric($userSubject['score'])
//                    ) {
//                        $scorePart = $userSubject['score'] * $coefficient;
//                        $totalScore += $scorePart;
//                    }
//                }
//            }
//            $finalScore = min(round($totalScore, 2), 178);
//            $output[] = [
//                'university' => $university,
//                'specialty' => $specialty,
//                'score' => $finalScore,
//                'matchedSubjects' => $matchedSubjects,
//            ];
//        }
    public function countFilteredResults(array $filters): int
    {
        $qb = $this->em->createQueryBuilder()
            ->select('COUNT(DISTINCT s.id)')
            ->from(Specialties::class, 's')
            ->leftJoin('s.university', 'u')
            ->leftJoin('s.numbers', 'n')
            ->leftJoin('n.subject', 'subj');

        if (!empty($filters['city'])) {
            $qb->andWhere('LOWER(TRIM(u.city)) = LOWER(:city)')
                ->setParameter('city', mb_strtolower(trim($filters['city'])));
        }

        if (!empty($filters['specialty'])) {
            $qb->andWhere('LOWER(TRIM(s.name)) LIKE LOWER(:specialty)')
                ->setParameter('specialty', '%' . trim($filters['specialty']) . '%');
        }

        if (!empty($filters['priceFrom'])) {
            $qb->andWhere('s.price >= :priceFrom')
                ->setParameter('priceFrom', $filters['priceFrom']);
        }

        if (!empty($filters['priceTo'])) {
            $qb->andWhere('s.price <= :priceTo')
                ->setParameter('priceTo', $filters['priceTo']);
        }

        if (!empty($filters['military'])) {
            $qb->andWhere('s.military = :military')
                ->setParameter('military', true);
        }
//        dd($qb->getQuery()->getScalarResult());
        return (int) $qb->getQuery()->getSingleScalarResult();
    }
    public function getFilteredResultsFromArray(array $filters): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('s', 'u', 'c', 'n', 'subj', 'sp')
            ->from(Specialties::class, 's')
            ->leftJoin('s.universities', 'u')
            ->leftJoin('u.city', 'c')
            ->leftJoin('s.numbers', 'n')
            ->leftJoin('n.subjects', 'subj')
            ->leftJoin('s.specialtyPrice', 'sp');

        if (!empty($filters['specialty'])) {
            $qb->andWhere('LOWER(TRIM(s.name)) = :specialty')
                ->setParameter('specialty', mb_strtolower(trim($filters['specialty'])));
        }

        if (!empty($filters['city'])) {
            $qb->andWhere('c.name = :city')
                ->setParameter('city', $filters['city']);
        }

        if (!empty($filters['price_from'])) {
            $qb->andWhere('sp.price >= :priceFrom')
                ->setParameter('priceFrom', $filters['price_from']);
        }

        if (!empty($filters['price_to'])) {
            $qb->andWhere('sp.price <= :priceTo')
                ->setParameter('priceTo', $filters['price_to']);
        }

        if (!empty($filters['military'])) {
            $qb->andWhere('u.military = true');
        }

        $results = $qb->getQuery()->getResult();

        $output = [];
        foreach ($results as $specialty) {
            foreach ($specialty->getUniversities() as $university) {
                $output[] = [
                    'specialty' => $specialty,
                    'university' => $university,
                ];
            }
        }

        return $output;
    }


}
