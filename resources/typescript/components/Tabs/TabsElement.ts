export class TabsElement extends HTMLDivElement {

    private headings: HTMLElement;

    private contentContainer: HTMLElement;

    private resizeObserver: ResizeObserver | null = null;

    private heightUpdateFrame: number | null = null;

    constructor() {
        super();
        let headings: HTMLElement|null = this.querySelector('nav.tab-headings');
        let contentContainer: HTMLElement|null = this.querySelector('div.tab-content-container');

        if (null === headings) {
            throw new Error('Tabs require a headings element');
        }
        if (null === contentContainer) {
            throw new Error('Tabs require a contentContainer element');
        }

        this.headings = headings;
        this.contentContainer = contentContainer;
    }


    connectedCallback() {
        this.setupListeners();
        this.setupResizeObserver();
        this.queueHeightUpdate();
    }

    disconnectedCallback(): void {
        this.resizeObserver?.disconnect();

        if (this.heightUpdateFrame !== null) {
            cancelAnimationFrame(this.heightUpdateFrame);
            this.heightUpdateFrame = null;
        }
    }

    private setHeight(): void {
        let navBarHeight: number = (this.headings as HTMLElement).offsetHeight;
        let tallestElementHeight: number = 0;
        this.contentContainer.querySelectorAll('.tab-content').forEach((contentElement: Element) => {
            let htmlContentElement = contentElement as HTMLElement;
            if (htmlContentElement.offsetHeight > tallestElementHeight) {
                tallestElementHeight = htmlContentElement.offsetHeight;
            }
        });

        let totalHeight = navBarHeight + tallestElementHeight;

        this.style.height = totalHeight + 'px';
    }

    private queueHeightUpdate(): void {
        if (this.heightUpdateFrame !== null) {
            cancelAnimationFrame(this.heightUpdateFrame);
        }

        this.heightUpdateFrame = requestAnimationFrame(() => {
            this.heightUpdateFrame = null;
            this.setHeight();
        });
    }

    private setupResizeObserver(): void {
        this.resizeObserver = new ResizeObserver(() => {
            this.queueHeightUpdate();
        });

        this.resizeObserver.observe(this.headings);
        this.contentContainer.querySelectorAll('.tab-content').forEach((contentElement: Element) => {
            this.resizeObserver?.observe(contentElement);
        });
    }

    private setupListeners(): void {
        this.headings.querySelectorAll('a').forEach(heading => {
            heading.addEventListener('click', (e: MouseEvent) => {
                e.preventDefault();
                this.setActiveHeading(heading);
            });
        });
        this.addEventListener('child-element-resize', () => {
            this.queueHeightUpdate();
        });
    }

    private setActiveHeading(activeHeading: HTMLAnchorElement):void {
        this.headings.querySelectorAll('a').forEach((candidateHeading: HTMLAnchorElement) => {
            if (candidateHeading === activeHeading) {
                candidateHeading.classList.add('active');

                let targetId: string|null = candidateHeading.getAttribute('href');
                if (null === targetId) {
                    throw new Error('Tab element headings must have a target id');
                }
                targetId = targetId.slice(1);
                let targetElement: HTMLElement|null = this.contentContainer.querySelector(`div#${targetId}.tab-content`);
                if (null === targetElement) {
                    throw new Error('Defined target element could not be found');
                }
                this.setActiveContent(targetElement);
            } else {
                candidateHeading.classList.remove('active');
            }
        });
    }

    private setActiveContent(activeContentContainer: HTMLElement): void {
        this.contentContainer.querySelectorAll('div.tab-content').forEach((candidateContentContainer: Element) => {
            if (candidateContentContainer === activeContentContainer) {
                candidateContentContainer.classList.add('active');
            } else {
                candidateContentContainer.classList.remove('active');
            }
        });
    }
}
