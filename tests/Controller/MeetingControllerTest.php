<?php

namespace App\Test\Controller;

use App\Entity\Meeting;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MeetingControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/meeting/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Meeting::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Meeting index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'meeting[roomId]' => 'Testing',
            'meeting[title]' => 'Testing',
            'meeting[description]' => 'Testing',
            'meeting[dateTime]' => 'Testing',
            'meeting[status]' => 'Testing',
            'meeting[organizer]' => 'Testing',
        ]);

        self::assertResponseRedirects('/sweet/food/');

        self::assertSame(1, $this->getRepository()->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Meeting();
        $fixture->setRoomId('My Title');
        $fixture->setTitle('My Title');
        $fixture->setDescription('My Title');
        $fixture->setDateTime('My Title');
        $fixture->setStatus('My Title');
        $fixture->setOrganizer('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Meeting');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Meeting();
        $fixture->setRoomId('Value');
        $fixture->setTitle('Value');
        $fixture->setDescription('Value');
        $fixture->setDateTime('Value');
        $fixture->setStatus('Value');
        $fixture->setOrganizer('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'meeting[roomId]' => 'Something New',
            'meeting[title]' => 'Something New',
            'meeting[description]' => 'Something New',
            'meeting[dateTime]' => 'Something New',
            'meeting[status]' => 'Something New',
            'meeting[organizer]' => 'Something New',
        ]);

        self::assertResponseRedirects('/meeting/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getRoomId());
        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getDateTime());
        self::assertSame('Something New', $fixture[0]->getStatus());
        self::assertSame('Something New', $fixture[0]->getOrganizer());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Meeting();
        $fixture->setRoomId('Value');
        $fixture->setTitle('Value');
        $fixture->setDescription('Value');
        $fixture->setDateTime('Value');
        $fixture->setStatus('Value');
        $fixture->setOrganizer('Value');

        $this->manager->remove($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/meeting/');
        self::assertSame(0, $this->repository->count([]));
    }
}
