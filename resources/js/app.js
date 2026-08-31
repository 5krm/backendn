// import Vimeo from "@vimeo/player";
import Swiper from "swiper";
import "swiper/css";
import Alpine from "alpinejs";
import { initExamNavigation } from './exam';
import { injectSpeedInsights } from '@vercel/speed-insights';

window.Alpine = Alpine;
// Alpine.start() is intentionally omitted — Livewire 3 starts Alpine internally.

const changeAvatarBtn = document.querySelector("#changeAvatarBtn");
const avatarInput = document.querySelector("#avatarInput");

if (changeAvatarBtn !== null && avatarInput !== null) {
    changeAvatarBtn.addEventListener("click", function (e) {
        e.preventDefault();
        avatarInput.click();
    });

    avatarInput.addEventListener("change", function () {
        const form = this.closest("form");
        form.submit();
    });
}

const forms = document.querySelectorAll("form");
forms.forEach((form) => {
    form.addEventListener("submit", function () {
        const actionButton = this.querySelector(".action-button");
        if (actionButton == undefined) return;
        actionButton.disabled = true;
        actionButton.innerHTML =
            '<span class="loading loading-spinner"></span>';
    });
});

// dashboard slider
new Swiper(".courses-swiper", {
    slidesPerView: "auto",
    spaceBetween: 30,
    grabCursor: true,
});

/* drawer  */

const width = window.innerWidth;

if (width > 1024) {
    document.querySelectorAll(".lesson-side-menu").forEach((el) => {
        el.classList.add("lesson-side-menu-open");
    });
}
document.querySelectorAll(".toggle-side-menu").forEach((el) => {
    el.addEventListener("click", function () {
        const parent = findParent(el, "lesson-side-menu");
        parent.classList.toggle("lesson-side-menu-open");
    });
});

document.querySelectorAll("[data-target]").forEach((el) => {
    el.addEventListener("click", () => {
        const target = document.querySelector(el.dataset.target);
        target.classList.toggle("lesson-side-menu-open");
    });
});

function findParent(el, className) {
    while ((el = el.parentNode) && !el.classList.contains(className));
    return el;
}

document.querySelectorAll(".js-choice").forEach((el) => {
    new Choices(el, {
        itemSelectText: "",
    });
});

document.querySelectorAll("form:not([wire\\:submit])").forEach((form) => {
    form.addEventListener("submit", (e) => {
        e.preventDefault();
        console.log("form submitted")
        const btn = form.querySelector("[data-recaptcha]");
        if (btn == null) return form.submit();

        const key = btn.dataset["recaptcha"];
        const action = btn.dataset["action"] ?? "submit";
        grecaptcha.ready(function () {
            grecaptcha
                .execute(key, { action: action })
                .then(function (token) {
                    // Add your logic to submit to your backend server here.
                    const recaptchaInput = document.createElement("input");
                    recaptchaInput.type = "hidden";
                    recaptchaInput.name = "g-recaptcha-response";
                    recaptchaInput.value = token;

                    form.appendChild(recaptchaInput);
                    form.submit();
                })
                .catch(function (error) {
                    console.log("🚀 ~ error:", error);
                });
        });
    });
});

initExamNavigation();

// Initialize Vercel Speed Insights
injectSpeedInsights();
