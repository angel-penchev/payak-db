const buttonVariants = {
  base: "inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium ring-offset-white transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-950 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 dark:ring-offset-gray-950 dark:focus-visible:ring-gray-300 cursor-pointer select-none",

  variants: {
    default: "bg-gray-900 text-gray-50 hover:bg-gray-900/90 dark:bg-gray-50 dark:text-gray-900 dark:hover:bg-gray-50/90 shadow",
    destructive: "bg-red-500 text-gray-50 hover:bg-red-500/90 dark:bg-red-900 dark:text-gray-50 dark:hover:bg-red-900/90",
    outline: "border border-gray-300 bg-white hover:bg-gray-100 hover:text-gray-900 dark:border-gray-700 dark:bg-gray-950 dark:hover:bg-gray-800 dark:hover:text-gray-50",
    secondary: "bg-gray-100 text-gray-900 hover:bg-gray-100/80 dark:bg-gray-800 dark:text-gray-50 dark:hover:bg-gray-800/80",
    ghost: "hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-gray-50",
    link: "text-gray-900 underline-offset-4 hover:underline dark:text-gray-50",
  },

  sizes: {
    default: "h-10 px-4 py-2",
    sm: "h-9 rounded-md px-3",
    lg: "h-11 rounded-md px-8",
    icon: "h-10 w-10",
  }
};

class UIButton extends HTMLElement {
  connectedCallback() {
    // Prevent double-rendering
    if (this.hasAttribute('rendered')) return;
    this.setAttribute('rendered', '');

    const variant = this.getAttribute('variant') || 'default';
    const size = this.getAttribute('size') || 'default';
    const href = this.getAttribute('href');
    const type = this.getAttribute('type') || 'button';
    const className = this.getAttribute('class') || '';

    // Generate Tailwind Classes
    const baseStyle = buttonVariants.base;
    const variantStyle = buttonVariants.variants[variant] || buttonVariants.variants.default;
    const sizeStyle = buttonVariants.sizes[size] || buttonVariants.sizes.default;
    const finalClass = `${baseStyle} ${variantStyle} ${sizeStyle} ${className}`;

    // Create the actual clickable element
    let el;
    if (href) {
      el = document.createElement('a');
      el.href = href;
      // Ensure the link takes up the full space of the component
      el.style.display = 'inline-flex';
      el.style.alignItems = 'center';
      el.style.justifyContent = 'center';
      el.style.textDecoration = 'none'; // reset
    } else {
      el = document.createElement('button');
      el.type = type;
    }

    // Apply classes
    this.addClasses(el, finalClass);

    // ROBUST MOVE: Move all child nodes (text, icons) into the new element
    // We use Array.from to create a static list, preventing live-DOM issues during the loop
    const children = Array.from(this.childNodes);
    children.forEach(child => el.appendChild(child));

    // Clear the host element and append the new wrapper
    this.innerHTML = '';
    this.appendChild(el);

    // Host styling: make the custom element behave like a wrapper
    this.style.display = 'inline-flex';
    this.removeAttribute('class'); // Remove class from host to prevent duplication
  }

  addClasses(el, classString) {
    const classes = classString.split(' ').filter(c => c.trim().length > 0);
    el.classList.add(...classes);
  }
}

customElements.define('ui-button', UIButton);
