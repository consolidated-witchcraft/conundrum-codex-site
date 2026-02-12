<?php

namespace App\Controller\Visitor\Checkout;


use App\Controller\AbstractBaseController;
use App\Form\Type\UserCartLineType;
use App\Form\Visitor\Checkout\UserCartType;
use App\Services\Orders\Service\Interfaces\OrderServiceInterface;
use App\Services\User\Domain\User;
use App\Services\User\Domain\UserCart\UserCart;
use App\Services\User\Service\Interfaces\UserServiceInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CheckoutController extends AbstractBaseController
{
    public function __construct(
        private readonly UserServiceInterface $userService,
        private readonly OrderServiceInterface $orderService
    ) {}

    #[Route('checkout', name: 'checkout.checkout')]
    public function checkout(Request $request): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            $this->addFlash('warning', 'You must be logged in to checkout.');
            return $this->redirectToRoute('app_login');
        }
        $cart = $user->getUserCart();
        $form = $this->createForm(UserCartType::class, $cart);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $order = $this->orderService->convertCartToOrder($cart);
        }
        $pageVars = [
            'pageTitle' => 'Review your Order',
            'form' => $form
        ];
        return $this->render('visitor/checkout/review.html.twig', $this->populatePageVars($pageVars, $request));
    }

}
