import '../../scss/landing/main.scss';
import 'aos/dist/aos.css';
import AOS from 'aos';
import { initTestimonials } from './slide';

document.addEventListener('DOMContentLoaded', () => {
    initTestimonials();

    AOS.init({
        duration: 1000,
        once: true,
        disable: 'mobile',
    });
});
