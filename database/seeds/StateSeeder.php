<?php

use App\State;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $state = new State();
        $state->name = 'Comprobando Pago';
        $state->description = 'Pedido espera comprobación de pago';
        $state->save();

        $state = new State();
        $state->name = 'Elaborando Pedido';
        $state->description = 'El pedido se esta construyendo';
        $state->save();


        $state = new State();
        $state->name = 'Esperando día Entrega';
        $state->description = 'Pedido elaborado, a la espera del día para ser entregado';
        $state->save();

        $state = new State();
        $state->name = 'Problema de Entrega';
        $state->description = 'Pedido tiene problema para el proceso de entrega';
        $state->save();

        $state = new State();
        $state->name = 'En Reparto';
        $state->description = 'Pedido espera comprobación de pago';
        $state->save();

        $state = new State();
        $state->name = 'Completado';
        $state->description = 'Pedido espera comprobación de pago';
        $state->save();
    }
}
