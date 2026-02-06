<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Account;

use App\Controller\AbstractBaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AddressController extends AbstractBaseController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/account/{id}/address/add', name: 'app.user.address.add')]
    public function add(Request $request): Response {

    }
}
