"use strict";

document.addEventListener("DOMContentLoaded", function() {
    let container = document.getElementById("container");
    let redirectForm = document.getElementById("redirect-form");
    let inputForm = document.getElementById("input-form");

    if (redirectForm) {
        redirectForm.addEventListener("submit", function (event) {
            if (redirectForm.id === "save-answer-form") {
                event.target.submit();
                return;
            }
            event.preventDefault();

            container.classList.remove("animate__bounceIn");
            container.classList.add("animate__bounceOut");

            setTimeout(function () {
                event.target.submit();
            }, 500);
        });
    }

    if (inputForm) {
        inputForm.addEventListener("submit", function (event) {
            if (inputForm.id === "save-answer-form") {
                event.target.submit();
                return;
            }

            event.preventDefault();

            container.classList.remove("animate__bounceIn");
            container.classList.add("animate__bounceOut");

            setTimeout(function () {
                event.target.submit();
            }, 500);
        });
    }
});
