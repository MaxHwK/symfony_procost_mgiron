<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use DateTime;

use App\Entity\Job;
use App\Entity\Employee;
use App\Entity\Project;
use App\Entity\WorkingDays;

class AppFixtures extends Fixture
{
    private ObjectManager $manager;

    private const DATA_JOBS = [
        ['Full-Stack Developer'],
        ['Front-End Developer'],
        ['Back-End Developer'],
        ['Web Project Manager'],
        ['SEO Consultant'],
    ];

    private const DATA_EMPLOYEES = [
        ['Maxence', 'GIRON', 'maxence.giron@gmail.com', '1000'],
        ['John', 'DOE', 'john.doe@gmail.com', '900'],
        ['Harry', 'COVER', 'harry.cover@gmail.com', '800'],
        ['Jean', 'REGISTRE', 'jean.registre@gmail.com', '700'],
        ['Alex', 'TERIEUR', 'alex.terieur@gmail.com', '600'],
    ];

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->createJobs();
        $this->createEmployees();
        $this->createProjects();
        $this->createWorkingDays();
        $manager->flush();
    }

    private function createJobs()
    {
        foreach (self::DATA_JOBS as $key => [$name]) {
            $job = (new Job())
                ->setName((string)$name);
            $this->manager->persist($job);
            $this->addReference(Job::class . $key, $job);
        }
    }

    private function createEmployees()
    {
        foreach (self::DATA_EMPLOYEES as $key => $value) {
            $job = $this->getReference(Job::class . random_int(0, 4));
            $employee = (new Employee())
                ->setFirstName((string)$value[0])
                ->setLastName($value[1])
                ->setEmail($value[2])
                ->setDailyCost($value[3])
                ->setHiringDate(new DateTime())
                ->setJob($job);
            $this->manager->persist($employee);
            $this->addReference(Employee::class . $key, $employee);
            sleep(1);
        }
    }

    private function createProjects()
    {
        $DATA_PROJECTS = [
            ['Project 1', 'Here is the description of the Project 1 ...', '10000', new DateTime()],
            ['Project 2', 'Here is the description of the Project 2 ...', '20000', new DateTime()],
            ['Project 3', 'Here is the description of the Project 3 ...', '30000', new DateTime()],
            ['Project 4', 'Here is the description of the Project 4 ...', '40000', new DateTime()],
            ['Project 5', 'Here is the description of the Project 5 ...', '50000', new DateTime()],
            ['Project 6', 'Here is the description of the Project 6 ...', '60000', new DateTime()],
            ['Project 7', 'Here is the description of the Project 7 ...', '70000', new DateTime()],
            ['Project 8', 'Here is the description of the Project 8 ...', '80000', new DateTime()],
            ['Project 9', 'Here is the description of the Project 9 ...', '90000', new DateTime()],
            ['Project 10', 'Here is the description of the Project 10 ...', '100000', new DateTime()],
            ['Project 11', 'Here is the description of the Project 11 ...', '110000', new DateTime()],
            ['Project 12', 'Here is the description of the Project 12 ...', '120000', new DateTime()],
        ];

        foreach ($DATA_PROJECTS as $key => $value) {
            $project = (new Project())
                ->setName((string)$value[0])
                ->setDescription($value[1])
                ->setPrice($value[2])
                ->setCreationDate($value[3]);
            $this->manager->persist($project);
            $this->addReference(Project::class . $key, $project);
            sleep(1);
        }
    }

    private function createWorkingDays()
    {
        $WorkingDays = [
            ['2'], ['4'], ['6'], ['8'], ['10'], ['12'],
            ['1'], ['3'], ['5'], ['7'], ['9'], ['11'],
            ['2'], ['4'], ['6'], ['8'], ['10'], ['12'],
        ];

        foreach ($WorkingDays as $key => [$value]) {
            $project = (new WorkingDays())
                ->setEmployee($this->getReference(Employee::class . random_int(0, 4)))
                ->setProject($this->getReference(Project::class . random_int(0, 3)))
                ->setNbDays($value);
            $this->manager->persist($project);
            $this->addReference(WorkingDays::class . $key, $project);
        }
    }

}
