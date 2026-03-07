<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Account;

use App\Controller\AbstractBaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CartController extends AbstractBaseController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/cart', name: 'app.user.cart')]
    public function cart(Request $request): Response {
        die("I am the cart");
    }
}
