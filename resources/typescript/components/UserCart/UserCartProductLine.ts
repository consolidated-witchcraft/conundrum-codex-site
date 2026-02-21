import createClient, {Client} from "openapi-fetch";
import type { paths } from "../../schema/schema";
import {centessimalToCurrency} from "../../helpers/CentessimalToCurrency";
export class UserCartProductLine extends HTMLDivElement {


    private client: Client<paths>;

    private cartId: string|undefined;

    private userCartProductLineId: string|undefined;

    private totalElement: HTMLSpanElement|undefined;

    constructor() {
        super();
        let env: string  = 'process.env.APP_ENV';
        let apiBaseUrl = (env == '"dev"') ? 'http://localhost:8089' : 'https://conundrumcodex.com';
        this.client = createClient<paths>({ baseUrl: apiBaseUrl });
    }

    connectedCallback() {
        let iri: string|undefined = this.dataset.iri;
        if (iri === undefined || iri === null) {
            throw new Error('Missing iri');
        }
        this.setAttribute('is', 'user-cart-product-line');
        let iriParts: string[] = iri.split('/');
        let cartId: string|undefined = iriParts[3];
        let userCartProductLineId: string|undefined = iriParts[5];

        if (cartId === undefined) {
            throw new Error('Missing cartId');
        }
        if (userCartProductLineId === undefined) {
            throw new Error('Missing userCartProductLineId');
        }

        this.cartId = cartId;
        this.userCartProductLineId = userCartProductLineId;

        this.buildProductLineItem();
    }

    private async buildProductLineItem(): Promise<void> {
        if (this.cartId === undefined) {
            throw new Error('Missing cartId in cart');
        }
        if (this.userCartProductLineId === undefined) {
            throw new Error('Missing userCartProductLineId');
        }

        let productLineDl: HTMLDListElement = document.createElement('dl');
        productLineDl.classList.add('cart-product-line-item');
        const {data, error} = await this.client.GET(
            '/api/cart/{cartId}/productLines/{productLineId}',
            {
                params: {
                    path: {
                        cartId: this.cartId,
                        productLineId: this.userCartProductLineId,
                    },
                },
            });
        if (error !== undefined) {
            throw error;
        }
        if (data !== undefined) {
            if (data.productVariantName !== undefined) {
                this.generateDtDdPair('Name', 'product-name', data.productVariantName, productLineDl);
            }
            if (data.productVariantSku !== undefined) {
                this.generateDtDdPair('SKU', 'product-sku', data.productVariantSku, productLineDl);
            }
            if (data.productVariantPriceCentessimal !== undefined) {
                this.generateDtDdPair(
                    'Price Each',
                    'product-ppu',
                    centessimalToCurrency(data.productVariantPriceCentessimal),
                    productLineDl
                );
            }

            if (data.quantity !== undefined && data.productVariantSku !== undefined) {
                let productVariantSku: string = data.productVariantSku;
                let quantity = data.quantity;
                let quantityDt = document.createElement('dt');
                quantityDt.classList.add('product-qty');
                quantityDt.textContent = 'Quantity';

                let quantityDd = document.createElement('dd');
                quantityDd.classList.add('product-qty');

                let quantityElement = document.createElement('input');
                quantityElement.setAttribute('type', 'number');
                quantityElement.setAttribute('min', '0');
                quantityElement.setAttribute('max', '100');
                quantityElement.setAttribute('step', '1');
                quantityElement.setAttribute('name', `${data.productVariantSku}['qty']`);
                quantityElement.value = quantity.toString();
                quantityElement.addEventListener('change', async (event) => {
                    event.preventDefault();
                    this.patchProductLine(
                        productVariantSku,
                        parseInt(quantityElement.value),
                    );

                })

                quantityDd.appendChild(quantityElement);
                productLineDl.appendChild(quantityDt);
                productLineDl.appendChild(quantityDd);

                let productLineRemoveDt: HTMLElement = document.createElement('dt');
                productLineRemoveDt.classList.add('product-remove');
                productLineRemoveDt.textContent = 'Remove';

                let productLineRemoveDd:HTMLElement = document.createElement('dt');
                productLineRemoveDd.classList.add('product-remove');

                let productLineRemoveButton: HTMLButtonElement = document.createElement('button');
                productLineRemoveButton.setAttribute('type', 'button');
                productLineRemoveButton.textContent = 'Remove';
                productLineRemoveButton.addEventListener('click', (event: MouseEvent) => {
                    event.preventDefault();
                    this.deleteProductLine();
                })

                productLineRemoveDd.appendChild(productLineRemoveButton);

                productLineDl.appendChild(productLineRemoveDt);
                productLineDl.appendChild(productLineRemoveDd);

            }

            if (data.lineTotalCentessimal !== undefined) {
                let lineDt: HTMLElement = document.createElement('dt');
                lineDt.classList.add('product-line-total');
                lineDt.innerHTML = 'Total';

                let lineDd: HTMLElement = document.createElement('dd');
                lineDd.classList.add('product-line-total');
                lineDd.innerHTML = centessimalToCurrency(data.lineTotalCentessimal);
                productLineDl.appendChild(lineDd);
                productLineDl.appendChild(lineDt);
                this.totalElement = lineDd;
            }

        }
        this.appendChild(productLineDl);
    }

    private generateDtDdPair(
        name: string,
        classString: string,
        value: string,
        list: HTMLDListElement
    ): void {
        let lineDt: HTMLElement = document.createElement('dt');
        lineDt.classList.add(classString);
        lineDt.innerHTML = name;

        let lineDd: HTMLElement = document.createElement('dd');
        lineDd.classList.add(classString);
        lineDd.innerHTML = value;

        list.appendChild(lineDt);
        list.appendChild(lineDd);
    }

    private async patchProductLine(productVariantSku: string, newQuantity: number): Promise<void> {
        if (this.cartId === undefined) {
            throw new Error('Missing cartId in cart');
        }
        if (this.userCartProductLineId === undefined) {
            throw new Error('Missing userCartProductLineId');
        }

        const {data, error} = await this.client.PATCH(
            '/api/cart/{cartId}/productLines/{productLineId}',
            {
                params: {
                    path: {
                        cartId: this.cartId,
                        productLineId: this.userCartProductLineId,
                    },
                },
                headers: {
                    'Content-Type': 'application/merge-patch+json',
                },
                body: {
                    productVariantSku: productVariantSku,
                    quantity: newQuantity,
                }
            }
        );
        if (error !== undefined) {
            throw error;
        }

        if (this.totalElement !== undefined && data.lineTotalCentessimal!== undefined) {
            this.totalElement.innerHTML = centessimalToCurrency(data.lineTotalCentessimal);
        }
    }

    private async deleteProductLine(): Promise<void> {
        if (this.cartId === undefined) {
            throw new Error('Missing cartId in cart');
        }
        if (this.userCartProductLineId === undefined) {
            throw new Error('Missing userCartProductLineId');
        }
        let removeSuccess = false;
        const {error} = await this.client.DELETE(
            '/api/cart/{cartId}/productLines/{productLineId}',
            {
                params: {
                    path: {
                        cartId: this.cartId,
                        productLineId: this.userCartProductLineId,
                    },
                },
                headers: {
                    'Content-Type': 'application/merge-patch+json',
                }
            }
        )
        if (error !== undefined) {
            throw error;
        }
        removeSuccess = true;
        if (removeSuccess) {
            this.remove();
        }


    }
}
