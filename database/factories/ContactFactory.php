<?php

namespace Database\Factories;


use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;


class ContactFactory extends Factory
{

    protected $model = Contact::class;


    public function definition()
    {

        return [

            'user_id'=>User::factory(),

            'name'=>$this->faker->name(),

            'phone'=>$this->faker->phoneNumber(),

            'email'=>$this->faker->unique()->safeEmail(),

            'favorite'=>false,

        ];

    }
}