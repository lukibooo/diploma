<?php
// src/Service/ProfileService.php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProfileService
{
    public function __construct(private EntityManagerInterface $em, private Security $security) {}

    public function updateProfile(Request $request): void
    {
        /** @var User $user */
        $user = $this->security->getUser();
        if (!$user) {
            return;
        }

        $user->setFullName($request->request->get('fullName'));
        $user->setPhone($request->request->get('phone'));
        $user->setAbout($request->request->get('about'));

        // Interests
        $interestsInput = $request->request->get('interests', '');
        $interests = array_filter(array_map('trim', explode(',', $interestsInput)));
        $user->clearInterests();
        foreach ($interests as $interestName) {
            $user->addInterestByName($interestName); // припускаємо, що метод реалізований
        }

        // Subjects
        $user->clearSubjects();
        $subjects = $request->request->all('subjects');
        foreach ($subjects as $subject) {
            if (!empty($subject['name']) && !empty($subject['score'])) {
                $user->addSubjectFromArray($subject); // припускаємо, що метод реалізований
            }
        }

        // Avatar
        /** @var UploadedFile|null $avatar */
        $avatar = $request->files->get('avatar');
        if ($avatar) {
            $filename = uniqid() . '.' . $avatar->guessExtension();
            $avatar->move(__DIR__ . '/../../public/uploads/avatars', $filename);
            $user->setAvatar($filename);
        }

        $this->em->persist($user);
        $this->em->flush();
    }
}
