<?php

namespace Database\Factories;


use App\Models\Export;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;


class ExportFactory extends Factory
{

    protected $model = Export::class;


    public function definition()
    {

        return [

            'user_id' => User::factory(),

            'formato' => 'csv',

            'status' => 'Pendente',

            'name_arquivo' => null,

            'caminho_arquivo' => null

        ];
    }
}
