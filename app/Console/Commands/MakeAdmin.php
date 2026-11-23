<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeAdmin extends Command
{
    protected $signature = 'bankboost:make-admin {email : E-mail de um usuário já cadastrado} {--revoke : Remove o acesso de admin}';

    protected $description = 'Dá (ou tira) acesso ao painel administrativo';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('Nenhum usuário com esse e-mail. Cadastre a pessoa pelo site primeiro.');

            return self::FAILURE;
        }

        $user->forceFill(['is_admin' => ! $this->option('revoke')])->save();

        $this->info($this->option('revoke') ? "{$user->email} não é mais admin." : "{$user->email} agora é admin.");

        return self::SUCCESS;
    }
}
