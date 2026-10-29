document.addEventListener('DOMContentLoaded', () => {
    // Mobile menu
    const button = document.getElementById('menu-button');
    const menu = document.getElementById('mobile-menu');

    button?.addEventListener('click', () => {
        const isHidden = menu.classList.toggle('hidden');
        button.setAttribute('aria-expanded', String(!isHidden));
    });

    menu?.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => menu.classList.add('hidden'));
    });

    // Reveal on scroll
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.12 }
    );

    document.querySelectorAll('.reveal').forEach((el) => observer.observe(el));
});