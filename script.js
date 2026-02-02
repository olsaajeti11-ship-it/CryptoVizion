const exploreBtn = document.getElementById('exploreBtn');
if (exploreBtn) {
    exploreBtn.addEventListener('click', () => {
        document.getElementById('market').scrollIntoView({ behavior: 'smooth' });
    });
}

document.querySelectorAll('nav a[href^="#"]').forEach(link => {
    link.addEventListener('click', (e) => {
        const targetId = link.getAttribute('href');
        if (!targetId || targetId === '#') {
            return;
        }
        e.preventDefault();
        const target = document.querySelector(targetId);
        if (target) {
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});

const scrollTopBtn = document.getElementById('scrollTop');
if (scrollTopBtn) {
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 300) {
            scrollTopBtn.classList.add('visible');
        } else {
            scrollTopBtn.classList.remove('visible');
        }
    });

    scrollTopBtn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    });
});

const animateStats = () => {
    const statsSection = document.querySelector('.stats');
    if (!statsSection) {
        return;
    }
    const statsPosition = statsSection.getBoundingClientRect().top;
    const screenPosition = window.innerHeight;
    if (statsPosition < screenPosition) {
        statsSection.style.opacity = '1';
        statsSection.style.transform = 'translateY(0)';
    }
};
window.addEventListener('scroll', animateStats);

function slideInCards() {
    const featureCards = document.querySelectorAll('.features-grid .feature-card');
    const whyCards = document.querySelectorAll('.why-grid .why-card');
    const allCards = [...featureCards, ...whyCards];
    allCards.forEach((card, index) => {
        const cardTop = card.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;
        if (cardTop < windowHeight - 100) {
            setTimeout(() => { card.classList.add('visible'); }, index * 150);
        }
    });
}
window.addEventListener('scroll', slideInCards);
window.addEventListener('load', slideInCards);
