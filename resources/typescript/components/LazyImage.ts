export class LazyImage extends HTMLImageElement {

    connectedCallback() {

        this.setAttribute('aria-hidden', 'true');

        if (!this.complete) {
            this.addEventListener('load', () => {
                this.classList.add('loaded');
                this.setAttribute('aria-hidden', 'false');
            });
        }
    }
}
