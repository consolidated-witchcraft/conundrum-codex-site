export function initialiseDropCaps(): void {
        const dropCaps: NodeListOf<Element> = document.querySelectorAll('.drop-cap');

        dropCaps.forEach(function (dropCap: Element) {
            let initial = dropCap.innerHTML?.charAt(0);
            if (initial === undefined) {
                throw new Error('DropCap requires text content');
            }
            const content = document.createTextNode(initial);
            const span = document.createElement('span');
            let trimmedContent: string = dropCap.innerHTML.substring(1);

            span.classList.add('dc');

            span.classList.add('dc-'+initial.toLowerCase());

            if (dropCap.classList.contains('nofloat')) {
                span.classList.add('nofloat');
            }

            if (dropCap.classList.contains('sm')) {
                span.classList.add('sm');
            }
            span.appendChild(content);

            dropCap.innerHTML = trimmedContent;
            dropCap.insertBefore(span, dropCap.firstChild);
            dropCap.setAttribute('content', trimmedContent);
        })
}
