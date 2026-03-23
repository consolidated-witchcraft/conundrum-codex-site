export class LazyImage extends HTMLImageElement {
    connectedCallback() {
        this.setAttribute('aria-hidden', 'true');

        const markLoaded = () => {
            if (this.naturalWidth > 0) {
                this.classList.add('loaded');
                this.setAttribute('aria-hidden', 'false');
            }
        };

        if (this.complete) {
            markLoaded();
            return;
        }

        this.addEventListener('load', markLoaded, { once: true });
    }
}
