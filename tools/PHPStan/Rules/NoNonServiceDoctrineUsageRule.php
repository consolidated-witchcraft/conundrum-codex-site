<?php

declare(strict_types=1);

namespace Tools\PHPStan\Rules;

use App\Services\Core\Service\Interfaces\DomainEntityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;
use PHPStan\Reflection\ClassReflection;
/**
 * To preserve separation of Domain Layers, Doctrine EntityManagers and Repositories are only allowed to be injected within the service layer
 *
 * @template-implements Rule<Node\Stmt\Class_>
 */
class NoNonServiceDoctrineUsageRule implements Rule
{
    private const string BASE_DOCTRINE_REPOSITORY_INTERFACE = ObjectRepository::class;

    private const string BASE_ENTITY_MANAGER_INTERFACE = EntityManagerInterface::class;

    private const string BASE_DOMAIN_SERVICE_INTERFACE = DomainEntityServiceInterface::class;

    private const string ERROR_MESSAGE_DOCTRINE_REPOSITORY_INJECTION = "The constructor parameter $%s is typed as %s or a subclass of it.\n";

    private const string ERROR_MESSAGE_ENTITY_MANAGER_INJECTION = "The constructor parameter $%s is typed as %s or a subclass of it.\n";



    /**
     * @var ReflectionProvider
     */
    private ReflectionProvider $reflectionProvider;

    private ClassReflection $repositoryClassReflection;

    private ClassReflection $entityManagerReflection;

    /**
     * @param ReflectionProvider $reflectionProvider
     */
    public function __construct(ReflectionProvider $reflectionProvider)
    {
        $this->reflectionProvider = $reflectionProvider;
        $this->entityManagerReflection = $this->reflectionProvider->getClass(self::BASE_ENTITY_MANAGER_INTERFACE);
        $this->repositoryClassReflection = $this->reflectionProvider->getClass(self::BASE_DOCTRINE_REPOSITORY_INTERFACE);

    }

    /**
     * @return string
     */
    public function getNodeType(): string
    {
        return Node\Stmt\Class_::class;
    }

    /**
     * @param Node\Stmt\Class_ $node
     * @param Scope $scope
     *
     * @throws ShouldNotHappenException
     *
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (!$node->name instanceof Node\Identifier) {
            return [];
        }

        $className = $scope->getNamespace() . '\\' . $node->name->name;
        $classReflection = $this->reflectionProvider->getClass($className);
        if (
            $classReflection->implementsInterface(self::BASE_DOMAIN_SERVICE_INTERFACE)
            || str_contains($className, '\\Service\\')
        ) {
            return [];
        }
        if (!$classReflection->hasConstructor()) {
            return [];
        }

        $constructorMethod = $classReflection->getConstructor();
        /**
         * @var RuleError[] $errors
         */
        $errors = [];

        foreach ($constructorMethod->getVariants() as $variant) {
            foreach ($variant->getParameters() as $parameterReflection) {
                $parameterType = $parameterReflection->getType();

                if ($parameterType instanceof UnionType) {
                    foreach ($parameterType->getTypes() as $innerType) {
                        if ($this->isDoctrineRepository($innerType)) {
                            $this->addRepositoryInjectionRuleError($parameterReflection->getName(), $errors);

                            continue 2; // Ignore remaining union types if error found for that parameter.
                        } elseif ($this->isDoctrineEntityManager($innerType)) {
                            $this->addEntityManagerInjectionRuleError($parameterReflection->getName(), $errors);

                            continue 2; // Ignore remaining union types if error found for that parameter.
                        }
                    }
                } elseif ($this->isDoctrineRepository($parameterType)) {
                    $this->addRepositoryInjectionRuleError($parameterReflection->getName(), $errors);
                } elseif ($this->isDoctrineEntityManager($parameterType)) {
                    $this->addEntityManagerInjectionRuleError($parameterReflection->getName(), $errors);
                }
            }
        }

        return $errors;
    }

    /**
     * @param string $parameterName
     * @param RuleError[] $errors
     *
     * @throws ShouldNotHappenException
     *
     * @return void
     */
    private function addRepositoryInjectionRuleError(string $parameterName, array &$errors): void
    {
        $errors[] = RuleErrorBuilder::message(sprintf(
            self::ERROR_MESSAGE_DOCTRINE_REPOSITORY_INJECTION,
            $parameterName,
            self::BASE_DOCTRINE_REPOSITORY_INTERFACE
        ))->build();
    }


    /**
     * @param string $parameterName
     * @param RuleError[] $errors
     *
     * @return void
     *@throws ShouldNotHappenException
     *
     */
    private function addEntityManagerInjectionRuleError(string $parameterName, array &$errors): void
    {
        $errors[] = RuleErrorBuilder::message(sprintf(
            self::ERROR_MESSAGE_ENTITY_MANAGER_INJECTION,
            $parameterName,
            self::BASE_ENTITY_MANAGER_INTERFACE
        ))->build();
    }

    /**
     * @param Type $type
     *
     * @return bool
     */
    private function isDoctrineRepository(Type $type): bool
    {
        $repositoryClassReflection = $this->reflectionProvider->getClass(self::BASE_DOCTRINE_REPOSITORY_INTERFACE);
        foreach ($type->getObjectClassNames() as $className) {
            $parameterClassReflection = $this->reflectionProvider->getClass($className);

            if (
                self::BASE_DOCTRINE_REPOSITORY_INTERFACE === $className
                || $parameterClassReflection->isSubclassOfClass($this->repositoryClassReflection)
            ) {
                return true;
            }
        }

        return false;
    }

    private function isDoctrineEntityManager(Type $type): bool {

        foreach ($type->getObjectClassNames() as $className) {
            $parameterClassReflection = $this->reflectionProvider->getClass($className);

            if ($parameterClassReflection->isSubclassOfClass($this->entityManagerReflection)
                || self::BASE_ENTITY_MANAGER_INTERFACE === $className
            ) {
                return true;
            }
        }

        return false;
    }
}
