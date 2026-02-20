import createClient, {Client} from "openapi-fetch";
import type { paths } from "../../schema/schema";

export default class UserCart extends HTMLDivElement {

    private client: Client<paths>;

    private cartId: undefined|string;

    constructor() {
        super();
        let env: string  = 'process.env.APP_ENV';
        let apiBaseUrl = (env == '"dev"') ? 'http://localhost:8089' : 'https://conundrumcodex.com';
        this.client = createClient<paths>({ baseUrl: apiBaseUrl });
        let cartId:string|undefined = this.dataset.cartid;
        if (cartId === undefined) {
            return;
        }
        this.cartId = cartId;
    }
    connectedCallback() {
        this.fetchCart();
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
            if (data !== undefined && data.cartChargeLines !== undefined) {
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
