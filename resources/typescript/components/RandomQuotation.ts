import {Client} from "openapi-fetch";
import type { paths } from "../schema/schema";
import {createApiClient} from "../helpers/CreateApiClient";

export default class RandomQuotation extends HTMLQuoteElement {

    private client: Client<paths>;
    private quotationElement: HTMLParagraphElement;
    private citationElement: HTMLSpanElement;

    constructor() {
        super();
        let quotationElement: HTMLParagraphElement = document.createElement('p');
        let citationElement: HTMLSpanElement = document.createElement('span');

        this.client = createApiClient();
        this.classList.add('random-quotation');
        quotationElement.classList.add('quotation');
        citationElement.classList.add('cite');

        this.quotationElement = quotationElement;
        this.citationElement = citationElement;
        this.appendChild(quotationElement);
        this.appendChild(citationElement);
    }

    connectedCallback(): void {
        void this.getQuote();

        this.addEventListener('click', (e: MouseEvent) => {
            e.preventDefault();
            void this.getQuote();
        })
    }

    async getQuote(): Promise<void> {
        this.classList.add('loading');
        const { data, error } = await this.client.GET("/api/quote", {});

        if (error) {
            console.error(error);
        } else {
            this.quotationElement.innerHTML = data.quote ?? '';
            this.citationElement.innerHTML = data.citation ?? '';
            this.classList.remove('loading');
        }
    }
}
