// Contador regresivo
const globalCountdownDate = new Date("2026-12-15T09:00:00").getTime();

function updateCountdown() {
    const now = new Date().getTime();
    const distance = globalCountdownDate - now;

    const daysElem = document.getElementById("days");
    const hoursElem = document.getElementById("hours");
    const minutesElem = document.getElementById("minutes");
    const secondsElem = document.getElementById("seconds");

    if (daysElem && hoursElem && minutesElem && secondsElem) {
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        daysElem.innerText = days.toString().padStart(2, '0');
        hoursElem.innerText = hours.toString().padStart(2, '0');
        minutesElem.innerText = minutes.toString().padStart(2, '0');
        secondsElem.innerText = seconds.toString().padStart(2, '0');

        if (distance < 0) {
            clearInterval(globalCountdownInterval);
            daysElem.innerText = "00";
            hoursElem.innerText = "00";
            minutesElem.innerText = "00";
            secondsElem.innerText = "00";
        }
    }
}

updateCountdown();
const globalCountdownInterval = setInterval(updateCountdown, 1000);

// Formulario de registro
const regForm = document.getElementById("registration-form");
if (regForm) {
    regForm.addEventListener("submit", function(e) {
        e.preventDefault();
        const name = document.getElementById("name").value;
        alert(`¡Gracias ${name} por registrarte! Hemos enviado un correo de confirmación con los detalles de tu inscripción.`);
        this.reset();
    });
}

// Navegación suave
document.querySelectorAll('nav a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
            window.scrollTo({
                top: targetElement.offsetTop - 60,
                behavior: 'smooth'
            });
        }
    });
});

// Animación de las barras del gráfico
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const chartBars = document.querySelectorAll('.chart-bar');
        chartBars.forEach(bar => {
            const height = getComputedStyle(bar).getPropertyValue('--final-height');
            bar.style.height = height;
        });
    }, 300);
});

// Notificaciones
function toggleNotif() {
    var dropdown = document.getElementById("notif-dropdown");
    dropdown.classList.toggle("hidden");

    // Llamada para marcar notificaciones como leídas
    if (!dropdown.classList.contains("hidden")) {
        fetch('marcar_notif.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    var notifCountElem = document.getElementById("notif-count");
                    notifCountElem.textContent = "0";
                    notifCountElem.classList.remove("active");
                }
            });
    }
}

// Panel de Admin desplegable
function toggleAdminMenu() {
    document.getElementById("admin-menu").classList.toggle("show");
}

window.onclick = function(event) {
    if (!event.target.matches('.admin-dropdown button')) {
        const dropdowns = document.getElementsByClassName("dropdown-content");
        for (let i = 0; i < dropdowns.length; i++) {
            dropdowns[i].classList.remove("show");
        }
    }
}

// Subida de ponencias vía AJAX
const ponenciaForm = document.querySelector('form[action="guardar_ponencia.php"]');
if (ponenciaForm) {
    ponenciaForm.addEventListener("submit", function (e) {
        e.preventDefault();

        const formData = new FormData(ponenciaForm);
        const messageBox = document.createElement("div");
        messageBox.style.textAlign = "center";
        messageBox.style.padding = "15px";
        messageBox.style.marginTop = "15px";
        messageBox.style.fontWeight = "bold";

        fetch("guardar_ponencia.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            messageBox.textContent = data.message;
            messageBox.style.color = data.success ? "green" : "red";
            ponenciaForm.parentNode.appendChild(messageBox);
            if (data.success) ponenciaForm.reset();
        })
        .catch(err => {
            messageBox.textContent = "❌ Error inesperado al subir la ponencia.";
            messageBox.style.color = "red";
            ponenciaForm.parentNode.appendChild(messageBox);
            console.error(err);
        });
    });
}

