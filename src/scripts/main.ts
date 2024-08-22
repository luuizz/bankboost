import "./../scss/main.scss";
import { slidesTestimonials } from "./slide.js";
import AOS from "aos";
import "aos/dist/aos.css";

document.addEventListener("DOMContentLoaded", () => {
  slidesTestimonials.init();
});

AOS.init({
  duration: 1000,
  once: true,
  disable: "mobile",
});
