class ProjectBanner extends HTMLElement {
  constructor() {
    super();
  }

  connectedCallback() {
    this.render();
  }

  get name() { return this.getAttribute('name') || 'Untitled Project'; }
  get color() { return this.getAttribute('color') || '#000000'; }
  get members() {
    try { return JSON.parse(this.getAttribute('members') || '[]'); } catch (e) { return []; }
  }

  render() {
    const members = this.members;

    this.innerHTML = `
      <div class="relative w-full h-full bg-gray-900 rounded-t-xl overflow-visible select-none">
        <div 
          class="absolute inset-0 flex flex-col w-full h-full pt-6 items-center text-center text-white overflow-visible rounded-t-xl"
          style="background-color: ${this.color};"
        >
          <span class="z-0 w-fit px-3 py-1 text-xl md:text-2xl font-bold bg-white text-black dark:bg-black dark:text-white shadow-sm uppercase tracking-wider">
            ${this.name}
          </span>
          <svg class="w-12 h-12 mt-2 opacity-20 text-white" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2L2 7l10 5 10-5-10-5zm0 9l2.5-1.25L12 8.5l-2.5 1.25L12 11zm0 2.5l-5-2.5-5 2.5L12 22l10-8.5-5-2.5-5 2.5z"/>
          </svg>
        </div>

        <div class="absolute inset-0 w-full h-full flex items-end justify-center z-10 pointer-events-none">
           <div class="flex items-end justify-center -space-x-4 hover:space-x-1 transition-all duration-300 px-4 w-full h-full pointer-events-auto">
              ${members.map((member, index) => this.renderAvatar(member, index)).join('')}
           </div>
        </div>

      </div>
    `;
  }

  renderAvatar(member, index) {
    const fullName = `${member.first_name} ${member.last_name}`;
    const avatarSrc = member.avatar_url
      ? member.avatar_url
      : `https://api.dicebear.com/9.x/avataaars/svg?seed=${member.id}&backgroundColor=b6e3f4`;

    return `
      <ui-hover-card style="z-index: ${10 + index};" cardId=${Math.random()}>
        <div slot="trigger" class="relative transform transition-all duration-300 hover:translate-y-1 hover:scale-110 origin-bottom">
           <img 
              src="${avatarSrc}" 
              alt="${fullName}"
              class="h-40 w-40 md:h-44 md:w-44 drop-shadow-xl object-contain block"
              onerror="this.style.display='none'"
           >
        </div>

        <div slot="content" class="flex flex-col items-center gap-2 text-center min-w-[140px]">
            <div class="w-10 h-10 rounded-full overflow-hidden border border-gray-200">
                <img src="${avatarSrc}" class="w-full h-full object-cover">
            </div>
            <div>
                <p class="font-bold text-gray-900 dark:text-white text-xs leading-tight">${fullName}</p>
                <p class="text-[10px] text-gray-500 font-mono mt-0.5 bg-gray-100 dark:bg-gray-800 rounded px-1">
                    #${member.faculty_number}
                </p>
            </div>
        </div>

      </ui-hover-card>
    `;
  }
}

customElements.define('project-banner', ProjectBanner);
