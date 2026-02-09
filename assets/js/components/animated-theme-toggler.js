class AnimatedThemeToggler extends HTMLElement {
  constructor() {
    super();
    this.attachShadow({ mode: "open" });
    this._handleClick = this._handleClick.bind(this);
    // Initialize property to null
    this.button = null;
  }

  connectedCallback() {
    // Basic styles for the button container
    const styles = `
      :host {
        display: inline-block;
      }
      button {
        appearance: none;
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 0;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: inherit;
        width: 100%;
        height: 100%;
      }
    `;

    if (!this.shadowRoot) {
      console.warn("Shadow root not available.");
      return;
    }

    this.shadowRoot.innerHTML = `
      <style>${styles}</style>
      <button part="button" type="button" aria-label="Toggle theme">
        <slot></slot>
      </button>
    `;

    this.button = this.shadowRoot.querySelector("button");
    if (this.button) {
      this.button.addEventListener("click", this._handleClick);
    }
  }

  disconnectedCallback() {
    if (this.button) {
      this.button.removeEventListener("click", this._handleClick);
    }
  }

  /**
   * Helper to actually switch the class and localStorage
   * @param {boolean} isDark 
   */
  _updateThemeSystem(isDark) {
    const newTheme = !isDark;
    if (newTheme) {
      document.documentElement.classList.add("dark");
    } else {
      document.documentElement.classList.remove("dark");
    }
    localStorage.setItem("theme", newTheme ? "dark" : "light");
  }

  async _handleClick() {
    const isDark = document.documentElement.classList.contains("dark");
    const duration = Number.parseInt(this.getAttribute("duration") || "400");

    // Fallback for browsers that don't support View Transitions
    if (!document.startViewTransition) {
      this._updateThemeSystem(isDark);
      return;
    }

    // Start the transition
    const transition = document.startViewTransition(() => {
      this._updateThemeSystem(isDark);
    });

    await transition.ready;

    // Calculate Geometry
    const rect = this.button.getBoundingClientRect();
    const x = rect.left + rect.width / 2;
    const y = rect.top + rect.height / 2;

    const maxRadius = Math.hypot(
      Math.max(x, window.innerWidth - x),
      Math.max(y, window.innerHeight - y)
    );

    // Animate the circular clip path
    document.documentElement.animate(
      {
        clipPath: [
          `circle(0px at ${x}px ${y}px)`,
          `circle(${maxRadius}px at ${x}px ${y}px)`,
        ],
      },
      {
        duration: duration,
        easing: "ease-in-out",
        pseudoElement: "::view-transition-new(root)",
      }
    );
  }
}

customElements.define("animated-theme-toggler", AnimatedThemeToggler);
