export interface ButtonPressedDetail {
    value: string;
    target: ButtonPuzzleButton;
}

export class ButtonPuzzleButton extends HTMLButtonElement {
    private isBound = false;

    connectedCallback(): void {
        this.classList.add("puzzle-button");
        this.type = "button";

        if (this.isBound) {
            return;
        }

        this.addEventListener("click", this.handleClick);
        this.addEventListener("successful-press", this.handleSuccessfulPress);
        this.isBound = true;
    }

    disconnectedCallback(): void {
        if (!this.isBound) {
            return;
        }

        this.removeEventListener("click", this.handleClick);
        this.removeEventListener("successful-press", this.handleSuccessfulPress);
        this.isBound = false;
    }

    private readonly handleClick = (): void => {
        const value = this.dataset.buttonValue;

        if (value === undefined) {
            throw new Error("Button puzzle button value is missing.");
        }

        this.dispatchEvent(new CustomEvent<ButtonPressedDetail>("pressed", {
            bubbles: true,
            detail: {
                value,
                target: this,
            },
        }));
    };

    private readonly handleSuccessfulPress = (): void => {
        this.classList.remove("btn-primary");
        this.classList.add("btn-success");
    };
}
