class UIHoverCard extends HTMLElement {
  get cardId() {
    return this.getAttribute('card-id');
  }

  set cardId(val) {
    if (val) {
      this.setAttribute('card-id', val);
    } else {
      this.removeAttribute('card-id');
    }
  }

  connectedCallback() {
    if (this.hasAttribute('rendered')) return;
    this.setAttribute('rendered', '');

    const triggerContent = this.querySelector('[slot="trigger"]');
    const popupContent = this.querySelector('[slot="content"]');

    this.innerHTML = '';

    const container = document.createElement('div');
    container.className = `relative flex flex-col items-center justify-end group/card-${this.cardId} z-10 hover:z-50`;

    const trigger = document.createElement('div');
    trigger.className = "transition-transform duration-200";
    if (triggerContent) {
      triggerContent.removeAttribute('slot');
      trigger.appendChild(triggerContent);
    }
    container.appendChild(trigger);

    const popup = document.createElement('div');
    popup.className = `
        absolute bottom-full mb-2 left-1/2 -translate-x-1/2
        w-max max-w-[200px]
        opacity-0 scale-95 translate-y-2 pointer-events-none
        group-hover/card-${this.cardId}:opacity-100 group-hover/card-${this.cardId}:scale-100 group-hover/card-${this.cardId}:translate-y-0 group-hover/card-${this.cardId}:pointer-events-auto
        transition-all duration-200 ease-out origin-bottom
        z-50
    `;

    const cardInner = document.createElement('div');
    cardInner.className = "bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 rounded-lg p-3 text-sm shadow-[0_4px_20px_-2px_rgba(0,0,0,0.3)] border border-gray-200 dark:border-gray-800 relative";

    if (popupContent) {
      popupContent.removeAttribute('slot');
      cardInner.appendChild(popupContent);
    }

    const arrow = document.createElement('div');
    arrow.className = "w-3 h-3 bg-white dark:bg-gray-900 border-r border-b border-gray-200 dark:border-gray-800 transform rotate-45 absolute -bottom-1.5 left-1/2 -translate-x-1/2";

    cardInner.appendChild(arrow);
    popup.appendChild(cardInner);
    container.appendChild(popup);

    this.appendChild(container);
    this.style.display = 'inline-block';
  }
}

customElements.define('ui-hover-card', UIHoverCard);
