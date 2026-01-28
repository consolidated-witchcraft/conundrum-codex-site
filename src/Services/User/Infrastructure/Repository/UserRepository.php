<?php

namespace App\Services\User\Infrastructure\Repository;

use App\Services\User\Domain\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     * @param PasswordAuthenticatedUserInterface $user
     * @param non-empty-string $newHashedPassword
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @return User[]
     */
    public function findAllPaginated(int $firstResult, int $maxResults = 50): iterable {
        $qb = $this->createQueryBuilder('u')
            ->setFirstResult($firstResult)
            ->setMaxResults($maxResults)
            ->orderBy('u.createdAt', 'ASC');

        return new Paginator($qb->getQuery());
    }

    /**
     * @return User[]
     */
    public function searchByEmailOrUserName(string $searchTerms, int $firstResult, int $maxResults = 50): iterable {
        $qb = $this->createQueryBuilder('u');
            $qb->setFirstResult($firstResult)
            ->setMaxResults($maxResults)
            ->orderBy('u.createdAt', 'ASC');

        $terms = explode(' ', $searchTerms);

        for ($i = 0; $i < count($terms); $i++) {
            $qb->orWhere($qb->expr()->like('u.username', ':term'.$i));
            $qb->orWhere($qb->expr()->like('u.email', ':term'.$i));
            //TODO - Fix this, then think about what you did.
            $qb->setParameter(sprintf('term%d', $i), '%'.$terms[$i].'%');
        }
        return new Paginator($qb->getQuery());
    }
    public function getUsersCount(): int {
        $result = $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
        return (int) $result;
    }

    /**
     * @return User[]
     */
    public function findUsersWithoutAccountType(): iterable {
        $qb = $this->createQueryBuilder('u')
            ->select('u')
            ->where('u.userAccountType IS NULL');
        return $qb->getQuery()->getResult();
    }
}
