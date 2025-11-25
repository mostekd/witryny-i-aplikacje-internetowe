// ============= BANNER / CAROUSEL =============
class BannerCarousel {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        this.banners = document.querySelectorAll('.banner');
        this.dots = document.querySelectorAll('.banner-dot');
        this.currentIndex = 0;
        this.autoPlayInterval = null;
        
        if (this.banners.length > 0) {
            this.init();
        }
    }
    
    init() {
        // Ustawienie pierwszego bannera
        this.showBanner(0);
        
        // Event listenery dla przycisków
        const prevBtn = document.querySelector('.banner-controls-arrow.prev');
        const nextBtn = document.querySelector('.banner-controls-arrow.next');
        
        if (prevBtn) prevBtn.addEventListener('click', () => this.prev());
        if (nextBtn) nextBtn.addEventListener('click', () => this.next());
        
        // Event listenery dla kropek
        this.dots.forEach((dot, index) => {
            dot.addEventListener('click', () => this.showBanner(index));
        });
        
        // Auto play
        this.startAutoPlay();
        
        // Zatrzymanie przy hover
        if (this.container) {
            this.container.addEventListener('mouseenter', () => this.stopAutoPlay());
            this.container.addEventListener('mouseleave', () => this.startAutoPlay());
        }
    }
    
    showBanner(index) {
        this.currentIndex = (index + this.banners.length) % this.banners.length;
        
        // Ukrywanie wszystkich banerów
        this.banners.forEach(banner => banner.classList.remove('active'));
        this.dots.forEach(dot => dot.classList.remove('active'));
        
        // Pokazanie bieżącego bannera
        if (this.banners[this.currentIndex]) {
            this.banners[this.currentIndex].classList.add('active');
        }
        
        if (this.dots[this.currentIndex]) {
            this.dots[this.currentIndex].classList.add('active');
        }
    }
    
    next() {
        this.showBanner(this.currentIndex + 1);
        this.resetAutoPlay();
    }
    
    prev() {
        this.showBanner(this.currentIndex - 1);
        this.resetAutoPlay();
    }
    
    startAutoPlay() {
        this.autoPlayInterval = setInterval(() => {
            this.showBanner(this.currentIndex + 1);
        }, 5000); // Zmiana co 5 sekund
    }
    
    stopAutoPlay() {
        clearInterval(this.autoPlayInterval);
    }
    
    resetAutoPlay() {
        this.stopAutoPlay();
        this.startAutoPlay();
    }
}

// Inicjalizacja bannera
document.addEventListener('DOMContentLoaded', () => {
    new BannerCarousel('banner-container');
});

// ============= KALENDARZ =============
function generateCalendar() {
    const today = new Date();
    const year = today.getFullYear();
    const month = today.getMonth();
    const monthNames = ['Styczeń', 'Luty', 'Marzec', 'Kwiecień', 'Maj', 'Czerwiec',
                        'Lipiec', 'Sierpień', 'Wrzesień', 'Październik', 'Listopad', 'Grudzień'];
    
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    
    let calendarHTML = `<h4>${monthNames[month]} ${year}</h4>
                       <table>
                           <thead>
                               <tr>
                                   <th>Pn</th><th>Wt</th><th>Śr</th><th>Cz</th><th>Pt</th><th>So</th><th>Ni</th>
                               </tr>
                           </thead>
                           <tbody><tr>`;
    
    // Puste komórki przed pierwszym dniem miesiąca
    for (let i = 1; i < firstDay; i++) {
        calendarHTML += '<td></td>';
    }
    
    // Dni miesiąca
    let dayCount = firstDay;
    for (let day = 1; day <= daysInMonth; day++) {
        const isToday = day === today.getDate() ? ' class="today"' : '';
        calendarHTML += `<td${isToday}>${day}</td>`;
        
        dayCount++;
        if (dayCount > 6) {
            calendarHTML += '</tr><tr>';
            dayCount = 0;
        }
    }
    
    // Uzupełnienie ostatniego rzędu
    while (dayCount > 0 && dayCount <= 6) {
        calendarHTML += '<td></td>';
        dayCount++;
    }
    
    calendarHTML += '</tr></tbody></table>';
    return calendarHTML;
}

// Funkcja do wstawienia kalendarza w widget
function initCalendar() {
    const calendarContainer = document.querySelector('.calendar-widget');
    if (calendarContainer) {
        calendarContainer.innerHTML = generateCalendar();
    }
}

// ============= POGODA (Weather API) =============
function getWeather(lat = 54.37, lon = 18.64) {
    // Domyślne współrzędne Gdańska
    const apiKey = 'открытый API (zastąpić własnym kluczem)';
    const url = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&units=metric&lang=pl&appid=${apiKey}`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            const weatherContainer = document.querySelector('.weather-widget');
            if (weatherContainer) {
                const weatherIcon = getWeatherIcon(data.weather[0].main);
                weatherContainer.innerHTML = `
                    <h4>Pogoda w Gdańsku</h4>
                    <div class="weather-icon">${weatherIcon}</div>
                    <p><strong>${data.main.temp}°C</strong></p>
                    <p>${data.weather[0].description}</p>
                    <p>Wiatr: ${data.wind.speed} m/s</p>
                `;
            }
        })
        .catch(error => {
            console.log('Błąd pobierania pogody:', error);
            // Fallback - pokazanie informacji offline
            const weatherContainer = document.querySelector('.weather-widget');
            if (weatherContainer) {
                weatherContainer.innerHTML = `
                    <h4>Pogoda</h4>
                    <div class="weather-icon">⛅</div>
                    <p>Informacja niedostępna</p>
                `;
            }
        });
}

function getWeatherIcon(weatherType) {
    const icons = {
        'Clear': '☀️',
        'Clouds': '☁️',
        'Rain': '🌧️',
        'Snow': '❄️',
        'Thunderstorm': '⛈️',
        'Mist': '🌫️',
        'Smoke': '💨',
        'Haze': '🌫️',
        'Dust': '🌪️',
        'Fog': '🌫️',
        'Sand': '🌪️',
        'Ash': '💨',
        'Squall': '💨',
        'Tornado': '🌪️',
        'Drizzle': '🌦️'
    };
    
    return icons[weatherType] || '🌡️';
}

// ============= INICJALIZACJA ============= 
document.addEventListener('DOMContentLoaded', () => {
    initCalendar();
    getWeather();
    
    // Aktualizacja pogody co 30 minut
    setInterval(getWeather, 30 * 60 * 1000);
});

// ============= FORMULARZ - VALIDACJA ============= 
class FormValidator {
    constructor(formId) {
        this.form = document.getElementById(formId);
        this.rules = {};
        
        if (this.form) {
            this.init();
        }
    }
    
    init() {
        this.form.addEventListener('submit', (e) => this.validate(e));
    }
    
    addRule(fieldName, rule) {
        if (!this.rules[fieldName]) {
            this.rules[fieldName] = [];
        }
        this.rules[fieldName].push(rule);
    }
    
    validate(e) {
        let isValid = true;
        const formData = new FormData(this.form);
        
        for (const [fieldName, value] of formData.entries()) {
            if (this.rules[fieldName]) {
                for (const rule of this.rules[fieldName]) {
                    if (!rule.test(value)) {
                        this.showError(fieldName, rule.message);
                        isValid = false;
                    }
                }
            }
        }
        
        if (!isValid) {
            e.preventDefault();
        }
        
        return isValid;
    }
    
    showError(fieldName, message) {
        const field = this.form.elements[fieldName];
        if (field) {
            field.style.borderColor = '#e74c3c';
            field.title = message;
        }
    }
    
    clearError(fieldName) {
        const field = this.form.elements[fieldName];
        if (field) {
            field.style.borderColor = '';
            field.title = '';
        }
    }
}

// ============= FUNKCJE POMOCNICZE ============= 
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.textContent = message;
    
    const container = document.querySelector('main') || document.body;
    container.insertBefore(notification, container.firstChild);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Funkcja do potwierdzenia usunięcia
function confirmDelete(message = 'Czy na pewno chcesz usunąć ten element?') {
    return confirm(message);
}

// Format daty
function formatDate(date) {
    const d = new Date(date);
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    
    return `${day}.${month}.${year} ${hours}:${minutes}`;
}
