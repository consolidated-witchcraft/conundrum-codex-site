
export class ButtonPuzzleButton extends HTMLButtonElement {


    constructor() {
        super();
    }

    connectedCallback() {
        let value: string|undefined = this.dataset.buttonvalue;
        if (value === undefined) {
            throw new Error("A button puzzle button must have a defined value");
        }

        this.classList.add('puzzle-button', 'btn','btn-info', 'btn-square', 'btn-lg');
        this.addEventListener('click', (e:MouseEvent) => {
            e.preventDefault();
            this.dispatchEvent(new CustomEvent('puzzle-button-click',
                {
                    bubbles: true,
                    detail: {
                        'value': value,
                        'target': this,
                    }
                }
            ));
        });

        this.addEventListener('successful-click-attempt', () => {

            this.classList.remove('btn-info');
            this.classList.add('btn-success');
        })
    }
}
