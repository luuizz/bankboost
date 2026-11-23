import 'aos/dist/aos.css';
import AOS from 'aos';
import { initFaqSearch } from './faq';
import { initMenu } from './menu';
import { initSimulators } from './simulators';
import { initSliders } from './sliders';

document.addEventListener('DOMContentLoaded', () => {
    initMenu();
    initSliders();
    initSimulators();
    initFaqSearch();

    AOS.init({
        duration: 800,
        once: true,
        offset: 60,
        disable: () => window.matchMedia('(prefers-reduced-motion: reduce)').matches,
    });
});
