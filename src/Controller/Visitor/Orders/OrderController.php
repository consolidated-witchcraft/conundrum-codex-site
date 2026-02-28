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

    #[IsGranted(UserOwnsOrderVoter::OPERATION, 'order')]
    #[Route('orders/{orderReference}/invoice', name: 'orders.invoice')]
    public function invoice(
        Order $order,
    ): Response {
        $invoiceAddresses = [];
        //TODO Inject COnundrum Codex organisation for charges.
        foreach ($order->getProductOrderLines() as $orderLine) {
            $product = $orderLine->getProductVariant()->getProduct();
            $invoiceAddress = $product->getOrganisation()->getInvoiceAddress();
            if (!isset($invoiceAddresses[$invoiceAddress->getId()])) {
                $invoiceAddresses[$invoiceAddress->getId()] = [
                    'organisationName' => $product->getOrganisation()->getName(),
                    'address' => $invoiceAddress
                ];
            }
        }
        $invoiceAddresses = array_unique($invoiceAddresses);
        return $this->render(
            '/printable/invoice.html.twig',
            [
                'documentTitle' => $order->getOrderReference(),
                'invoiceAddresses' => $invoiceAddresses,
                'order' => $order
            ]
        );
    }
}
