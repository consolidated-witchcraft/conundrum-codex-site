<?php

declare(strict_types=1);

namespace Tests\ApplicationLayer\Controller\Visitor\Characters;

use App\Services\Character\Domain\Character;
use App\Services\Character\Service\Interfaces\CharacterServiceInterface;
use App\Services\ManagedFiles\Domain\ManagedFile;
use App\Services\ManagedImages\Domain\ManagedImage;
use App\Services\ManagedImages\Domain\Enums\ManagedImageTypeEnum;
use App\Services\User\Domain\Role;
use App\Services\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Tests\ApplicationLayer\API\ApiTestCase;
use Twig\Environment;

final class CharacterControllerTest extends ApiTestCase
{
    private function makeCharacter(User $owner, ?ManagedFile $characterSheet = null): Character
    {
        $portraitFile = new ManagedFile(
            path: '/uploads/images/seraphina-vale.webp',
            mimeType: 'image/webp',
            title: 'Seraphina Vale',
            description: 'Seraphina Vale portrait',
            uploader: $owner,
        );
        $portrait = new ManagedImage(
            description: 'Portrait of Seraphina Vale',
            createdBy: $owner,
            sourceImage: $portraitFile,
            type: ManagedImageTypeEnum::DEFAULT,
        );

        return new Character(
            name: 'Seraphina Vale',
            user: $owner,
            class: 'Wizard',
            race: 'Human',
            portrait: $portrait,
            characterSheet: $characterSheet,
            biography: 'Keeper of the celestial archive.',
            id: 42,
        );
    }

    private function replaceCharacterService(Character $character): void
    {
        $service = $this->createMock(CharacterServiceInterface::class);
        $service->method('find')->with(42)->willReturn($character);
        static::getContainer()->set(CharacterServiceInterface::class, $service);
    }

    public function testAuthenticatedUserCanViewCreateCharacterForm(): void
    {
        static::bootKernel();
        $container = static::getContainer();
        $entityManager = $container->get(EntityManagerInterface::class);
        makeFeatureSwitchers(
            entityManager: $entityManager,
            cache: $container->get(CacheInterface::class),
        );
        $user = makeUser(entityManager: $entityManager);
        static::ensureKernelShutdown();

        $client = $this->makeAuthenticatedClient(user: $user);
        $crawler = $client->request(method: 'GET', uri: '/account/characters/create');

        self::assertResponseIsSuccessful();
        self::assertCount(expectedCount: 1, haystack: $crawler->filter('#create_character_form_name'));
        self::assertCount(expectedCount: 1, haystack: $crawler->filter('#create_character_form_class'));
        self::assertCount(expectedCount: 1, haystack: $crawler->filter('#create_character_form_race'));
        self::assertSame(expected: '1', actual: $crawler->filter('#create_character_form_level')->attr('value'));
        self::assertCount(expectedCount: 1, haystack: $crawler->filter('#create_character_form_portrait'));
        self::assertCount(
            expectedCount: 1,
            haystack: $crawler->filter('#character-portrait-upload #create_character_form_portrait'),
        );
        self::assertCount(expectedCount: 1, haystack: $crawler->filter('#create_character_form_characterSheet'));
        self::assertCount(
            expectedCount: 1,
            haystack: $crawler->filter('#character-sheet-upload #create_character_form_characterSheet'),
        );
        self::assertCount(expectedCount: 1, haystack: $crawler->filter('#create_character_form_biography'));
        self::assertCount(expectedCount: 0, haystack: $crawler->filter('#create_character_form_npc'));
        self::assertCount(expectedCount: 1, haystack: $crawler->filter('#create_character_form_submit'));
    }

    public function testOwnerCanViewEditCharacterForm(): void
    {
        static::bootKernel();
        $container = static::getContainer();
        $entityManager = $container->get(EntityManagerInterface::class);
        makeFeatureSwitchers($entityManager, $container->get(CacheInterface::class));
        $owner = makeUser(entityManager: $entityManager);
        $characterSheet = new ManagedFile(
            path: '/uploads/character-sheet.pdf',
            mimeType: 'application/pdf',
            title: 'Character sheet',
            description: 'Seraphina Vale character sheet',
            uploader: $owner,
        );
        $character = $this->makeCharacter(owner: $owner, characterSheet: $characterSheet);
        static::ensureKernelShutdown();

        $client = $this->makeAuthenticatedClient(user: $owner);
        $this->replaceCharacterService(character: $character);
        $crawler = $client->request(method: 'GET', uri: '/account/characters/42/edit');

        self::assertResponseIsSuccessful();
        self::assertSame(expected: 'Seraphina Vale', actual: $crawler->filter('#edit_character_form_name')->attr('value'));
        self::assertSame(expected: 'Wizard', actual: $crawler->filter('#edit_character_form_class')->attr('value'));
        self::assertSame(expected: 'Human', actual: $crawler->filter('#edit_character_form_race')->attr('value'));
        self::assertSame(expected: '1', actual: $crawler->filter('#edit_character_form_level')->attr('value'));
        self::assertSame(
            expected: 'Replace character sheet',
            actual: trim($crawler->filter('label[for="edit_character_form_characterSheet"]')->text()),
        );
        self::assertSame(
            expected: '/uploads/images/seraphina-vale.webp',
            actual: $crawler
                ->filter('#character-portrait-replacement #character-current-portrait-image')
                ->attr('src'),
        );
        self::assertCount(
            expectedCount: 1,
            haystack: $crawler->filter('#character-portrait-replacement #edit_character_form_portrait'),
        );
        self::assertCount(expectedCount: 1, haystack: $crawler->filter('#edit_character_form_characterSheet'));
        self::assertCount(
            expectedCount: 1,
            haystack: $crawler->filter(
                '#character-sheet-replacement #character-current-sheet-download '
                . '> a[download][href="/uploads/character-sheet.pdf"]',
            ),
        );
        self::assertCount(expectedCount: 0, haystack: $crawler->filter('#edit_character_form_npc'));
    }

    public function testCharacterCardLinksToCharacterSheetWhenPresent(): void
    {
        static::bootKernel();
        $owner = $this->createStub(User::class);
        $characterSheet = new ManagedFile(
            path: '/uploads/character-sheet.pdf',
            mimeType: 'application/pdf',
            title: 'Character sheet',
            description: 'Seraphina Vale character sheet',
            uploader: $owner,
        );
        $character = $this->makeCharacter(owner: $owner, characterSheet: $characterSheet);
        $twig = static::getContainer()->get(Environment::class);
        self::assertInstanceOf(expected: Environment::class, actual: $twig);

        $markup = $twig->render(
            name: 'visitor/character/partials/_characterCard.html.twig',
            context: ['character' => $character],
        );

        self::assertStringContainsString(
            needle: 'href="/uploads/character-sheet.pdf"',
            haystack: $markup,
        );
        self::assertStringContainsString(needle: 'download', haystack: $markup);
        self::assertStringContainsString(needle: 'Download character sheet', haystack: $markup);
    }

    public function testOwnerCanViewDeleteCharacterForm(): void
    {
        static::bootKernel();
        $container = static::getContainer();
        $entityManager = $container->get(EntityManagerInterface::class);
        makeFeatureSwitchers($entityManager, $container->get(CacheInterface::class));
        $owner = makeUser(entityManager: $entityManager);
        $character = $this->makeCharacter(owner: $owner);
        static::ensureKernelShutdown();

        $client = $this->makeAuthenticatedClient(user: $owner);
        $this->replaceCharacterService(character: $character);
        $crawler = $client->request(method: 'GET', uri: '/account/characters/42/delete');

        self::assertResponseIsSuccessful();
        self::assertCount(expectedCount: 1, haystack: $crawler->filter('#delete_character_form_confirmation'));
        self::assertCount(expectedCount: 1, haystack: $crawler->filter('#delete_character_form_submit'));
    }

    public function testAnotherUserCannotEditCharacter(): void
    {
        static::bootKernel();
        $container = static::getContainer();
        $entityManager = $container->get(EntityManagerInterface::class);
        makeFeatureSwitchers($entityManager, $container->get(CacheInterface::class));
        $owner = makeUser(entityManager: $entityManager);
        $otherUser = makeUser(entityManager: $entityManager);
        $character = $this->makeCharacter(owner: $owner);
        static::ensureKernelShutdown();

        $client = $this->makeAuthenticatedClient(user: $otherUser);
        $this->replaceCharacterService(character: $character);
        $client->request(method: 'GET', uri: '/account/characters/42/edit');

        self::assertResponseStatusCodeSame(expectedCode: 403);
    }

    public function testAdministratorCanEditAnotherUsersCharacter(): void
    {
        static::bootKernel();
        $container = static::getContainer();
        $entityManager = $container->get(EntityManagerInterface::class);
        makeFeatureSwitchers($entityManager, $container->get(CacheInterface::class));
        $owner = makeUser(entityManager: $entityManager);
        $administrator = makeUser(entityManager: $entityManager);
        $adminRole = $entityManager->getRepository(Role::class)->findOneBy(['handle' => 'ROLE_ADMIN']);
        if (!$adminRole instanceof Role) {
            $adminRole = new Role(label: 'Administrator', handle: 'ROLE_ADMIN');
        }
        $administrator->addRole(role: $adminRole);
        $entityManager->persist($administrator);
        $entityManager->flush();
        $character = $this->makeCharacter(owner: $owner);
        static::ensureKernelShutdown();

        $client = $this->makeAuthenticatedClient(user: $administrator);
        $this->replaceCharacterService(character: $character);
        $client->request(method: 'GET', uri: '/account/characters/42/edit');

        self::assertResponseIsSuccessful();
    }
}
