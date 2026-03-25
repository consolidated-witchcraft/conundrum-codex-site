import {Client} from "openapi-fetch";
import type { paths } from "../../schema/schema";
import {UserCartProductLine} from "./UserCartProductLine";
import {UserCartProductLineRemovedDetail} from "./types/UserCartProductLineRemovedDetail";
import {createApiClient} from "@shared/helpers/CreateApiClient";

export class UserCart extends HTMLDivElement {

    private client: Client<paths>;

    private cartId: undefined|string;

    private cartEmptyElement: HTMLElement;

    constructor() {
        super();

        this.client = createApiClient();

        let cartEmptyElement: HTMLElement|null = this.querySelector('em.cart-empty-message');

        if (cartEmptyElement === null || cartEmptyElement === undefined) {
            throw new Error("Cart Empty span is not defined");
        }

        this.cartEmptyElement = cartEmptyElement;

        let cartId:string|undefined = this.dataset.cartid;
        if (cartId === undefined || cartId === "") {
            return;
        }
        this.cartId = cartId;
    }
    connectedCallback() {
        if (this.cartId !== undefined) {
            void this.fetchCart();
        }

        this.addEventListener('productLineDeleted', (event: CustomEventInit<UserCartProductLineRemovedDetail>)=> {
            if (event.detail?.productVariantSku !== null) {
                let productVariantSku: string | undefined = event.detail?.productVariantSku;
                if (productVariantSku !== undefined) {
                    let selectorString = `.user-cart-product-line[data-productvariantsku="${productVariantSku}"]`;

                    const targetElement = this.querySelector(selectorString);
                    targetElement?.remove();
                }
            }

            let productLines = this.querySelectorAll('.user-cart-product-line');
            if (productLines.length < 1) {
                this.cartEmptyElement.classList.remove('hide');
            }
        });


    }

    async fetchCart()
    {
        if (this.cartId !== undefined) {
            const {data, error} = await this.client.GET(
                '/api/cart/{cartId}',
                {
                    params: {
                        path: {
                            cartId: this.cartId,
                        },
                    },
                });

            if (error !== undefined) {
                throw error;
            }
            if (data !== undefined) {
                if (data.cartProductLines !== undefined) {
                    if (data.cartProductLines.length > 0) {
                        this.cartEmptyElement.classList.add('hide');
                    } else {
                        this.cartEmptyElement.classList.remove('hide');
                    }
                    data.cartProductLines.forEach((iri: string) => {
                        let lineElement: UserCartProductLine = new UserCartProductLine();
                        lineElement.dataset.iri = iri;
                        lineElement.setAttribute('is', 'user-cart-product-line');
                        this.appendChild(lineElement);

                    })
                }
                if (data.cartChargeLines !== undefined) {
                    data.cartChargeLines.forEach((cartChargeLine: {
                        readonly chargeTypeHandle?: string | null,
                        readonly chargeTypeName?: string | null,
                        readonly preTaxAmountCentessimal?: number | null,
                    }) => {
                        console.log(cartChargeLine);
                    })
                }
            }

        }
    }
}
