import Swiper from 'swiper';
import { Autoplay, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/pagination';

export function initSliders(): void {
    document.querySelectorAll<HTMLElement>('[data-slider]').forEach((element) => {
        new Swiper(element, {
            modules: [Pagination, Autoplay],
            slidesPerView: 1.15,
            spaceBetween: 16,
            grabCursor: true,
            autoplay: { delay: 6000, pauseOnMouseEnter: true },
            pagination: { el: element.querySelector<HTMLElement>('.swiper-pagination'), clickable: true },
            breakpoints: {
                640: { slidesPerView: 2.1, spaceBetween: 20 },
                1024: { slidesPerView: 3, spaceBetween: 24 },
            },
        });
    });
}
