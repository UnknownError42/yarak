<?php

namespace Yarak\Tests\Integration;

use App\Models\Users;
use Yarak\Tests\FactoryTestCase;

class FactoryHelperTest extends FactoryTestCase
{
    /**
     * @test
     */
    public function factory_helper_makes_a_simple_model_instance()
    {
        $user = factory(Users::class)->make();

        $this->assertUserInstanceMade($user);
    }

    /**
     * @test
     */
    public function factory_helper_doesnt_save_models_when_making_them()
    {
        $user = factory(Users::class)->make();

        $this->dontSeeInDatabase('users', [
            'id' => 1,
        ]);
    }

    /**
     * @test
     */
    public function factory_helper_uses_user_defined_attributes_when_making_model()
    {
        $attributes = [
            'username' => 'bobsmith',
            'email' => 'bobsmsith@example.com',
        ];

        $user = factory(Users::class)->make($attributes);

        $this->assertUserHasAttributes($user, $attributes);
    }

    /**
     * @test
     */
    public function factory_helper_makes_multiple_instances_of_models()
    {
        $users = factory(Users::class, 3)->make();

        $this->assertCount(3, $users);

        foreach ($users as $user) {
            $this->assertUserInstanceMade($user);
        }
    }

    /**
     * @test
     */
    public function factory_helper_makes_classes_with_a_given_name()
    {
        $user = factory(Users::class, 'myUser')->make();

        $this->assertInstanceOf(Users::class, $user);

        $this->assertEquals('myUsername', $user->username);
    }

    /**
     * @test
     */
    public function factory_helper_makes_multiple_instances_of_models_with_given_name()
    {
        $users = factory(Users::class, 'myUser', 3)->make();

        $this->assertCount(3, $users);

        foreach ($users as $user) {
            $this->assertInstanceOf(Users::class, $user);

            $this->assertEquals('myUsername', $user->username);
        }
    }

    /**
     * @test
     */
    public function factory_helper_creates_a_single_model_instance()
    {
        $user = factory(Users::class)->create();

        $this->assertUserInstanceCreated($user);
    }

    /**
     * @test
     */
    public function it_uses_user_defined_attributes_when_creating_model()
    {
        $attributes = [
            'username' => 'bobsmith',
            'email' => 'bobsmsith@example.com',
        ];

        $user = factory(Users::class)->create($attributes);

        $this->assertInstanceOf(Users::class, $user);

        $this->seeInDatabase('users', $attributes);
    }

    /**
     * @test
     */
    public function it_creates_multiple_instances_of_models()
    {
        $users = factory(Users::class, 3)->create();

        $this->assertCount(3, $users);

        foreach ($users as $user) {
            $this->assertUserInstanceCreated($user);
        }
    }

    /**
     * @test
     */
    public function it_creates_classes_with_a_given_name()
    {
        $user =factory(Users::class, 'myUser')->create();

        $this->assertInstanceOf(Users::class, $user);

        $this->seeInDatabase('users', [
            'username' => $user->username,
            'email' => $user->email,
        ]);
    }
}