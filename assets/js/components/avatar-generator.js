import { createAvatar } from 'https://esm.sh/@dicebear/core@9.0.1';
import { avataaars } from 'https://esm.sh/@dicebear/collection@9.0.1';

const options = {
  skinColor: ['edb98a', '614335', 'ae5d29', 'd08b5b', 'f8d25c', 'ffdbb4', 'fd9841'],
  top: ['dreads01', 'curvy', 'frizzle', 'shaggy', 'bun', 'frida', 'turban', 'hijab', 'bigHair', 'bob', 'straight01', 'straight02', 'winterHat04', 'theCaesarAndSidePart', 'noHair'],
  hatColor: ['3c4f5c', '65c9ff', '262e33', '5199e4', '25557c', '929598', 'a7ffc4', 'b1e2ff', 'e6e6e6', 'ff5c5c', 'ff488e', 'ffafb9', 'ffffb1', 'ffffff'],
  hairColor: ['2c1b18', '4a312c', '724133', 'a55728', 'b58143', 'c93305', 'd6b370', 'e8e1e1', 'ecdcbf', 'f59797'],
  facialHair: ['none', 'beardLight', 'beardMajestic', 'beardMedium', 'moustacheFancy', 'moustacheMagnum'],
  facialHairColor: ['2c1b18', '4a312c', '724133', 'a55728', 'b58143', 'c93305', 'd6b370', 'e8e1e1', 'ecdcbf', 'f59797'],
  eyes: ['default', 'closed', 'cry', 'eyeRoll', 'happy', 'hearts', 'side', 'squint', 'surprised', 'wink', 'winkWacky', 'xDizzy'],
  eyebrows: ['angry', 'angryNatural', 'default', 'defaultNatural', 'flatNatural', 'frownNatural', 'raisedExcited', 'raisedExcitedNatural', 'sadConcerned', 'sadConcernedNatural', 'unibrowNatural', 'upDown', 'upDownNatural'],
  mouth: ['default', 'smile', 'sad', 'serious', 'screamOpen', 'tongue', 'eating'],
  accessories: ['none', 'eyepatch', 'kurt', 'prescription01', 'prescription02', 'round', 'sunglasses', 'wayfarers'],
  clothing: ['blazerAndShirt', 'blazerAndSweater', 'collarAndSweater', 'graphicShirt', 'hoodie', 'overall', 'shirtCrewNeck', 'shirtScoopNeck', 'shirtVNeck'],
  clothesColor: ['3c4f5c', '65c9ff', '262e33', '5199e4', '25557c', '929598', 'a7ffc4', 'b1e2ff', 'e6e6e6', 'ff5c5c', 'ff488e', 'ffafb9', 'ffffb1', 'ffffff'],
};

const icons = {
  skinColor: '<path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" fill="currentColor"/>',
  top: '<path d="M12 2C7.5 2 4 5.5 4 8C4 10.5 6 12 6 12V20H18V12C18 12 20 10.5 20 8C20 5.5 16.5 2 12 2Z" fill="currentColor"/>',
  // New Icon for Hat Color
  hatColor: '<path d="M2 12h20v3H2zm2 3h16v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2z" fill="currentColor" opacity="0.5"/><path d="M12 2C7.5 2 4 5.5 4 8c0 2.5 2 4 2 4h12s2-1.5 2-4c0-2.5-3.5-6-8-6z" fill="currentColor"/>',
  hairColor: '<path d="M19.07 4.93L17.66 6.34C18.5 7.18 19 8.24 19 9.41V20H5V9.41C5 8.24 5.5 7.18 6.34 6.34L4.93 4.93C3.62 6.24 2.87 7.96 2.76 9.79L2.73 10H1V22H23V10H21.27L21.24 9.79C21.13 7.96 20.38 6.24 19.07 4.93Z" fill="currentColor"/>',
  facialHair: '<path d="M12 14C9.5 14 7.5 15.5 7.5 17.5C7.5 19.5 9.5 21 12 21C14.5 21 16.5 19.5 16.5 17.5C16.5 15.5 14.5 14 12 14ZM12 19C10.5 19 9.5 18.25 9.5 17.5C9.5 16.75 10.5 16 12 16C13.5 16 14.5 16.75 14.5 17.5C14.5 18.25 13.5 19 12 19Z" fill="currentColor"/>',
  facialHairColor: '<circle cx="12" cy="12" r="8" fill="currentColor" opacity="0.5"/><path d="M12 14C9.5 14 7.5 15.5 7.5 17.5C7.5 19.5 9.5 21 12 21C14.5 21 16.5 19.5 16.5 17.5C16.5 15.5 14.5 14 12 14Z" fill="currentColor"/>',
  eyes: '<path d="M12 4.5C7 4.5 2.73 7.61 1 12C2.73 16.39 7 19.5 12 19.5C17 19.5 21.27 16.39 23 12C21.27 7.61 17 4.5 12 4.5ZM12 17C9.24 17 7 14.76 7 12C7 9.24 9.24 7 12 7C14.76 7 17 9.24 17 12C17 14.76 14.76 17 12 17ZM12 9C10.34 9 9 10.34 9 12C9 13.66 10.34 15 12 15C13.66 15 15 13.66 15 12C15 10.34 13.66 9 12 9Z" fill="currentColor"/>',
  eyebrows: '<path d="M6 10C6 10 7.5 8 11 8C14.5 8 16 10 16 10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
  mouth: '<path d="M7 14C7 14 9 17 12 17C15 17 17 14 17 14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>',
  accessories: '<path d="M12 6C8 6 6 8 6 10V14H18V10C18 8 16 6 12 6ZM5 10H4V14H5V10ZM20 10H19V14H20V10Z" fill="currentColor"/>',
  clothing: '<path d="M12 3L4 7V21H20V7L12 3ZM12 9.5C10.62 9.5 9.5 8.38 9.5 7C9.5 6.81 9.53 6.63 9.58 6.46L12 5.25L14.42 6.46C14.47 6.63 14.5 6.81 14.5 7C14.5 8.38 13.38 9.5 12 9.5Z" fill="currentColor"/>',
  clothesColor: '<path d="M12 3L4 7V21H20V7L12 3Z" fill="currentColor" opacity="0.5"/>',
};

class AvatarGenerator extends HTMLElement {
  constructor() {
    super();
    this.attachShadow({ mode: 'open' });
    this.state = {
      // Left Column (6 items)
      skinColor: 0,
      top: 0,
      hatColor: 0,
      hairColor: 0,
      facialHair: 0,
      facialHairColor: 0,

      // Right Column (6 items)
      eyes: 0,
      eyebrows: 0,
      mouth: 0,
      accessories: 0,
      clothing: 0,
      clothesColor: 0,
    };
    this._currentDataUri = '';
  }

  connectedCallback() {
    this.render();
    this.updateAvatar();
    this.addEventListeners();
  }

  get value() {
    return this._currentDataUri;
  }

  generateAvatar() {
    const config = {};
    Object.keys(this.state).forEach(key => {
      if (options[key]) {
        config[key] = [options[key][this.state[key]]];
      }
    });

    return createAvatar(avataaars, {
      seed: 'payak-seed',
      ...config,
      size: 128,
      accessoriesProbability: 100,
      facialHairProbability: 100,
    });
  }

  updateAvatar() {
    const avatar = this.generateAvatar();
    const svgString = avatar.toString();
    this._currentDataUri = `data:image/svg+xml;utf8,${encodeURIComponent(svgString)}`;

    const container = this.shadowRoot.querySelector('#avatar-preview');
    if (container) {
      container.innerHTML = svgString;
    }

    this.setAttribute('value', this._currentDataUri);
    this.dispatchEvent(new CustomEvent('avatar-generated', {
      detail: { dataUri: this._currentDataUri },
      bubbles: true,
      composed: true
    }));
  }

  handleControl(category, direction) {
    if (!options[category]) return;

    const currentIndex = this.state[category];
    const length = options[category].length;
    let newIndex;

    if (direction === 'next') {
      newIndex = (currentIndex + 1) % length;
    } else {
      newIndex = (currentIndex - 1 + length) % length;
    }

    this.state[category] = newIndex;
    this.updateAvatar();
  }

  addEventListeners() {
    this.shadowRoot.addEventListener('click', (e) => {
      const btn = e.target.closest('.nav-btn');
      if (btn) {
        e.preventDefault();
        e.stopPropagation();

        const category = btn.dataset.category;
        const action = btn.dataset.action;

        if (category && action) {
          this.handleControl(category, action);
        }
      }
    });
  }

  render() {
    // 12 items total: Splitting perfectly into 6 and 6
    const keys = Object.keys(this.state);
    const mid = Math.ceil(keys.length / 2); // 12 / 2 = 6
    const leftCats = keys.slice(0, mid);
    const rightCats = keys.slice(mid);

    const createControlRow = (cat) => `
      <div class="control-row">
        <button type="button" class="nav-btn" data-category="${cat}" data-action="prev">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
        </button>
        <div class="icon-label" title="${cat}">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                ${icons[cat] || '<circle cx="12" cy="12" r="10" />'}
            </svg>
        </div>
        <button type="button" class="nav-btn" data-category="${cat}" data-action="next">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
        </button>
      </div>
    `;

    this.shadowRoot.innerHTML = `
      <style>
        :host {
          display: block;
          font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
          max-width: 600px;
          margin: 0 auto;
        }
        .container {
          display: flex;
          align-items: center;
          justify-content: center;
          gap: 20px;
          padding: 10px 0;
        }
        .column {
          display: flex;
          flex-direction: column;
          gap: 8px;
        }
        #avatar-preview {
          width: 140px;
          height: 140px;
          border-radius: 50%;
          overflow: hidden;
          background: #f1f5f9;
          border: 4px solid white;
          box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
          flex-shrink: 0;
          display: flex;
          align-items: center;
          justify-content: center;
        }
        .control-row {
          display: flex;
          align-items: center;
          gap: 6px;
          background: #f8fafc;
          padding: 4px;
          border-radius: 8px;
          border: 1px solid #e2e8f0;
        }
        .icon-label {
          width: 28px;
          height: 28px;
          display: flex;
          align-items: center;
          justify-content: center;
          color: #64748b;
        }
        .nav-btn {
          background: white;
          border: 1px solid #cbd5e1;
          border-radius: 6px;
          cursor: pointer;
          width: 24px;
          height: 24px;
          display: flex;
          align-items: center;
          justify-content: center;
          color: #334155;
          transition: all 0.1s;
          padding: 0;
        }
        .nav-btn:hover {
          background: #e2e8f0;
          color: black;
        }
        .nav-btn:active {
          transform: translateY(1px);
        }
        @media (max-width: 500px) {
          .container { flex-direction: column; }
          .column { flex-direction: row; flex-wrap: wrap; justify-content: center; }
        }
      </style>

      <div class="wrapper">
        <div class="container">
          <div class="column">
            ${leftCats.map(createControlRow).join('')}
          </div>

          <div id="avatar-preview"></div>

          <div class="column">
            ${rightCats.map(createControlRow).join('')}
          </div>
        </div>
      </div>
    `;
  }
}

customElements.define('avatar-generator', AvatarGenerator);
