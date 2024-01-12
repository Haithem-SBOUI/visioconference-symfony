<?php

namespace App\Test\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/user/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(User::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('User index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'user[username]' => 'Testing',
            'user[firstname]' => 'Testing',
            'user[lastname]' => 'Testing',
            'user[password]' => 'Testing',
            'user[email]' => 'Testing',
            'user[role]' => 'Testing',
            'user[status]' => 'Testing',
            'user[createdOn]' => 'Testing',
            'user[updatedOn]' => 'Testing',
            'user[meetings]' => 'Testing',
        ]);

        self::assertResponseRedirects('/sweet/food/');

        self::assertSame(1, $this->getRepository()->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new User();
        $fixture->setUsername('My Title');
        $fixture->setFirstname('My Title');
        $fixture->setLastname('My Title');
        $fixture->setPassword('My Title');
        $fixture->setEmail('My Title');
        $fixture->setRole('My Title');
        $fixture->setStatus('My Title');
        $fixture->setCreatedOn('My Title');
        $fixture->setUpdatedOn('My Title');
        $fixture->setMeetings('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('User');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new User();
        $fixture->setUsername('Value');
        $fixture->setFirstname('Value');
        $fixture->setLastname('Value');
        $fixture->setPassword('Value');
        $fixture->setEmail('Value');
        $fixture->setRole('Value');
        $fixture->setStatus('Value');
        $fixture->setCreatedOn('Value');
        $fixture->setUpdatedOn('Value');
        $fixture->setMeetings('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'user[username]' => 'Something New',
            'user[firstname]' => 'Something New',
            'user[lastname]' => 'Something New',
            'user[password]' => 'Something New',
            'user[email]' => 'Something New',
            'user[role]' => 'Something New',
            'user[status]' => 'Something New',
            'user[createdOn]' => 'Something New',
            'user[updatedOn]' => 'Something New',
            'user[meetings]' => 'Something New',
        ]);

        self::assertResponseRedirects('/user/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getUsername());
        self::assertSame('Something New', $fixture[0]->getFirstname());
        self::assertSame('Something New', $fixture[0]->getLastname());
        self::assertSame('Something New', $fixture[0]->getPassword());
        self::assertSame('Something New', $fixture[0]->getEmail());
        self::assertSame('Something New', $fixture[0]->getRole());
        self::assertSame('Something New', $fixture[0]->getStatus());
        self::assertSame('Something New', $fixture[0]->getCreatedOn());
        self::assertSame('Something New', $fixture[0]->getUpdatedOn());
        self::assertSame('Something New', $fixture[0]->getMeetings());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new User();
        $fixture->setUsername('Value');
        $fixture->setFirstname('Value');
        $fixture->setLastname('Value');
        $fixture->setPassword('Value');
        $fixture->setEmail('Value');
        $fixture->setRole('Value');
        $fixture->setStatus('Value');
        $fixture->setCreatedOn('Value');
        $fixture->setUpdatedOn('Value');
        $fixture->setMeetings('Value');

        $this->manager->remove($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/user/');
        self::assertSame(0, $this->repository->count([]));
    }
}
