<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Account;

use App\Controller\AbstractBaseController;
use App\Controller\Traits\HandlesFileUploadsTrait;
use App\Dto\Visitor\User\UserChangePasswordDto;
use App\Dto\Visitor\User\UserEditDto;
use App\Form\Visitor\Account\ChangePasswordType;
use App\Form\Visitor\Account\UserEditType;
use App\Services\Files\Service\Interfaces\FilesEntityServiceInterface;
use App\Services\Game\Service\Interfaces\GameEntityServiceInterface;
use App\Services\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CartController extends AbstractBaseController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/cart', name: 'app.user.cart')]
    public function cart(Request $request): Response {
        die("I am the cart");
    }
}
