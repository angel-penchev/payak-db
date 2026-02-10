// Reusable Class Helper (if not already global)
const addFormClasses = (el, classString) => {
  const classes = classString.split(' ').filter(c => c.trim().length > 0);
  el.classList.add(...classes);
};

// <ui-form-item> (Container)
class UIFormItem extends HTMLElement {
  connectedCallback() {
    // Tailwind: Grid layout with gap
    addFormClasses(this, "grid gap-2");
  }
}

// <ui-label> (Label Text)
class UILabel extends HTMLElement {
  connectedCallback() {
    addFormClasses(this, "text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-gray-700 dark:text-gray-300");
  }
}

// <ui-input> (The Actual Input Wrapper)
class UIInput extends HTMLElement {
  connectedCallback() {
    // Prevent double-rendering if connectedCallback runs twice
    if (this.querySelector('input')) return;

    // 1. Get attributes from the parent <ui-input>
    const type = this.getAttribute('type') || 'text';
    const name = this.getAttribute('name') || '';
    const id = this.getAttribute('id') || name;
    const placeholder = this.getAttribute('placeholder') || '';
    const value = this.getAttribute('value') || '';
    const required = this.hasAttribute('required');

    // 2. Create the actual <input> element
    const input = document.createElement('input');
    input.type = type;
    input.name = name;
    input.id = id;
    input.placeholder = placeholder;
    input.value = value;
    if (required) input.required = true;

    // 3. Add Tailwind Classes for the Input
    addFormClasses(input, "flex h-10 w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 px-3 py-2 text-sm text-gray-900 dark:text-gray-50 ring-offset-white dark:ring-offset-gray-950 file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-gray-500 dark:placeholder:text-gray-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-400 dark:focus-visible:ring-gray-600 disabled:cursor-not-allowed disabled:opacity-50");

    // 4. Append to Light DOM (so PHP can read the value on submit)
    this.appendChild(input);
  }
}

// Register Components
customElements.define('ui-form-item', UIFormItem);
customElements.define('ui-label', UILabel);
customElements.define('ui-input', UIInput);
