<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Account;

use App\Controller\AbstractBaseController;
use App\Dto\Visitor\User\PostalAddressDto;
use App\Form\Visitor\Account\Address\DeletePostalAddressForm;
use App\Form\Visitor\Account\Address\PostalAddressForm;
use App\Security\PostalAddressVoter;
use App\Services\PostalAddress\Domain\PostalAddress;
use App\Services\PostalAddress\Service\PostalAddressServiceInterface;
use App\Services\User\Domain\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PostalAddressController extends AbstractBaseController
{
    public function __construct(
        private ValidatorInterface $validator,
        private readonly PostalAddressServiceInterface $postalAddressService
    ) {}

    #[IsGranted('ROLE_USER')]
    #[Route('/account/address/add', name: 'app.user.address.add')]
    public function add(Request $request): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedHttpException();
        }
        $dto = new PostalAddressDto(user: $user);
        $form = $this->createForm(PostalAddressForm::class, $dto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $postalAddress = PostalAddress::makeFromDto($dto);
            $success = true;
            $violations = $this->validator->validate($postalAddress);
            if ($violations->count()) {
                $success = false;
                foreach ($violations as $violation) {
                    $this->addFlash('error', $violation->getMessage());
                }
            }
            if ($success) {
                $this->postalAddressService->save($postalAddress);
                $this->addFlash('success', 'Address added successfully.');
                return $this->redirectToRoute('app.user.account');
            }
        }
        $pageVars = [
            'pageTitle' => "Create a new address",
            'form' => $form
        ];
        return $this->render('/visitor/account/postalAddress/add.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[IsGranted('ROLE_USER')]
    #[IsGranted(PostalAddressVoter::OWNS_ADDRESS, 'postalAddress')]
    #[Route('/account/address/{postalAddressId}/edit', name: 'app.user.address.edit')]
    public function edit(
        PostalAddress $postalAddress,
        Request $request,
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedHttpException();
        }
        $dto = PostalAddressDto::makeFromAddress($postalAddress);
        $form = $this->createForm(PostalAddressForm::class, $dto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $postalAddress->setLabel($dto->postalAddress->label);
            $postalAddress->setAddressLineOne($dto->postalAddress->addressLineOne);
            $postalAddress->setAddressLineTwo($dto->postalAddress->addressLineTwo);
            $postalAddress->setTownCity($dto->postalAddress->townCity);
            $postalAddress->setCountryCode($dto->postalAddress->countryCode);
            $postalAddress->setCountyProvince($dto->postalAddress->countyProvince);
            $postalAddress->setCountryCode($dto->postalAddress->countryCode);
            $postalAddress->setPostalZip($dto->postalAddress->postalZip);
            $postalAddress->setDefaultBillingAddress($dto->postalAddress->isDefaultBillingAddress);
            $postalAddress->setDefaultShippingAddress($dto->postalAddress->isDefaultShippingAddress);
            $success = true;
            $violations = $this->validator->validate($postalAddress);
            if ($violations->count()) {
                $success = false;
                foreach ($violations as $violation) {
                    $this->addFlash('error', $violation->getMessage());
                }
            }
            if ($success) {
                $this->postalAddressService->save($postalAddress);
                $this->addFlash('success', 'Address updated successfully.');
                return $this->redirectToRoute('app.user.account');
            }
        }
        $pageVars = [
            'pageTitle' => sprintf("Edit address '%s'", $postalAddress->getLabel()),
            'form' => $form
        ];
        return $this->render('/visitor/account/postalAddress/edit.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[IsGranted('ROLE_USER')]
    #[IsGranted(PostalAddressVoter::OWNS_ADDRESS, 'postalAddress')]
    #[Route('/account/address/{postalAddressId}/delete', name: 'app.user.address.delete')]
    public function delete(
        PostalAddress $postalAddress,
        Request $request,
    ): Response {
        $form = $this->createForm(DeletePostalAddressForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->postalAddressService->delete($postalAddress);
            $this->addFlash('success', 'Address deleted successfully.');
            return $this->redirectToRoute('app.user.account');
        }
        $pageVars = [
            'pageTitle' => sprintf("Delete address %s?", $postalAddress->getLabel()),
            'form' => $form
        ];
        return $this->render('/visitor/account/postalAddress/edit.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
