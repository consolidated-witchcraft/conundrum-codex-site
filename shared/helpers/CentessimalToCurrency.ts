export function centessimalToCurrency(
    centessimal: number,
    currencyCode: string = 'GBP'
): string {
    let decimal: number = centessimal/100;
    return decimal.toLocaleString('en-GB', {
        style: 'currency',
        currency: currencyCode,
    });
}
