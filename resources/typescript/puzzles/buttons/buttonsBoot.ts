import {ButtonsElement} from "./ButtonsElement";
import {ButtonPuzzleButton} from "./ButtonPuzzleButton";

document.addEventListener('DOMContentLoaded', () => {

    let customElementRegistry = window.customElements;
    customElementRegistry.define('buttons-element', ButtonsElement, { extends: 'div'});
    customElementRegistry.define('buttons-puzzle-button', ButtonPuzzleButton, { extends: 'button' });

});
