import {rollDice} from "@shared/helpers/RollDie";

export default class LightForge extends HTMLDivElement {

    private ringButton: HTMLButtonElement;

    private doorbellChime: HTMLAudioElement;

    private resetButton: HTMLButtonElement;

    private solveButton: HTMLButtonElement;

    private switches: NodeListOf<HTMLInputElement>;

    private messageContainer: HTMLDivElement;

    private switchesContainer: HTMLDivElement;

    private solution: string[];

    constructor() {
        super();
        let ringButton = this.querySelector('button#ring'),
            doorbellChime = this.querySelector('audio#chime'),
            resetButton = this.querySelector('button#reset'),
            solveButton = this.querySelector('button#solve'),
            messageContainer = this.querySelector('div#message-container'),
            switchesContainer = this.querySelector('div#switches'),
            solution = this.dataset.solution;

        if (!(ringButton instanceof HTMLButtonElement)){
            throw new Error('LightForge requires a ring button');
        }

        if (!(messageContainer instanceof HTMLDivElement)){
            throw new Error('LightForge requires a message container');
        }

        if (!(switchesContainer instanceof HTMLDivElement)){
            throw new Error('LightForge requires a switches container');
        }
        if (!(resetButton instanceof HTMLButtonElement)){
            throw new Error('LightForge requires a reset button');
        }

        if (!(solveButton instanceof HTMLButtonElement)){
            throw new Error('LightForge requires a solve button');
        }

        if (!(doorbellChime instanceof HTMLAudioElement)){
            throw new Error('LightForge requires a doorbell chime audio element');
        }

        if (undefined === solution) {
            throw new Error('LightForge requires a solution');
        }
        this.solution = solution.split(',');
        this.messageContainer = messageContainer;
        this.switchesContainer = switchesContainer;
        this.ringButton = ringButton;
        this.doorbellChime = doorbellChime;
        this.resetButton = resetButton;
        this.solveButton = solveButton;
        this.switches = this.querySelectorAll('input[type="checkbox"]');
    }

    connectedCallback() {
        this.setupListeners();
    }

    private setupListeners(): void {
        this.ringButton.addEventListener('click', () => {
           this.doorbellChime.play();
        });

        this.resetButton.addEventListener('click', () => {
            this.handleReset();
        });

        this.solveButton.addEventListener('click', () => {
            let success: boolean = true;

            this.switches.forEach((switchElement: HTMLInputElement) => {
                //What to do, what to do.
                if (this.solution.indexOf(switchElement.id) !== -1) {
                    if (!switchElement.checked) {
                        success = false;
                    }
                } else {
                    if (switchElement.checked) {
                        success = false;
                    }
                }
            });
           if (success) {
               this.handleSuccess();
           } else {
               this.handleFailure();
           }
        });
    }

    private handleReset(): void {
        this.switchesContainer.classList.remove('lock');
        this.messageContainer.classList.remove('show');
        this.switches.forEach((switchElement: HTMLInputElement) => {
            switchElement.checked = false;
        })
    }
    private handleSuccess(): void {
        this.switchesContainer.classList.add('lock');
        this.messageContainer.innerHTML = 'Success! The door is now open.';
        this.messageContainer.classList.add('show');

    }

    private handleFailure(): void {
        this.switchesContainer.classList.add('lock');
        let damage = rollDice(10, 4);
        this.messageContainer.innerHTML = `Failure! The floor panels deliver an electric shock dealing ${damage} damage to you!`;
        this.messageContainer.classList.add('show');
    }
}
