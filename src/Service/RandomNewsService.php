<?php

namespace App\Service;

use App\Entity\News;
use Doctrine\ORM\EntityManagerInterface;

class RandomNewsService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Повертає $count рандомних новин.
     */
    public function getRandomNews(int $count = 4): array
    {
        $allIds = $this->entityManager->createQueryBuilder()
            ->select('n.id')
            ->from(News::class, 'n')
            ->getQuery()
            ->getSingleColumnResult(); // тільки ID

        shuffle($allIds);
        $randomIds = array_slice($allIds, 0, $count);

        return $this->entityManager->createQueryBuilder()
            ->select('n')
            ->from(News::class, 'n')
            ->where('n.id IN (:ids)')
            ->setParameter('ids', $randomIds)
            ->getQuery()
            ->getResult();
    }
//    private array $imageTopics = ['education', 'students', 'books', 'school', 'learning', 'university'];
//
//    public function getNewsWithImages(int $count = 6): array
//    {
//        $news = $this->getRandomNews($count);
//        shuffle($this->imageTopics);
//
//        foreach ($news as $index => $item) {
//            $item->imageUrl = 'https://source.unsplash.com/random/800x400?' . $this->imageTopics[$index % count($this->imageTopics)];
//        }
//
//        return $news;
//    }
}
