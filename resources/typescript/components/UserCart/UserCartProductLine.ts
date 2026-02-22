import type { paths } from "../../schema/schema";
import {Client} from "openapi-fetch";
import {centessimalToCurrency} from "../../helpers/CentessimalToCurrency";
import {createApiClient} from "../../helpers/CreateApiClient";
export class UserCartProductLine extends HTMLDivElement {

    private client: Client<paths>;

    private cartId: string|undefined;

    private userCartProductLineId: string|undefined;

    private totalElement: HTMLElement|undefined;

    constructor() {
        super();
        this.client = createApiClient();
    }

    private requireIds(): { cartId: string; productLineId: string } {
        if (this.cartId === undefined) {
            throw new Error('Missing cartId in cart');
        }

        if (this.userCartProductLineId === undefined) {
            throw new Error('Missing userCartProductLineId');
        }

        return {
            cartId: this.cartId,
            productLineId: this.userCartProductLineId,
        };
    }

    private parseProductLineIri(iri: string): { cartId: string; productLineId: string } {
        const match = iri.match(/^\/api\/cart\/([^/]+)\/productLines\/([^/]+)$/);

        if (!match) {
            throw new Error(`Invalid product line IRI format: ${iri}`);
        }

        const [, cartId, productLineId] = match;

        if (cartId === undefined) {
            throw new Error('Missing cartId in cart');
        }

        if (productLineId === undefined) {
            throw new Error('Missing productLineId in cart');
        }
        return { cartId, productLineId };
    }

    connectedCallback() {
        const iri: string|undefined = this.dataset.iri;
        if (iri === undefined || iri === null) {
            throw new Error('Missing iri');
        }
        this.classList.add('user-cart-product-line');
        const { cartId, productLineId } = this.parseProductLineIri(iri);
        this.cartId = cartId;
        this.userCartProductLineId = productLineId;

        void this.buildProductLineItem();
    }

    private async buildProductLineItem(): Promise<void> {
        const { cartId, productLineId } = this.requireIds();

        let productLineDl: HTMLDListElement = document.createElement('dl');
        productLineDl.classList.add('cart-product-line-item');

        const {data, error} = await this.client.GET(
            '/api/cart/{cartId}/productLines/{productLineId}',
            {
                params: {
                    path: {
                        cartId: cartId,
                        productLineId: productLineId,
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

            if (data.productVariantPriceCentessimal !== undefined) {
                this.generateDtDdPair(
                    'Price Each',
                    'product-ppu',
                    centessimalToCurrency(data.productVariantPriceCentessimal),
                    productLineDl
                );
            }

            if (data.quantity !== undefined && data.productVariantSku !== undefined && data.lineTotalCentessimal !== undefined) {
                this.dataset.productvariantsku = data.productVariantSku;

                this.generateQuantityFields(
                    data.quantity,
                    data.productVariantSku,
                    productLineDl
                );

                this.generateTotalFields(data.lineTotalCentessimal, productLineDl);

                this.generateRemoveFields(data.productVariantSku, productLineDl);
            }
        }
        this.appendChild(productLineDl);
    }
    private async patchProductLine(productVariantSku: string, newQuantity: number): Promise<void> {
        const { cartId, productLineId } = this.requireIds();

        const {data, error} = await this.client.PATCH(
            '/api/cart/{cartId}/productLines/{productLineId}',
            {
                params: {
                    path: {
                        cartId: cartId,
                        productLineId: productLineId,
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
            this.totalElement.innerText = centessimalToCurrency(data.lineTotalCentessimal);
        }
    }

    private async deleteProductLine(productVariantSku: string): Promise<void> {
        const { cartId, productLineId } = this.requireIds();

        const {error} = await this.client.DELETE(
            '/api/cart/{cartId}/productLines/{productLineId}',
            {
                params: {
                    path: {
                        cartId: cartId,
                        productLineId: productLineId,
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

        this.dispatchEvent(new CustomEvent('productLineDeleted', {
            composed: true,
            bubbles: true,
            detail: {
                productVariantSku: productVariantSku
            }
        }));


    }

    private generateQuantityFields(quantity: number, productVariantSku: string, parentElement: ParentNode): void {

        const quantityDt = document.createElement('dt');
        const quantityDd = document.createElement('dd');
        const quantityInput = document.createElement('input');

        quantityDt.classList.add('product-qty');
        quantityDt.textContent = 'Quantity';
        quantityDd.classList.add('product-qty');

        quantityInput.setAttribute('type', 'number');
        quantityInput.setAttribute('min', '0');
        quantityInput.setAttribute('max', '100');
        quantityInput.setAttribute('step', '1');
        quantityInput.setAttribute('name', `${productVariantSku}['qty']`);
        quantityInput.value = quantity.toString();
        quantityInput.addEventListener('change', async (event) => {
            event.preventDefault();
            void this.patchProductLine(
                productVariantSku,
                parseInt(quantityInput.value),
            );
        })

        quantityDd.appendChild(quantityInput);
        parentElement.appendChild(quantityDt);
        parentElement.appendChild(quantityDd);
    }

    private generateRemoveFields(productVariantSku: string, parentElement: ParentNode): void {
        const productLineRemoveDt: HTMLElement = document.createElement('dt');
        const productLineRemoveDd:HTMLElement = document.createElement('dd');
        const productLineRemoveButton: HTMLButtonElement = document.createElement('button');

        productLineRemoveDt.classList.add('product-remove');
        productLineRemoveDt.textContent = 'Remove';
        productLineRemoveDd.classList.add('product-remove');
        productLineRemoveButton.setAttribute('type', 'button');
        productLineRemoveButton.textContent = 'Remove';
        productLineRemoveButton.addEventListener('click', (event: MouseEvent) => {
            event.preventDefault();
            void this.deleteProductLine(productVariantSku);
        })
        productLineRemoveButton.classList.add('btn', 'btn-danger');
        productLineRemoveDd.appendChild(productLineRemoveButton);

        parentElement.appendChild(productLineRemoveDt);
        parentElement.appendChild(productLineRemoveDd);
    }

    private generateTotalFields(lineTotalCentessimal: number, parentElement: ParentNode): void {
        const lineDt: HTMLElement = document.createElement('dt');
        const lineDd: HTMLElement = document.createElement('dd');

        lineDt.classList.add('product-line-total');
        lineDt.innerText = 'Total';

        lineDd.classList.add('product-line-total');
        lineDd.innerText = centessimalToCurrency(lineTotalCentessimal);

        parentElement.appendChild(lineDt);
        parentElement.appendChild(lineDd);
        this.totalElement = lineDd;
    }

    private generateDtDdPair(
        name: string,
        classString: string,
        value: string,
        list: HTMLDListElement
    ): void {
        const lineDt: HTMLElement = document.createElement('dt');
        const lineDd: HTMLElement = document.createElement('dd');

        lineDt.classList.add(classString);
        lineDt.innerText = name;

        lineDd.classList.add(classString);
        lineDd.innerText = value;

        list.appendChild(lineDt);
        list.appendChild(lineDd);
    }
}
