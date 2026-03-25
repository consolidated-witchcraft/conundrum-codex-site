export function rollDice(dieSides: number, dieCount: number): number {

    let total = 0;

    for(let i: number = 0; i < dieCount; i++) {
        total += (Math.floor(Math.random() * dieSides) + 1);
    }

    return total;
}
