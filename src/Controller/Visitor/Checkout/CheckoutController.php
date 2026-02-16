<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Checkout;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Controller\AbstractBaseController;
use App\Dto\Visitor\Checkout\DeliveryInformationDto;
use App\Dto\Visitor\Checkout\UserCartDto;
use App\Form\Visitor\Checkout\DeliveryInformationType;
use App\Form\Visitor\Checkout\UserCartType;
use App\Services\Orders\Service\Interfaces\OrderServiceInterface;
use App\Services\PostalAddress\Domain\PostalAddress;
use App\Services\PostalAddress\Service\PostalAddressServiceInterface;
use App\Services\User\Domain\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CheckoutController extends AbstractBaseController
{
    public function __construct(
        private readonly OrderServiceInterface $orderService,
        private readonly PostalAddressServiceInterface $postalAddressService,
        private readonly ValidatorInterface $validator,
    ) {}

    #[Route('checkout/delivery', name: 'checkout.delivery')]
    public function deliveryInformation(Request $request): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }
        $dto = DeliveryInformationDto::makeFromUser($user);
        $form = $this->createForm(DeliveryInformationType::class, $dto);
        $form->handleRequest($request);
        $success = true;

        if ($form->isSubmitted() && $form->isValid()) {
            $deliveryAddressId = $dto->deliveryAddress->id;
            if ($deliveryAddressId !== null) {
                $deliveryAddress = $this->postalAddressService->find($deliveryAddressId);
                $deliveryAddress->setLabel($dto->deliveryAddress->label);
                $deliveryAddress->setAddressLineOne($dto->deliveryAddress->addressLineOne);
                $deliveryAddress->setAddressLineTwo($dto->deliveryAddress->addressLineTwo);
                $deliveryAddress->setTownCity($dto->deliveryAddress->townCity);
                $deliveryAddress->setCountryCode($dto->deliveryAddress->countryCode);
                $deliveryAddress->setCountyProvince($dto->deliveryAddress->countyProvince);
                $deliveryAddress->setCountryCode($dto->deliveryAddress->countryCode);
                $deliveryAddress->setPostalZip($dto->deliveryAddress->postalZip);
                $deliveryAddress->setDefaultBillingAddress($dto->deliveryAddress->isDefaultBillingAddress);
                $deliveryAddress->setDefaultShippingAddress($dto->deliveryAddress->isDefaultShippingAddress);
                $violations = $this->validator->validate($deliveryAddress);
                if ($violations->count() > 0) {
                    $success = false;
                    foreach ($violations as $violation) {
                        $this->addFlash('error', $violation->getMessage());
                    }
                }

            } else {
                $deliveryAddress = PostalAddress::makeFromPostalAddressFieldDto($user, $dto->deliveryAddress);
            }

            $billingAddressId = $dto->billingAddress->id;
            if ($billingAddressId !== null) {
                $billingAddress = $this->postalAddressService->find($billingAddressId);
                $billingAddress->setLabel($dto->billingAddress->label);
                $billingAddress->setAddressLineOne($dto->billingAddress->addressLineOne);
                $billingAddress->setAddressLineTwo($dto->billingAddress->addressLineTwo);
                $billingAddress->setTownCity($dto->billingAddress->townCity);
                $billingAddress->setCountryCode($dto->billingAddress->countryCode);
                $billingAddress->setCountyProvince($dto->billingAddress->countyProvince);
                $billingAddress->setCountryCode($dto->billingAddress->countryCode);
                $billingAddress->setPostalZip($dto->billingAddress->postalZip);
                $billingAddress->setDefaultBillingAddress($dto->billingAddress->isDefaultBillingAddress);
                $billingAddress->setDefaultShippingAddress($dto->billingAddress->isDefaultShippingAddress);
                $violations = $this->validator->validate($billingAddress);
                if ($violations->count() > 0) {
                    $success = false;
                    foreach ($violations as $violation) {
                        $this->addFlash('error', $violation->getMessage());
                    }
                }
            } else {
                $deliveryAddress = PostalAddress::makeFromPostalAddressFieldDto($user, $dto->deliveryAddress);
            }
            if ($success) {
                $this->postalAddressService->save($deliveryAddress, false);
                $this->postalAddressService->save($deliveryAddress, false);
            }
            $this->postalAddressService->flush();


        }
        $pageVars = [
            'pageTitle' => 'Set your Delivery Information',
            'cart' => $user->getUserCart(),
            'form' => $form,
        ];
        return $this->render('visitor/checkout/delivery-information.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('checkout/review', name: 'checkout.review')]
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
            return $this->redirectToRoute('payments.pay', ['orderReference' => $order->getId(), 'paymentReference' => $order->getOrderPayments()->first()->getPaymentReference()]);
        }
        $pageVars = [
            'pageTitle' => 'Review your Order',
            'form' => $form
        ];
        return $this->render('visitor/checkout/review.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
