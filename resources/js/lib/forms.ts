/**
 * Primeira mensagem de erro entre vários campos. Erros de regra de negócio
 * (conta bloqueada, destino inexistente…) chegam em campos fora do formulário.
 */
export function firstError(errors: object, ...fields: string[]): string | undefined {
    const bag = errors as Record<string, string | undefined>;

    return fields.map((field) => bag[field]).find(Boolean);
}
