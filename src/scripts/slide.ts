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
};

export const slidesTestimonials = new Swiper(".slide-testimonials", slides);
