<?php

namespace Tests\Feature;

use Tests\TestCase;

class LandingTest extends TestCase
{
    public function test_a_landing_carrega(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Desperte seu potencial financeiro com o Bankboost!');
    }

    public function test_health_check_responde(): void
    {
        $this->get('/up')->assertOk();
    }
}
