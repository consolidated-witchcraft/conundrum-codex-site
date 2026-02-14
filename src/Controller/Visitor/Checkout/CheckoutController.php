<?php

namespace App\Controller\Visitor\Checkout;


use App\Controller\AbstractBaseController;
use App\Dto\Visitor\Checkout\UserCartDto;
use App\Form\Visitor\Checkout\UserCartType;
use App\Form\Visitor\DummyPaymentForm;
use App\Services\Orders\Domain\Order;
use App\Services\Orders\Service\Interfaces\OrderServiceInterface;
use App\Services\Payment\Domain\Payment;
use App\Services\Payment\Service\PaymentServiceInterface;
use App\Services\User\Domain\User;
use App\Services\User\Service\Interfaces\UserServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CheckoutController extends AbstractBaseController
{
    public function __construct(
        private readonly UserServiceInterface $userService,
        private readonly OrderServiceInterface $orderService,
        private readonly PaymentServiceInterface $paymentService
    ) {}

    #[Route('checkout', name: 'checkout.checkout')]
    public function checkout(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            $this->addFlash('warning', 'You must be logged in to checkout.');
            return $this->redirectToRoute('app_login');
        }
        $cart = $user->getUserCart();
        $dto = UserCartDTO::makeFromUserCart($cart);
        $form = $this->createForm(UserCartType::class, $dto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $order = $this->orderService->convertCartToOrder($cart);
            return $this->redirectToRoute('payments.pay', ['orderReference' => $order->getOrderReference(), 'paymentReference' => $order->getOrderPayments()->first()->getPaymentReference()]);
        }
        $pageVars = [
            'pageTitle' => 'Review your Order',
            'form' => $form
        ];
        return $this->render('visitor/checkout/review.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
