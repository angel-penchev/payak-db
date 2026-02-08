// Helper to add multiple classes
const addClasses = (el, classString) => {
    const classes = classString.split(' ').filter(c => c.trim().length > 0);
    el.classList.add(...classes);
};

// <ui-card>
class UICard extends HTMLElement {
    connectedCallback() {
        this.setAttribute('data-slot', 'card');

        // Handle 'size' attribute (default to 'default')
        const size = this.getAttribute('size') || 'default';
        this.setAttribute('data-size', size);

        // Base styles from your React code
        addClasses(this, "ring-foreground/10 bg-card text-card-foreground gap-4 overflow-hidden rounded-xl py-4 text-sm ring-1 has-data-[slot=card-footer]:pb-0 has-[>img:first-child]:pt-0 data-[size=sm]:gap-3 data-[size=sm]:py-3 data-[size=sm]:has-data-[slot=card-footer]:pb-0 *:[img:first-child]:rounded-t-xl *:[img:last-child]:rounded-b-xl group/card flex flex-col");
    }
}

// <ui-card-header>
class UICardHeader extends HTMLElement {
    connectedCallback() {
        this.setAttribute('data-slot', 'card-header');
        addClasses(this, "gap-1 rounded-t-xl px-4 group-data-[size=sm]/card:px-3 [.border-b]:pb-4 group-data-[size=sm]/card:[.border-b]:pb-3 group/card-header @container/card-header grid auto-rows-min items-start has-data-[slot=card-action]:grid-cols-[1fr_auto] has-data-[slot=card-description]:grid-rows-[auto_auto]");
    }
}

// <ui-card-title>
class UICardTitle extends HTMLElement {
    connectedCallback() {
        this.setAttribute('data-slot', 'card-title');
        addClasses(this, "text-base leading-snug font-medium group-data-[size=sm]/card:text-sm block");
    }
}

// <ui-card-description>
class UICardDescription extends HTMLElement {
    connectedCallback() {
        this.setAttribute('data-slot', 'card-description');
        addClasses(this, "text-muted-foreground text-sm block");
    }
}

// <ui-card-content>
class UICardContent extends HTMLElement {
    connectedCallback() {
        this.setAttribute('data-slot', 'card-content');
        addClasses(this, "px-4 group-data-[size=sm]/card:px-3 block");
    }
}

// <ui-card-footer>
class UICardFooter extends HTMLElement {
    connectedCallback() {
        this.setAttribute('data-slot', 'card-footer');
        addClasses(this, "bg-muted/50 rounded-b-xl border-t p-4 group-data-[size=sm]/card:p-3 flex items-center");
    }
}

// <ui-card-action>
class UICardAction extends HTMLElement {
    connectedCallback() {
        this.setAttribute('data-slot', 'card-action');
        addClasses(this, "col-start-2 row-span-2 row-start-1 self-start justify-self-end");
    }
}

// Register the components
customElements.define('ui-card', UICard);
customElements.define('ui-card-header', UICardHeader);
customElements.define('ui-card-title', UICardTitle);
customElements.define('ui-card-description', UICardDescription);
customElements.define('ui-card-content', UICardContent);
customElements.define('ui-card-footer', UICardFooter);
customElements.define('ui-card-action', UICardAction);
