<?php
declare(strict_types=1);

//ВЕЗДЕ ГДЕ СЕЙЧАС ЕСТЬ userId ПОЛУЧАТЬ ЕГО ИЗ СЕССИИ!!!                        
//В BasketRepository СДЕЛАТЬ МЕТОД findByOrder
//ВЫЗЫВАТЬ ЕГО ПРИ МЕТОДЕ removeFromBasket
//СДЕЛАТЬ ИЗМЕНЕНИЕ userId В СЕССИИ ПРИ ПЕРЕХОДЕ НА listByCategorie
//С РУТА show_user 

namespace App\Controller;

use App\Constants\AppConstants;
use App\Service\Data\BasketData;
use App\Service\UserServiceInterface;
use App\Service\OrderServiceInterface;
use App\Service\BasketService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StoreFrontController extends AbstractController
{
    //Переменные, константы и конструктор класса
    private $userService;

    private $orderService;

    private $basketService;

    public function __construct(OrderServiceInterface $orderService, UserServiceInterface $userService, BasketService $basketService)
    {
        $this->basketService = $basketService;
        $this->orderService = $orderService;
        $this->userService = $userService;
    }

    //Публичные методы
    public function index(SessionInterface $session): Response
    {
        $session->set(AppConstants::USER_SESSION_NAME, AppConstants::UNAUTHORIZED_ID);

        return $this->redirectToRoute('list_order', [
            'category' => AppConstants::BASE_CATEGORY,
        ]);
    }

    public function showCreateForm(string $currCategory): Response
    {
        return $this->render('store/order/create/create_page.html.twig', [
            'currCategory' => $currCategory,
            'categories' => AppConstants::EXISTING_CATEGORIES,
        ]);
    }

    public function showUpdateForm(): Response
    {
        return $this->render('store/order/update/update_page.html.twig', [
            'categories' => AppConstants::EXISTING_CATEGORIES,
        ]);
    }

    public function errorPage(Request $request): Response
    {
        return $this->render('custom_error/error_page.html.twig', [
            'errorTitle' => $request->get('errorTitle'),
            'errorText' => $request->get('errorText'),
        ]);
    }

    public function listByCategory(Request $request, SessionInterface $session): Response
    {
        $category = $request->get('category');
        $userId = $session->get(AppConstants::USER_SESSION_NAME, AppConstants::UNAUTHORIZED_ID);
        
        if (!array_key_exists($category, AppConstants::EXISTING_CATEGORIES)) {
            return $this->redirectToRoute('error_store', [
                'errorTitle' => 'Wrong Category Error',
                'errorText' => 'This category doesn\'t exist',
            ]);
        }
        $orders = $this->orderService->findAllInCategory($category);

        return $this->render(
            'store/order/list/list_page.html.twig',
            [
                'userId' => $userId,
                'category' => $category,
                'orders' => $orders,
                'categories' => AppConstants::EXISTING_CATEGORIES,
            ]
        );
    }

    public function showOrder(int $orderId): Response
    {
        try {
            $order = $this->orderService->find($orderId);
        } catch (\Exception $e) {
            return $this->redirectToRoute('error_store', [
                'errorTitle' => 'Show Order Error',
                'errorText' => $e->getMessage(),
            ]);
        }

        return $this->render('store/order/view/view_page.html.twig', ['order' => $order]);
    }

    public function addToBasket(int $orderId, SessionInterface $session): Response
    {
        try {
        $user = $this->userService->getUser($session->get(AppConstants::USER_SESSION_NAME, AppConstants::UNAUTHORIZED_ID));
        $order = $this->orderService->find($orderId);
        
        $this->basketService->add($user, $order);

        } catch (\Exception $e) {
            return $this->redirectToRoute('register_user_form');
        }

        return $this->redirectToRoute('list_order', [
            'category' => $order->getCategorie()
        ]);
    }

    public function showBasket(SessionInterface $session): Response
    {
        try {
        $userId = $session->get(AppConstants::USER_SESSION_NAME, AppConstants::UNAUTHORIZED_ID);
        $orders = $this->basketService->show($userId);
        } catch (\Exception $e) {
            return $this->redirectToRoute('register_user_form');
        }

        return $this->render('store/basket/basket_page.html.twig', [
            'basket' => $orders,
        ]);
    }

    public function removeFromBasket(int $orderId, SessionInterface $session): Response
    {
        $order = $this->orderService->find($orderId);
        $userId = $session->get(AppConstants::USER_SESSION_NAME, AppConstants::UNAUTHORIZED_ID);
        
        $user = $this->userService->getUser($userId);
        $this->basketService->remove($user, $order);

        return $this->redirectToRoute('basket_order_form');
    }
}