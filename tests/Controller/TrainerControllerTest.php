<?php

namespace App\Test\Controller;

use App\Entity\Trainer;
use App\Repository\TrainerRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TrainerControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private TrainerRepository $repository;
    private string $path = '/trainer/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Trainer::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Trainer index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'trainer[fName]' => 'Testing',
            'trainer[lName]' => 'Testing',
            'trainer[Trainings]' => 'Testing',
        ]);

        self::assertResponseRedirects('/trainer/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Trainer();
        $fixture->setFName('My Title');
        $fixture->setLName('My Title');
        $fixture->setTrainings('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Trainer');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Trainer();
        $fixture->setFName('My Title');
        $fixture->setLName('My Title');
        $fixture->setTrainings('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'trainer[fName]' => 'Something New',
            'trainer[lName]' => 'Something New',
            'trainer[Trainings]' => 'Something New',
        ]);

        self::assertResponseRedirects('/trainer/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getFName());
        self::assertSame('Something New', $fixture[0]->getLName());
        self::assertSame('Something New', $fixture[0]->getTrainings());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Trainer();
        $fixture->setFName('My Title');
        $fixture->setLName('My Title');
        $fixture->setTrainings('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/trainer/');
    }
}
