<?php

namespace Yarak\Tests\Functional;

use Yarak\Yarak;
use Yarak\Tests\TestCase;

class MakeMigrationTest extends TestCase
{
    /**
     * @test
     */
    public function it_makes_basic_migration()
    {
        $this->removeMigrationDirectory();

        $config = $this->getConfig();

        Yarak::call('make:migration', [
            'name'    => 'create_new_table',
        ], $config->getAll());

        $files = scandir($config->getMigrationDirectory());

        $this->assertContains('create_new_table', $files[2]);

        $migrationPath = $config->getMigrationDirectory().$files[2];

        $migrationContents = file_get_contents($migrationPath);

        $this->assertContains('class CreateNewTable', $migrationContents);
    }

    /**
     * @test
     */
    public function it_makes_create_migration()
    {
        $this->removeMigrationDirectory();
        
        $config = $this->getConfig();

        Yarak::call('make:migration', [
            'name'     => 'create_new_table',
            '--create' => 'new',
        ], $config->getAll());

        $files = scandir($config->getMigrationDirectory());

        $this->assertContains('create_new_table', $files[2]);

        $migrationPath = $config->getMigrationDirectory().$files[2];

        $migrationContents = file_get_contents($migrationPath);

        $this->assertContains('class CreateNewTable', $migrationContents);

        $this->assertContains("'new',", $migrationContents);

        $this->assertContains("\$connection->dropTable('new');", $migrationContents);
    }
}