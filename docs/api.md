# API Bankboost (v1)

Base: `/api/v1`. Respostas em JSON. Valores sempre em **centavos** (`15025` = R$ 150,25).
Rotas autenticadas usam `Authorization: Bearer <token>` (Sanctum).

## Autenticação

| Método | Rota | Corpo |
| --- | --- | --- |
| POST | `/auth/register` | `name`, `email`, `cpf`, `password`, `password_confirmation`, `device_name?` |
| POST | `/auth/login` | `email`, `password`, `device_name` |
| POST | `/auth/logout` | — |

Cadastro e login devolvem `token`. O cadastro já abre a conta (agência `0001`).

## Conta e extrato

| Método | Rota | Observação |
| --- | --- | --- |
| GET | `/account` | saldo, agência, número e titular |
| GET | `/statement` | extrato paginado, do mais recente ao mais antigo |

Filtros do extrato (query string, todos opcionais):

- `type`: `deposit`, `withdrawal`, `transfer_in`, `transfer_out`, `pix_in`, `pix_out` (vários separados por vírgula)
- `direction`: `credit` ou `debit`
- `from`, `to`: datas `AAAA-MM-DD`
- `min_amount`, `max_amount`: centavos
- `search`: busca na descrição e no nome da contraparte
- `per_page`: 1 a 100 (padrão 20)

A resposta traz `summary` com entradas, saídas e saldo líquido do período filtrado.

## Movimentações simuladas

| Método | Rota | Corpo |
| --- | --- | --- |
| POST | `/deposits` | `amount`, `description?` |
| POST | `/withdrawals` | `amount`, `description?` |
| POST | `/transfers` | `agency`, `number`, `amount`, `description?` |
| GET | `/transfers/{id}` | comprovante (só para quem enviou ou recebeu) |

## Pix

| Método | Rota | Corpo |
| --- | --- | --- |
| GET | `/pix/keys` | chaves da conta |
| POST | `/pix/keys` | `type` (`cpf`, `email`, `phone`, `random`), `phone` quando o tipo for celular |
| DELETE | `/pix/keys/{id}` | — |
| GET | `/pix/lookup?key=` | recebedor com CPF mascarado, para confirmar antes de enviar |
| POST | `/pix/transfers` | `key`, `amount`, `description?`, `key_type?` |

CPF e e-mail usam os dados do titular. Limite de 5 chaves por conta.

## Idempotência

`POST /transfers` e `POST /pix/transfers` aceitam o cabeçalho `Idempotency-Key`. Repetir a requisição com a mesma chave devolve a transferência original (`200` e `Idempotent-Replayed: true`) sem debitar de novo. A mesma chave com outro valor ou destino retorna `409`.

## Erros de negócio

Retornam `{ "message": "...", "code": "..." }`:

| code | status |
| --- | --- |
| `insufficient_funds` | 422 |
| `amount_out_of_range` | 422 |
| `daily_limit_exceeded` | 422 |
| `account_blocked` | 422 |
| `same_account` | 422 |
| `pix_key_already_registered` | 422 |
| `pix_key_limit_reached` | 422 |
| `destination_not_found` | 404 |
| `pix_key_not_found` | 404 |
| `idempotency_conflict` | 409 |

Erros de validação seguem o padrão do Laravel (`422` com `errors`).

## Limites (config/banking.php)

| Limite | Padrão |
| --- | --- |
| Depósito por operação | R$ 100.000,00 |
| Saque por operação | R$ 5.000,00 |
| Transferência/Pix por operação | R$ 50.000,00 |
| Total enviado por dia | R$ 20.000,00 |

## Consistência do saldo

Toda movimentação passa pelo `App\Services\Banking\Ledger`, que trava as contas envolvidas (`SELECT ... FOR UPDATE`, sempre na mesma ordem para evitar deadlock) e grava o lançamento com o saldo resultante na mesma transação. Lançamentos são imutáveis. O comando `php artisan bankboost:audit` confere se o saldo de cada conta bate com a soma dos lançamentos.
