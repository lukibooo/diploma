<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Interest;
use App\Entity\News;
use App\Entity\Specialties;
use App\Entity\Subject;
use App\Entity\University;
use App\Service\AuthService;
use App\Service\FilterService;
use App\Service\ProfileService;
use App\Service\RandomNewsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class MainController extends AbstractController
{
    #[Route('/')]
    public function index(): Response
    {
        return $this->redirectToRoute('main');
    }

    #[Route('/main', name: 'main')]
    public function main(AuthService $authService, SessionInterface $session, RandomNewsService $randomNewsService): Response
    {
        $currentUser = $authService->getCurrentUser($session);
        $newsList = $randomNewsService->getRandomNews(4);

        return $this->render('main.html.twig', [
            'currentUser' => $currentUser,
            'newsList' => $newsList,
        ]);
    }
    #[Route('/news/{id}', name: 'show.news')]
    public function show(News $news, Request $request): Response
    {
        $imageOptions = ['img1.jpg', 'img2.png', 'img3.jpg', 'img4.png'];
        $imageName = $imageOptions[array_rand($imageOptions)];
        $referer = $request->headers->get('referer');
        return $this->render('show.news.html.twig', [
            'news' => $news,
            'imageName' => $imageName,
            'backUrl' => $referer ?? $this->generateUrl('main'),
            'refSource' => $referer,
        ]);
    }
    #[Route('/about', name: 'about')]
    public function about(AuthService $authService, SessionInterface $session): Response
    {

        $currentUser = $authService->getCurrentUser($session);
        return $this->render('about.html.twig', [
            'currentUser' => $currentUser,
        ]);
    }

    #[Route('/search', name: 'search', methods: ['GET', 'POST'])]
    public function search(Request $request, FilterService $filterService, EntityManagerInterface $entityManager, AuthService $authService, SessionInterface $session): Response
    {
        $subjects = $entityManager->getRepository(Subject::class)->findAll();
        $subjects = array_map(fn(Subject $subject) => $subject->getName(), $subjects);
        $cities = $entityManager->getRepository(City::class)->findAll();
        $cities = array_map(fn(City $city) => $city->getName(), $cities);
        $specialties = $entityManager->getRepository(Specialties::class)->findAll();
        $specialties = array_map(fn(Specialties $specialties) => $specialties->getName(), $specialties);
        $city = $request->get('city') ?: null;
        $specialtyName = $request->get('specialty') ?: null;
        $priceFrom = $request->get('price_from') ? (int) $request->get('price_from') : null;
        $priceTo = $request->get('price_to') ? (int) $request->get('price_to') : null;
        $military = $request->request->filter('military', false, FILTER_VALIDATE_BOOLEAN);

        $selectedSubjects = [];
        $userSubjects = [];

        for ($i = 1; $i <= 4; $i++) {
            $subject = $request->request->get("subject$i");
            $score = $request->request->get("score$i");

            if ($subject && $score) {
                $selectedSubjects[] = $subject;
                $userSubjects[] = [
                    'subject' => $subject,
                    'score' => (float) $score,
                ];
            }
        }

        $universities = null;

        if ($request->isMethod('POST') && !empty($userSubjects)) {

            $universities = $filterService->getFilteredResults(
                $userSubjects,
                $city,
                $specialtyName,
                $priceFrom,
                $priceTo,
                $military
            );
        }
        $currentUser = $authService->getCurrentUser($session);
        return $this->render('search.html.twig', [
            'universities' => $universities,
            'subjects' => $subjects,
            'cities' => $cities,
            'specialties' => $specialties,
            'selectedSubjects' => $selectedSubjects,
            'userSubjects' => $userSubjects,
            'selectedCity' => $city,
            'selectedSpecialty' => $specialtyName,
            'priceFrom' => $priceFrom,
            'priceTo' => $priceTo,
            'military' => $military,
            'filtersApplied' => $request->isMethod('POST'),
            'currentUser' => $currentUser,
        ]);
    }
    #[Route('/university/{id}', name: 'university_detail')]
    public function universityDetail(University $university, Request $request): Response {

        $selectedSpecialtyId = $request->query->get('specialty');
        $specialties = $university->getSpecialties();
        $subjectString = $request->query->get('subjects', '');
        $subjectNames = $subjectString ? explode(',', $subjectString) : [];

        $selectedSpecialty = null;
        $otherSpecialties = [];
        $scoresRaw = $request->query->get('scores', '');
        $userScores = [];

        foreach (explode(',', $scoresRaw) as $entry) {
            if (str_contains($entry, ':')) {
                [$subject, $score] = explode(':', $entry);
                $userScores[trim(mb_strtolower($subject))] = (int) $score;
            }
        }
        foreach ($specialties as $specialty) {
            if ($specialty->getId() == $selectedSpecialtyId) {
                $selectedSpecialty = $specialty;
            } else {
                $otherSpecialties[] = $specialty;
            }
        }
        $backUrl = $this->generateUrl('search', array_filter($request->query->all(), fn($key) => !in_array($key, ['id', 'specialty']), ARRAY_FILTER_USE_KEY));
        return $this->render('university_detail.html.twig', [
            'university' => $university,
            'selectedSpecialty' => $selectedSpecialty,
            'otherSpecialties' => $otherSpecialties,
            'userSubjects' => $subjectNames,
            'userScores' => $userScores,
            'backUrl' => $backUrl
        ]);
    }
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        $session = $request->getSession();
        $referer = $request->headers->get('referer');

        if ($referer && !str_contains($referer, '/login')) {
            $session->set('_security.main.target_path', $referer);
        }
        return $this->render('auth.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Symfony обробляє logout автоматично — тут нічого не потрібно
    }

    #[Route('/register', name: 'register')]
    public function register(Request $request, AuthService $authService): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $password = $request->request->get('password');

            if ($email && $password) {
                $authService->register($email, $password);
                return $this->redirectToRoute('profile');
            }

            $this->addFlash('error', 'Email або пароль не передано.');
        }

        return $this->render('registr.html.twig');
    }

    #[Route('/profile', name: 'profile')]
    #[IsGranted('ROLE_USER')]
    public function profile(Request $request, Security $security, EntityManagerInterface $em, ProfileService $profileService): Response
    {
        $user = $security->getUser();
        $mustEdit = empty($user->getFullName()) || empty($user->getPhone()) || empty($user->getAbout());
        $manualEdit = $request->query->get('edit') === '1';
        $editMode = $mustEdit || $manualEdit;
        $requestedEdit = $request->query->get('edit');
        if ($requestedEdit !== null) {
            $editMode = (bool) $requestedEdit;
        } else {
            $editMode = !$profileService->isProfileComplete($user);
        }

        $allInterests = $em->getRepository(Interest::class)->findAll();

        return $this->render('profile.html.twig', [
            'user' => $user,
            'editMode' => $editMode,
            'showAlert' => !$profileService->isProfileComplete($user),
            'allInterests' => $allInterests
        ]);
    }

    #[Route('/profile/save', name: 'profile_save', methods: ['POST'])]
    public function saveProfile(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        /** @var \App\Entity\User $user */
        $user = $security->getUser();

        $user->setFullName($request->get('full_name'));
        $user->setPhone($request->get('phone'));
        $user->setAbout($request->get('about'));

        // Фото
        $photoFile = $request->files->get('photo');
        if ($photoFile) {
            $filename = uniqid() . '.' . $photoFile->guessExtension();
            $photoFile->move('uploads/photos', $filename);
            $user->setAvatar('uploads/photos/' . $filename);
        }

        // Інтереси
        $interestIds = $request->get('interests', []);
        $interests = $em->getRepository(Interest::class)->findBy(['id' => $interestIds]);

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



        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('profile',  ['edit' => 0]);
    }
    #[Route('/account/delete', name: 'delete_account', methods: ['POST'])]
    public function deleteAccount(EntityManagerInterface $em, Security $security): Response
    {
        $user = $security->getUser();

        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('register');
    }
    #[Route('/news', name: 'news')]
    public function news(AuthService $authService, SessionInterface $session, RandomNewsService $randomNewsService): Response
    {
        $currentUser = $authService->getCurrentUser($session);
        $newsList = $randomNewsService->getRandomNews(6);
        return $this->render('news.html.twig', [
            'currentUser' => $currentUser,
            'newsList' => $newsList
        ]);
    }

    #[Route('/autocomplete/cities', name: 'autocomplete_cities')]
    public function autocompleteCities(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $query = $request->query->get('q');
        $results = $em->getRepository(City::class)
            ->createQueryBuilder('c')
            ->where('LOWER(c.name) LIKE :q')
            ->setParameter('q', mb_strtolower($query) . '%')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

        $names = array_map(fn(City $city) => $city->getName(), $results);
        return $this->json($names);
    }

    #[Route('/autocomplete/specialties', name: 'autocomplete_specialties')]
    public function autocompleteSpecialties(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $query = $request->query->get('q');
        $results = $em->createQueryBuilder()
            ->select('DISTINCT s.name')
            ->from(Specialties::class, 's')
            ->where('LOWER(s.name) LIKE :q')
            ->setParameter('q', mb_strtolower($query) . '%')
            ->setMaxResults(20)
            ->getQuery()
            ->getScalarResult();

        $names = array_column($results, 'name');

        return $this->json($names);
    }
    #[Route('/subjects/autocomplete', name: 'subject_autocomplete')]
    public function autocompleteSubject(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $query = $request->query->get('q');
        $results = $em->createQueryBuilder('s')
            ->select('DISTINCT s.name')
            ->from(Subject::class, 's')
            ->where('LOWER(s.name) LIKE :q')
            ->setParameter('q', mb_strtolower($query) . '%')
            ->setMaxResults(20)
            ->getQuery()
            ->getScalarResult();

        $names = array_column($results, 'name');

        return $this->json($names);
    }
    #[Route('/interests/autocomplete', name: 'interests_autocomplete')]
    public function autocompleteInterest(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $query = $request->query->get('q');
        $results = $em->createQueryBuilder('s')
            ->select('DISTINCT s.name')
            ->from(Interest::class, 's')
            ->where('LOWER(s.name) LIKE :q')
            ->setParameter('q', mb_strtolower($query) . '%')
            ->setMaxResults(20)
            ->getQuery()
            ->getScalarResult();

        $names = array_column($results, 'name');

        return $this->json($names);
    }
}
