// Klasa do obsługi kalendarza
class Calendar {
    constructor(containerId, userId) {
        this.container = document.getElementById(containerId);
        this.userId = userId;
        this.currentDate = new Date();
        this.events = [];
        this.init();
    }

    async init() {
        await this.loadEvents();
        this.render();
        this.addEventListeners();
    }

    async loadEvents() {
        try {
            const response = await fetch(`../strona/api/calendar_events.php?user_id=${this.userId}`);
            this.events = await response.json();
        } catch (error) {
            console.error('Błąd podczas ładowania wydarzeń:', error);
        }
    }

    async addEvent(eventData) {
        try {
            const response = await fetch('../strona/api/calendar_events.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(eventData)
            });
            if (response.ok) {
                await this.loadEvents();
                this.render();
            }
        } catch (error) {
            console.error('Błąd podczas dodawania wydarzenia:', error);
        }
    }

    getDaysInMonth(year, month) {
        return new Date(year, month + 1, 0).getDate();
    }

    getFirstDayOfMonth(year, month) {
        return new Date(year, month, 1).getDay();
    }

    render() {
        const year = this.currentDate.getFullYear();
        const month = this.currentDate.getMonth();
        const daysInMonth = this.getDaysInMonth(year, month);
        const firstDay = this.getFirstDayOfMonth(year, month);

        const monthNames = ['Styczeń', 'Luty', 'Marzec', 'Kwiecień', 'Maj', 'Czerwiec', 
                          'Lipiec', 'Sierpień', 'Wrzesień', 'Październik', 'Listopad', 'Grudzień'];

        let html = `
            <div class="calendar-header">
                <button class="prev-month">&lt;</button>
                <h3>${monthNames[month]} ${year}</h3>
                <button class="next-month">&gt;</button>
            </div>
            <div class="calendar-grid">
                <div class="weekday">Pn</div>
                <div class="weekday">Wt</div>
                <div class="weekday">Śr</div>
                <div class="weekday">Cz</div>
                <div class="weekday">Pt</div>
                <div class="weekday">Sb</div>
                <div class="weekday">Nd</div>
        `;

        // Wypełnienie pustymi miejscami przed pierwszym dniem miesiąca
        for (let i = 0; i < firstDay; i++) {
            html += '<div class="day empty"></div>';
        }

        // Dni miesiąca
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            const dayEvents = this.events.filter(event => {
                const eventDate = new Date(event.data_rozpoczecia);
                return eventDate.toDateString() === date.toDateString();
            });

            let eventDots = '';
            if (dayEvents.length > 0) {
                eventDots = dayEvents.map(event => 
                    `<span class="event-dot" style="background-color: ${event.kolor}" 
                     title="${event.tytul}"></span>`
                ).join('');
            }

            html += `
                <div class="day${date.toDateString() === new Date().toDateString() ? ' today' : ''}" 
                     data-date="${date.toISOString().split('T')[0]}">
                    ${day}
                    <div class="event-dots">${eventDots}</div>
                </div>
            `;
        }

        html += '</div>';
        if (this.userId) {
            html += `
                <button class="add-event-btn">Dodaj wydarzenie</button>
                <div class="event-form" style="display: none;">
                    <input type="text" id="event-title" placeholder="Tytuł wydarzenia">
                    <textarea id="event-desc" placeholder="Opis"></textarea>
                    <input type="datetime-local" id="event-start">
                    <input type="datetime-local" id="event-end">
                    <input type="color" id="event-color" value="#3949ab">
                    <button class="save-event-btn">Zapisz</button>
                </div>
            `;
        }

        this.container.innerHTML = html;
    }

    addEventListeners() {
        this.container.addEventListener('click', e => {
            if (e.target.classList.contains('prev-month')) {
                this.currentDate.setMonth(this.currentDate.getMonth() - 1);
                this.render();
            } else if (e.target.classList.contains('next-month')) {
                this.currentDate.setMonth(this.currentDate.getMonth() + 1);
                this.render();
            } else if (e.target.classList.contains('add-event-btn')) {
                const form = this.container.querySelector('.event-form');
                form.style.display = form.style.display === 'none' ? 'block' : 'none';
            } else if (e.target.classList.contains('save-event-btn')) {
                const title = document.getElementById('event-title').value;
                const desc = document.getElementById('event-desc').value;
                const start = document.getElementById('event-start').value;
                const end = document.getElementById('event-end').value;
                const color = document.getElementById('event-color').value;

                if (title && start) {
                    this.addEvent({
                        userId: this.userId,
                        title,
                        description: desc,
                        startDate: start,
                        endDate: end || start,
                        color
                    });
                }
            }
        });
    }
}

// Klasa do obsługi pogody
class WeatherWidget {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        this.apiKey = 'YOUR_OPENWEATHERMAP_API_KEY'; // Należy zastąpić własnym kluczem API
        this.init();
    }

    async init() {
        try {
            const position = await this.getUserLocation();
            const weather = await this.getWeatherData(position.coords.latitude, position.coords.longitude);
            this.render(weather);
        } catch (error) {
            console.error('Błąd podczas pobierania pogody:', error);
            this.container.innerHTML = 'Nie udało się pobrać pogody';
        }
    }

    getUserLocation() {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                reject(new Error('Geolokalizacja nie jest wspierana'));
                return;
            }

            navigator.geolocation.getCurrentPosition(resolve, reject);
        });
    }

    async getWeatherData(lat, lon) {
        const response = await fetch(
            `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&appid=${this.apiKey}&units=metric&lang=pl`
        );
        return await response.json();
    }

    getWeatherIcon(iconCode) {
        return `https://openweathermap.org/img/wn/${iconCode}@2x.png`;
    }

    render(data) {
        const temp = Math.round(data.main.temp);
        const feelsLike = Math.round(data.main.feels_like);
        const description = data.weather[0].description;
        const icon = this.getWeatherIcon(data.weather[0].icon);
        const humidity = data.main.humidity;
        const windSpeed = Math.round(data.wind.speed * 3.6); // m/s na km/h

        this.container.innerHTML = `
            <div class="weather-widget">
                <div class="weather-main">
                    <img src="${icon}" alt="${description}" class="weather-icon">
                    <div class="weather-temp">${temp}°C</div>
                </div>
                <div class="weather-details">
                    <div class="weather-desc">${description}</div>
                    <div>Odczuwalna: ${feelsLike}°C</div>
                    <div>Wilgotność: ${humidity}%</div>
                    <div>Wiatr: ${windSpeed} km/h</div>
                </div>
            </div>
        `;
    }
}

// Inicjalizacja widżetów po załadowaniu strony
document.addEventListener('DOMContentLoaded', function() {
    // Inicjalizacja kalendarza
    if (document.getElementById('calendar')) {
        const userId = document.getElementById('calendar').dataset.userId;
        new Calendar('calendar', userId);
    }
    
    // Inicjalizacja pogody
    if (document.getElementById('weather')) {
        new WeatherWidget('weather');
    }
});