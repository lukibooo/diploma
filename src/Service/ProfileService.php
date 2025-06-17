<?php
// src/Service/ProfileService.php

namespace App\Service;

use App\Entity\Interest;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;

class ProfileService
{
    public function __construct(private EntityManagerInterface $em, private Security $security, ParameterBagInterface $params) {
    }

    public function updateProfile(Request $request): void
    {
        /** @var \App\Entity\User $user */
        $user = $this->security->getUser();
        if (!$user) {
            return;
        }

        $user->setFullName($request->request->get('fullName'));
        $user->setPhone($request->request->get('phone'));
        $user->setAbout($request->request->get('about'));


        $interestIds = $request->request->all('interests');
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
        $avatar = $request->request->get('avatar'); // seed
        $avatarStyle = $request->request->get('avatar_style');

        if ($avatar && $avatarStyle) {
            $user->setAvatar($avatar);
            $user->setAvatarStyle($avatarStyle);
        }

        $this->em->flush();
    }

    public function isProfileComplete(UserInterface $user): bool
    {
        return $user->getFullName() &&
            $user->getPhone() &&
            $user->getAbout() &&
            $user->getSubject1Name() && $user->getSubject1Score() &&
            $user->getSubject2Name() && $user->getSubject2Score() &&
            $user->getSubject3Name() && $user->getSubject3Score() &&
            $user->getSubject4Name() && $user->getSubject4Score();
    }
    public function getAvatarUrl(?string $seed, string $style = 'bottts', int $size = 200): ?string
    {
        if (!$seed) {
            return null;
        }
        return "https://api.dicebear.com/7.x/{$style}/svg?seed=" . urlencode($seed) . "&size={$size}";
    }
    public function getAllInterests(): array
    {
        return $this->em->getRepository(Interest::class)->findAll();
    }
}
