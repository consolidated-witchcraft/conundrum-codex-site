<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Orders;

use App\Controller\AbstractBaseController;
use App\Security\UserOwnsOrderVoter;
use App\Services\Orders\Domain\Order;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class OrderController extends AbstractBaseController
{
    #[IsGranted(UserOwnsOrderVoter::OPERATION, 'order')]
    #[Route('orders/{orderReference}', name: 'orders.view')]
    public function view(
        Order $order,
        Request $request
    ): Response {
        $pageVars = [
            'pageTitle' => $order->getOrderReference(),
            'order' => $order,
        ];
        return $this->render('/visitor/orders/view.html.twig', $this->populatePageVars(pageVars: $pageVars, request: $request));
    }
}
