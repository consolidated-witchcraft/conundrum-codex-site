import {centessimalToCurrency} from "../helpers/CentessimalToCurrency";

export class CartLinePreview extends HTMLDListElement {

    private inputElement: HTMLInputElement;

    private lineTotalDd: HTMLElement;

    constructor() {
        super();
        let inputElement: HTMLInputElement|null = this.querySelector('input.cartline-quantity');
        let lineTotalDd : HTMLElement|null = this.querySelector('dd.line-total');
        if (lineTotalDd === null) {
            throw new Error("Could not find line token dd");
        }
        if (null === inputElement) {
            throw new Error("Cart line must have a quantity field");
        }
        this.inputElement = inputElement;
        this.lineTotalDd = lineTotalDd;
    }
    connectedCallback() {

        if (this.inputElement.dataset.name === undefined) {
            throw new Error("No dataset name provided");
        }
        if (this.inputElement.dataset.pricepretax === undefined) {
            throw new Error("Dataset price pretax data attr unset");
        }

        if (this.inputElement.dataset.typehandle === undefined) {
            throw new Error("Type Handle attr unset");
        }
        this.classList.add(this.inputElement.dataset.typehandle);

        let nameDd : HTMLElement|null = this.querySelector('dd.name');
        let pricePerUnitDd : HTMLElement|null = this.querySelector('dd.pricePerUnit');


        if (nameDd === null) {
            throw new Error("Could not find name dd");
        }
        if (pricePerUnitDd === null) {
            throw new Error("Could not find price per unit dd");
        }

        nameDd.innerText = this.inputElement.dataset.name;
        let pricePreTax:number = parseInt(this.inputElement.dataset.pricepretax);

        pricePerUnitDd.innerText = centessimalToCurrency(pricePreTax);
        let quantity: number = parseInt(this.inputElement.value);
        this.lineTotalDd.innerText = this.calculateLineTotal(pricePreTax, quantity);


        this.inputElement.addEventListener('change', () => {
           this.lineTotalDd.innerText =  this.calculateLineTotal(pricePreTax, parseInt(this.inputElement.value));
        });
        this.classList.add('loaded');
    }

    private calculateLineTotal(pricePreTax: number, quantity: number): string {



        let lineTotalDecimal: number = (pricePreTax * quantity);

        return centessimalToCurrency(lineTotalDecimal);
    }
}
