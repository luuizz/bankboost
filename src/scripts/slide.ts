import Swiper from "swiper";
import { Pagination } from "swiper/modules";
import "swiper/swiper-bundle.css";
import { SwiperOptions } from "swiper/types";

const slides: SwiperOptions = {
  modules: [Pagination],
  slidesPerView: 3,
  spaceBetween: 32,
  grabCursor: true,
  pagination: {
    el: ".s-testimonials .title .swiper-pagination",
    clickable: true,
  },
  breakpoints: {
    320: {
      slidesPerView: 1.3,
      spaceBetween: 17,
    },
    768: {
      slidesPerView: 2.1,
      spaceBetween: 20,
    },
    1024: {
      slidesPerView: 3,
      spaceBetween: 32,
    },
  },
};

export const slidesTestimonials = new Swiper(".slide-testimonials", slides);
