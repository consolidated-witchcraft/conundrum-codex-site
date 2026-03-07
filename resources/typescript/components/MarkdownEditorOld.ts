export class MarkdownEditorOld extends HTMLTextAreaElement {

    private readonly container: HTMLDivElement;

    private readonly toolbar: HTMLDivElement;

    private readonly editor: HTMLDivElement;

    private readonly rawEditor: HTMLTextAreaElement;

    private viewMode: 'rendered'|'raw' = 'rendered';

    private isSyncing = false;

    private readonly syncFromEditorListener: () => void;

    private readonly syncFromTextareaListener: () => void;

    private readonly syncFromRawEditorListener: () => void;

    private readonly toolbarClickListener: (event: MouseEvent) => void;

    constructor() {
        super();

        this.container = document.createElement('div');
        this.toolbar = document.createElement('div');
        this.editor = document.createElement('div');
        this.rawEditor = document.createElement('textarea');
        this.syncFromEditorListener = () => this.syncMarkdownFromEditor();
        this.syncFromTextareaListener = () => this.syncEditorFromMarkdown();
        this.syncFromRawEditorListener = () => this.syncMarkdownFromRawEditor();
        this.toolbarClickListener = (event: MouseEvent) => this.onToolbarClick(event);

        this.container.classList.add('markdown-editor');
        this.toolbar.classList.add('markdown-editor-toolbar');
        this.editor.classList.add('markdown-editor-surface');
        this.rawEditor.classList.add('markdown-editor-raw-surface');
        this.editor.setAttribute('contenteditable', 'true');
        this.editor.setAttribute('role', 'textbox');
        this.editor.setAttribute('aria-multiline', 'true');
        this.rawEditor.setAttribute('aria-label', 'Raw markdown');
        this.rawEditor.style.display = 'none';

        this.buildToolbar();
    }

    connectedCallback(): void {
        if (!this.container.isConnected) {
            this.container.appendChild(this.toolbar);
            this.container.appendChild(this.editor);
            this.container.appendChild(this.rawEditor);
            this.insertAdjacentElement('afterend', this.container);
        }

        this.style.display = 'none';
        this.editor.innerHTML = this.markdownToHtml(this.value);
        this.rawEditor.value = this.value;
        this.updateToolbarViewButton();

        this.editor.addEventListener('input', this.syncFromEditorListener);
        this.editor.addEventListener('blur', this.syncFromEditorListener);
        this.rawEditor.addEventListener('input', this.syncFromRawEditorListener);
        this.addEventListener('input', this.syncFromTextareaListener);
        this.toolbar.addEventListener('click', this.toolbarClickListener);
    }

    disconnectedCallback(): void {
        this.editor.removeEventListener('input', this.syncFromEditorListener);
        this.editor.removeEventListener('blur', this.syncFromEditorListener);
        this.rawEditor.removeEventListener('input', this.syncFromRawEditorListener);
        this.removeEventListener('input', this.syncFromTextareaListener);
        this.toolbar.removeEventListener('click', this.toolbarClickListener);
        this.container.remove();
    }

    private buildToolbar(): void {
        const actions: Array<{ action: string, label: string }> = [
            {action: 'bold', label: 'Bold'},
            {action: 'italic', label: 'Italic'},
            {action: 'h2', label: 'H2'},
            {action: 'unordered-list', label: 'UL'},
            {action: 'ordered-list', label: 'OL'},
            {action: 'quote', label: 'Quote'},
            {action: 'code', label: 'Code'},
            {action: 'link', label: 'Link'},
            {action: 'toggle-view', label: 'Raw'},
        ];

        actions.forEach(({action, label}) => {
            const button: HTMLButtonElement = document.createElement('button');
            button.type = 'button';
            button.dataset.action = action;
            button.textContent = label;
            button.classList.add('markdown-toolbar-button');
            button.classList.add(action);
            this.toolbar.appendChild(button);
        });
    }

    private onToolbarClick(event: MouseEvent): void {
        const target: HTMLElement|null = event.target as HTMLElement | null;
        if (target === null) {
            return;
        }

        const button = target.closest('button');
        if (!(button instanceof HTMLButtonElement) || button.dataset.action === undefined) {
            return;
        }

        event.preventDefault();
        const {action} = button.dataset;
        if (action === undefined) {
            return;
        }

        if (action === 'toggle-view') {
            this.toggleViewMode();
            return;
        }

        if (this.viewMode === 'raw') {
            return;
        }

        this.editor.focus();
        this.applyAction(action);
        this.syncMarkdownFromEditor();
    }

    private toggleViewMode(): void {
        if (this.viewMode === 'rendered') {
            this.setViewMode('raw');
            return;
        }

        this.setViewMode('rendered');
    }

    private setViewMode(mode: 'rendered'|'raw'): void {
        if (mode === this.viewMode) {
            return;
        }

        if (mode === 'raw') {
            this.syncMarkdownFromEditor();
            this.rawEditor.value = this.value;
            this.editor.style.display = 'none';
            this.rawEditor.style.display = '';
            this.rawEditor.focus();
            this.viewMode = 'raw';
        } else {
            this.value = this.rawEditor.value;
            this.syncEditorFromMarkdown();
            this.rawEditor.style.display = 'none';
            this.editor.style.display = '';
            this.editor.focus();
            this.viewMode = 'rendered';
        }

        this.updateToolbarViewButton();
    }

    private updateToolbarViewButton(): void {
        const toggleButton: HTMLButtonElement|null = this.toolbar.querySelector('button[data-action="toggle-view"]');
        if (toggleButton === null) {
            return;
        }

        toggleButton.textContent = this.viewMode === 'rendered' ? 'Raw' : 'Preview';

        const actionButtons = this.toolbar.querySelectorAll<HTMLButtonElement>('button[data-action]:not([data-action="toggle-view"])');
        actionButtons.forEach((toolbarButton: HTMLButtonElement): void => {
            toolbarButton.disabled = this.viewMode === 'raw';
        });
    }

    private applyAction(action: string): void {
        switch (action) {
            case 'bold':
                this.applyInlineTag('strong');
                return;
            case 'italic':
                this.applyInlineTag('em');
                return;
            case 'h2':
                this.applyBlockTag('h2');
                return;
            case 'unordered-list':
                this.applyListTag('ul');
                return;
            case 'ordered-list':
                this.applyListTag('ol');
                return;
            case 'quote':
                this.applyBlockTag('blockquote');
                return;
            case 'code':
                this.insertCodeTag();
                return;
            case 'link':
                this.insertLink();
                return;
            default:
                return;
        }
    }

    private insertCodeTag(): void {
        const selectedText: string = this.getSelectedText();
        if (selectedText.trim() === '') {
            return;
        }

        const codeNode = document.createElement('code');
        this.wrapSelectionWithElement(codeNode);
    }

    private insertLink(): void {
        const selectedText: string = this.getSelectedText().trim();
        if (selectedText === '') {
            return;
        }

        const uri: string|null = window.prompt('Enter link URL');
        if (uri === null || uri.trim() === '') {
            return;
        }

        const linkNode = document.createElement('a');
        linkNode.setAttribute('href', uri.trim());
        this.wrapSelectionWithElement(linkNode);
    }

    private getSelectedText(): string {
        const range = this.getEditorSelectionRange();
        if (range === null) {
            return '';
        }

        return range.toString();
    }

    private applyInlineTag(tagName: 'strong'|'em'): void {
        const wrapper = document.createElement(tagName);
        this.wrapSelectionWithElement(wrapper);
    }

    private applyBlockTag(tagName: 'h2'|'blockquote'): void {
        const block = this.getCurrentBlockElement();
        if (block === null) {
            return;
        }

        if (tagName === 'blockquote') {
            if (block.tagName.toLowerCase() === 'blockquote') {
                return;
            }

            const wrapper = document.createElement('blockquote');
            block.replaceWith(wrapper);
            wrapper.appendChild(block);
            this.placeCaretAtEnd(block);
            return;
        }

        this.replaceElementTag(block, tagName);
    }

    private applyListTag(listTag: 'ul'|'ol'): void {
        const block = this.getCurrentBlockElement();
        if (block === null) {
            return;
        }

        if (block.tagName.toLowerCase() === 'li') {
            const existingList = block.parentElement;
            if (existingList !== null && (existingList.tagName.toLowerCase() === 'ul' || existingList.tagName.toLowerCase() === 'ol')) {
                if (existingList.tagName.toLowerCase() === listTag) {
                    return;
                }

                const replacementList = document.createElement(listTag);
                while (existingList.firstChild !== null) {
                    replacementList.appendChild(existingList.firstChild);
                }
                existingList.replaceWith(replacementList);
                this.placeCaretAtEnd(block);
                return;
            }
        }

        const list = document.createElement(listTag);
        const listItem = document.createElement('li');
        listItem.innerHTML = block.innerHTML;
        list.appendChild(listItem);
        block.replaceWith(list);
        this.placeCaretAtEnd(listItem);
    }

    private getEditorSelectionRange(): Range|null {
        const selection: Selection|null = window.getSelection();
        if (selection === null || selection.rangeCount === 0) {
            return null;
        }

        const range = selection.getRangeAt(0);
        if (!this.isNodeInsideEditor(range.commonAncestorContainer)) {
            return null;
        }

        return range;
    }

    private isNodeInsideEditor(node: Node): boolean {
        if (node === this.editor) {
            return true;
        }

        if (node instanceof HTMLElement) {
            return this.editor.contains(node);
        }

        return this.editor.contains(node.parentNode);
    }

    private wrapSelectionWithElement(wrapper: HTMLElement): boolean {
        const range = this.getEditorSelectionRange();
        if (range === null || range.collapsed || range.toString().trim() === '') {
            return false;
        }

        const fragment = range.extractContents();
        wrapper.appendChild(fragment);
        range.insertNode(wrapper);
        this.selectNodeContents(wrapper);
        return true;
    }

    private getCurrentBlockElement(): HTMLElement|null {
        const range = this.getEditorSelectionRange();
        if (range === null) {
            return null;
        }

        let currentNode: Node = range.commonAncestorContainer;
        if (currentNode.nodeType === Node.TEXT_NODE && currentNode.parentElement !== null) {
            currentNode = currentNode.parentElement;
        }

        while (currentNode instanceof HTMLElement && currentNode !== this.editor) {
            const tagName = currentNode.tagName.toLowerCase();
            if (['p', 'div', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote', 'li', 'pre'].includes(tagName)) {
                return currentNode;
            }
            if (currentNode.parentElement === null) {
                return null;
            }
            currentNode = currentNode.parentElement;
        }

        return null;
    }

    private replaceElementTag(element: HTMLElement, tagName: string): void {
        if (element.tagName.toLowerCase() === tagName) {
            return;
        }

        const replacement = document.createElement(tagName);
        replacement.innerHTML = element.innerHTML;
        element.replaceWith(replacement);
        this.placeCaretAtEnd(replacement);
    }

    private placeCaretAtEnd(element: HTMLElement): void {
        const selection: Selection|null = window.getSelection();
        if (selection === null) {
            return;
        }

        const range = document.createRange();
        range.selectNodeContents(element);
        range.collapse(false);
        selection.removeAllRanges();
        selection.addRange(range);
    }

    private selectNodeContents(element: HTMLElement): void {
        const selection: Selection|null = window.getSelection();
        if (selection === null) {
            return;
        }

        const range = document.createRange();
        range.selectNodeContents(element);
        selection.removeAllRanges();
        selection.addRange(range);
    }

    private syncMarkdownFromEditor(): void {
        if (this.isSyncing) {
            return;
        }

        this.isSyncing = true;
        const markdownValue = this.htmlToMarkdown(this.editor.innerHTML);
        this.value = markdownValue;
        this.dispatchEvent(new Event('input', {bubbles: true}));
        this.isSyncing = false;
    }

    private syncEditorFromMarkdown(): void {
        if (this.isSyncing) {
            return;
        }

        this.isSyncing = true;
        this.editor.innerHTML = this.markdownToHtml(this.value);
        if (this.viewMode === 'raw') {
            this.rawEditor.value = this.value;
        }
        this.isSyncing = false;
    }

    private syncMarkdownFromRawEditor(): void {
        if (this.isSyncing) {
            return;
        }

        this.isSyncing = true;
        this.value = this.rawEditor.value;
        this.dispatchEvent(new Event('input', {bubbles: true}));
        this.isSyncing = false;
    }

    private markdownToHtml(markdown: string): string {
        const lines: string[] = markdown.replace(/\r\n?/g, '\n').split('\n');
        const fragments: string[] = [];
        const paragraphBuffer: string[] = [];
        const listBuffer: Array<{type: 'ul'|'ol', content: string}> = [];
        let codeBuffer: string[] = [];
        let inCodeBlock = false;

        const flushParagraph = (): void => {
            if (paragraphBuffer.length === 0) {
                return;
            }

            const content = paragraphBuffer.map(line => this.parseInlineMarkdown(line)).join('<br>');
            fragments.push(`<p>${content}</p>`);
            paragraphBuffer.length = 0;
        };

        const flushList = (): void => {
            if (listBuffer.length === 0) {
                return;
            }

            const firstItem = listBuffer[0];
            if (firstItem === undefined) {
                return;
            }

            const listType = firstItem.type;
            const items = listBuffer.map(item => `<li>${this.parseInlineMarkdown(item.content)}</li>`).join('');
            fragments.push(`<${listType}>${items}</${listType}>`);
            listBuffer.length = 0;
        };

        lines.forEach(line => {
            if (line.startsWith('```')) {
                if (inCodeBlock) {
                    fragments.push(`<pre><code>${this.escapeHtml(codeBuffer.join('\n'))}</code></pre>`);
                    codeBuffer = [];
                    inCodeBlock = false;
                } else {
                    flushParagraph();
                    flushList();
                    inCodeBlock = true;
                }
                return;
            }

            if (inCodeBlock) {
                codeBuffer.push(line);
                return;
            }

            if (line.trim() === '') {
                flushParagraph();
                flushList();
                return;
            }

            const headingMatch = line.match(/^(#{1,6})\s+(.*)$/);
            if (headingMatch !== null) {
                flushParagraph();
                flushList();
                const hashes = headingMatch[1] ?? '#';
                const level = Math.max(1, Math.min(6, hashes.length));
                fragments.push(`<h${level}>${this.parseInlineMarkdown(headingMatch[2] ?? '')}</h${level}>`);
                return;
            }

            if (/^(-{3,}|\*{3,}|_{3,})$/.test(line.trim())) {
                flushParagraph();
                flushList();
                fragments.push('<hr>');
                return;
            }

            const quoteMatch = line.match(/^>\s?(.*)$/);
            if (quoteMatch !== null) {
                flushParagraph();
                flushList();
                fragments.push(`<blockquote><p>${this.parseInlineMarkdown(quoteMatch[1] ?? '')}</p></blockquote>`);
                return;
            }

            const unorderedMatch = line.match(/^[-*+]\s+(.*)$/);
            if (unorderedMatch !== null) {
                flushParagraph();
                const firstItem = listBuffer[0];
                if (listBuffer.length > 0 && firstItem !== undefined && firstItem.type !== 'ul') {
                    flushList();
                }
                listBuffer.push({type: 'ul', content: unorderedMatch[1] ?? ''});
                return;
            }

            const orderedMatch = line.match(/^\d+\.\s+(.*)$/);
            if (orderedMatch !== null) {
                flushParagraph();
                const firstItem = listBuffer[0];
                if (listBuffer.length > 0 && firstItem !== undefined && firstItem.type !== 'ol') {
                    flushList();
                }
                listBuffer.push({type: 'ol', content: orderedMatch[1] ?? ''});
                return;
            }

            if (listBuffer.length > 0) {
                flushList();
            }
            paragraphBuffer.push(line);
        });

        if (inCodeBlock) {
            fragments.push(`<pre><code>${this.escapeHtml(codeBuffer.join('\n'))}</code></pre>`);
        }
        flushParagraph();
        flushList();

        return fragments.join('');
    }

    private parseInlineMarkdown(line: string): string {
        const codeSegments: string[] = [];
        let html = this.escapeHtml(line).replace(/`([^`]+)`/g, (_fullMatch: string, content: string): string => {
            const token = `__CODE_SEGMENT_${codeSegments.length}__`;
            codeSegments.push(`<code>${this.escapeHtml(content)}</code>`);
            return token;
        });

        html = html
            .replace(/\[([^\]]+)]\(([^)\s]+)\)/g, (_fullMatch: string, text: string, uri: string): string => `<a href="${this.escapeHtmlAttribute(uri)}">${text}</a>`)
            .replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>')
            .replace(/__([^_]+)__/g, '<strong>$1</strong>')
            .replace(/\*([^*]+)\*/g, '<em>$1</em>')
            .replace(/_([^_]+)_/g, '<em>$1</em>')
            .replace(/~~([^~]+)~~/g, '<del>$1</del>');

        codeSegments.forEach((segment, index) => {
            html = html.replace(`__CODE_SEGMENT_${index}__`, segment);
        });

        return html;
    }

    private htmlToMarkdown(html: string): string {
        const parser = new DOMParser();
        const parsed = parser.parseFromString(`<div>${html}</div>`, 'text/html');
        const wrapper: HTMLElement|null = parsed.body.firstElementChild as HTMLElement | null;

        if (wrapper === null) {
            return '';
        }

        const markdown = this.serializeChildren(wrapper).replace(/\n{3,}/g, '\n\n').trim();
        return markdown;
    }

    private serializeChildren(element: Element): string {
        const parts: string[] = [];
        element.childNodes.forEach((node: ChildNode) => {
            parts.push(this.serializeNode(node));
        });
        return parts.join('');
    }

    private serializeNode(node: ChildNode): string {
        if (node.nodeType === Node.TEXT_NODE) {
            return this.escapeMarkdownText(node.textContent ?? '');
        }

        if (!(node instanceof HTMLElement)) {
            return '';
        }

        switch (node.tagName.toLowerCase()) {
            case 'br':
                return '  \n';
            case 'p':
            case 'div': {
                const content = this.serializeChildren(node).trim();
                return content === '' ? '' : `${content}\n\n`;
            }
            case 'strong':
            case 'b':
                return `**${this.serializeChildren(node)}**`;
            case 'em':
            case 'i':
                return `*${this.serializeChildren(node)}*`;
            case 'code':
                if (node.parentElement?.tagName.toLowerCase() === 'pre') {
                    return node.textContent ?? '';
                }
                return `\`${this.serializeChildren(node)}\``;
            case 'pre':
                return `\`\`\`\n${(node.textContent ?? '').trim()}\n\`\`\`\n\n`;
            case 'a': {
                const href = node.getAttribute('href') ?? '';
                const text = this.serializeChildren(node).trim();
                return `[${text === '' ? href : text}](${href})`;
            }
            case 'h1':
            case 'h2':
            case 'h3':
            case 'h4':
            case 'h5':
            case 'h6': {
                const level = Number(node.tagName.slice(1));
                const hashes = '#'.repeat(level);
                return `${hashes} ${this.serializeChildren(node).trim()}\n\n`;
            }
            case 'blockquote': {
                const content = this.serializeChildren(node).trim();
                const quoted = content.split('\n').map((line: string) => `> ${line}`).join('\n');
                return `${quoted}\n\n`;
            }
            case 'ul': {
                const lines: string[] = [];
                node.querySelectorAll(':scope > li').forEach((item: Element) => {
                    lines.push(`- ${this.serializeChildren(item).trim()}`);
                });
                return `${lines.join('\n')}\n\n`;
            }
            case 'ol': {
                const lines: string[] = [];
                node.querySelectorAll(':scope > li').forEach((item: Element, index: number) => {
                    lines.push(`${index + 1}. ${this.serializeChildren(item).trim()}`);
                });
                return `${lines.join('\n')}\n\n`;
            }
            case 'hr':
                return '\n---\n\n';
            default:
                return this.serializeChildren(node);
        }
    }

    private escapeHtml(value: string): string {
        return value
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll('\'', '&#39;');
    }

    private escapeHtmlAttribute(value: string): string {
        return value.replaceAll('&', '&amp;')
            .replaceAll('"', '&quot;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;');
    }

    private escapeMarkdownText(value: string): string {
        return value
            .replaceAll('\\', '\\\\')
            .replaceAll('`', '\\`')
            .replaceAll('*', '\\*')
            .replaceAll('_', '\\_')
            .replaceAll('[', '\\[')
            .replaceAll(']', '\\]');
    }
}
