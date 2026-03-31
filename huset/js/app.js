document.addEventListener('DOMContentLoaded', () => {
    // Burger menu toggle
    const burgerBtn = document.getElementById('burger-btn');
    const menu = document.getElementById('mobile-menu');
    if (burgerBtn && menu) {
        burgerBtn.addEventListener('click', () => {
            menu.classList.remove('hidden');
            const expanded = burgerBtn.getAttribute('aria-expanded') === 'true';
            burgerBtn.setAttribute('aria-expanded', expanded ? 'false' : 'true');
        });
    }

    // Collapsible submenus (desktop and mobile)
    document.querySelectorAll('.submenu-toggle').forEach(toggleDiv => {
        toggleDiv.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const menuId = toggleDiv.getAttribute('data-menu');
            const submenu = document.getElementById(menuId);
            if (submenu) {
                submenu.classList.remove('hidden');
                // Close all other submenus first
                document.querySelectorAll('.submenu-toggle').forEach(otherToggle => {
                    const otherMenuId = otherToggle.getAttribute('data-menu');
                    const otherSubmenu = document.getElementById(otherMenuId);
                    if (otherToggle !== toggleDiv && otherSubmenu) {
                        otherToggle.setAttribute('aria-expanded', 'false');
                        otherSubmenu.classList.add('hidden');
                    }
                });
                // Toggle this submenu
                const expanded = toggleDiv.getAttribute('aria-expanded') === 'true';
                toggleDiv.setAttribute('aria-expanded', expanded ? 'false' : 'true');
            }
        });
    });

    // Role page: hide other role cards when editing one
    document.querySelectorAll('.role-div').forEach((currentDiv) => {
        currentDiv.addEventListener('click', () => {
            document.querySelectorAll('.role-div').forEach((div) => {
                if (div !== currentDiv) {
                    div.style.display = 'none';
                }
            });
        });
    });
});
