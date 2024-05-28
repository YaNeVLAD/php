<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\UserService;
use App\Service\Data\UserParams;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    
class UserController extends AbstractController
{
    //Переменные, константы и конструктор класса
    private const NOT_NULLABLE_FORM_FIELDS = ['first_name', 'last_name', 'gender', 'birth_date', 'email'];

    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    //Публичные методы
    public function index(): Response
    {
        return $this->render('base/index.html.twig');
    }

    public function showRegisterForm(): Response
    {
        return $this->render('user/register/register_page.html.twig');
    }

    public function errorPage(Request $request): Response
    {
        return $this->render('custom_error/error_page.html.twig', [
            'errorText' => $request->get('errorText'),
            'errorTitle' => $request->get('errorTitle'),
        ]);
    }

    public function viewUser(int $userId): Response
    {
        try {
            $user = $this->userService->getUser($userId);
        } catch (\Exception $e) {
            return $this->redirectToRoute('user_error', [
                'errorTitle' => 'Failed To Show User',
                'errorText' => $e->getMessage(),
            ]);
        }

        return $this->render('user/view/view_page.html.twig', ['user' => $user]);
    }

    public function viewAllUsers(): Response
    {
        try {
            $list = $this->userService->getAllUsers();
        } catch (\Exception $e) {
            return $this->redirectToRoute('user_error', [
                'errorTitle' => 'Failed To Show Users List',
                'errorText' => $e->getMessage(),
            ]);
        }

        return $this->render('user/list/list_page.html.twig', ['users_list' => $list]);
    }

    public function showUpdateForm(int $userId): Response
    {
        try {
            $user = $this->userService->getUser($userId);
        } catch (\Exception $e) {
            return $this->redirectToRoute('user_error', [
                'errorTitle' => 'Failed To Show Update Form',
                'errorText' => $e->getMessage(),
            ]);
        }

        return $this->render('user/update/update_page.html.twig', ['user' => $user]);
    }

    public function removeUser(int $userId): Response
    {
        try {
            $this->userService->deleteUser($userId);
        } catch (\Exception $e) {
            return $this->redirectToRoute('user_error', [
                'errorTitle' => 'Failed To Delete User',
                'errorText' => $e->getMessage(),
            ]);
        }

        return $this->redirectToRoute('users_list', []);
    }

    public function registerUser(Request $request): Response
    {
        try {
            $avatar = $request->files->get('avatar_path');
            $destination = $this->getParameter('kernel.project_dir') . '/public/uploads';

            $userParams = $this->createFromRequest($request);

            $userId = $this->userService->createUser($userParams, $avatar, $destination);
        } catch (\Exception $e) {
            return $this->redirectToRoute('user_error', [
                'errorTitle' => 'Failed To Add User',
                'errorText' => $e->getMessage(),
            ]);
        }

        return $this->redirectToRoute('show_user', ['userId' => $userId]);
    }

    public function updateUser(Request $request, int $userId): Response
    {
        try {
            $newAvatar = $request->files->get('avatar_path');
            $destination = $this->getParameter('kernel.project_dir') . '/public/uploads';

            $userParams = $this->createFromRequest($request);

            $userId = $this->userService->editUser($userParams, $newAvatar, $destination);
        } catch (\Exception $e) {
            return $this->redirectToRoute('user_error', [
                'errorTitle' => 'Failed To Update User',
                'errorText' => $e->getMessage(),
            ]);
        }

        return $this->redirectToRoute('show_user', ['userId' => $userId]);
    }

    //Приватные методы
    private function createFromRequest(Request $request): UserParams
    {
        $userInfo = new UserParams(
            (int) $request->get('userId') ?? null,
            $this->validateData($request, 'first_name'),
            $this->validateData($request, 'last_name'),
            $this->validateData($request, 'middle_name'),
            $this->validateData($request, 'gender'),
            $this->createDate($request, 'birth_date'),
            $this->validateData($request, 'email'),
            $this->validateData($request, 'phone'),
            null,
        );

        return $userInfo;
    }

    private function validateData(Request $request, string $formFieldName): ?string
    {
        $formFieldValue = $request->get($formFieldName);
        if (in_array($formFieldName, self::NOT_NULLABLE_FORM_FIELDS) and $formFieldValue === '') {
            throw new \Exception('Missing User`s ' . $formFieldName);
        } elseif ($formFieldValue === '') {
            return null;
        }

        return $formFieldValue;
    }

    private function createDate(Request $request, string $fieldName): \DateTimeImmutable
    {
        $date = \DateTimeImmutable::createFromFormat('Y-m-d', $request->get($fieldName));
        if ($date === false) {
            throw new \Exception('Invalid Date format. Must be Y-m-d');
        } else {
            return $date;
        }
    }
}