<?php
// src/Service/ProfileService.php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProfileService
{
    public function __construct(private EntityManagerInterface $em, private Security $security) {}

    public function updateProfile(Request $request): void
    {
        $user = $this->security->getUser();
        if (!$user) {
            return;
        }

        $user->setFullName($request->request->get('fullName'));
        $user->setPhone($request->request->get('phone'));
        $user->setAbout($request->request->get('about'));


        $interestIds = $request->request->get('interests', []);
        if (!is_array($interestIds)) {
            $interestIds = [];
        }
        $interests = $this->em->getRepository(Interest::class)->findBy(['id' => $interestIds]);

        $user->getInterests()->clear();
        foreach ($interests as $interest) {
            $user->addInterest($interest);
        }

        for ($i = 1; $i <= 4; $i++) {
            $name = $request->request->get("subject{$i}_name");
            $score = $request->request->get("subject{$i}_score");

            if ($name && $score) {
                $setterName = "setSubject{$i}Name";
                $setterScore = "setSubject{$i}Score";

                $user->$setterName($name);
                $user->$setterScore((int)$score);
            }
        }

        $avatar = $request->files->get('avatar');
        if ($avatar) {
            $filename = uniqid() . '.' . $avatar->guessExtension();
            $avatar->move(__DIR__ . '/../../public/uploads/avatars', $filename);
            $user->setAvatar($filename);
        }

        $this->em->persist($user);
        $this->em->flush();

    }
    public function isProfileComplete(User $user): bool
    {
        return $user->getFullName() &&
            $user->getPhone() &&
            $user->getAbout() &&
            $user->getSubject1Name() && $user->getSubject1Score() &&
            $user->getSubject2Name() && $user->getSubject2Score() &&
            $user->getSubject3Name() && $user->getSubject3Score() &&
            $user->getSubject4Name() && $user->getSubject4Score();
    }
}
