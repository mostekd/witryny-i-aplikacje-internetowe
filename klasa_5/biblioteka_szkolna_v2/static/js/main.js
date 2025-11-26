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
    
    let firstDay = new Date(year, month, 1).getDay();
    // Konwertuj niedzielę z 0 na 7 (poniedziałek = 1, ..., niedziela = 7)
    firstDay = firstDay === 0 ? 7 : firstDay;
    
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    
    let calendarHTML = `<h4>${monthNames[month]} ${year}</h4>
                       <table>
                           <thead>
                               <tr>
                                   <th>Pn</th><th>Wt</th><th>Śr</th><th>Cz</th><th>Pt</th><th>So</th><th>Ni</th>
                               </tr>
                           </thead>
                           <tbody>
                               <tr>`;
    
    // Puste komórki przed pierwszym dniem miesiąca
    for (let i = 1; i < firstDay; i++) {
        calendarHTML += '<td></td>';
    }
    
    // Dni miesiąca
    let dayCount = firstDay;
    for (let day = 1; day <= daysInMonth; day++) {
        const isToday = day === today.getDate() && month === today.getMonth() ? ' class="today"' : '';
        calendarHTML += `<td${isToday}>${day}</td>`;
        
        dayCount++;
        // Jeśli tydzień się skończył (8 dniami - bo zaczynamy od 1, a 8 oznacza następny poniedziałek)
        if (dayCount > 7) {
            calendarHTML += '</tr><tr>';
            dayCount = 1;
        }
    }
    
    // Uzupełnienie ostatniego rzędu
    while (dayCount > 1 && dayCount <= 7) {
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
function getWeather(lat = null, lon = null) {
    // Jeśli nie podano współrzędnych, pobierz je z GPS przeglądarki
    if (lat === null || lon === null) {
        if ('geolocation' in navigator) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const userLat = position.coords.latitude;
                    const userLon = position.coords.longitude;
                    console.log('GPS pobrana:', userLat, userLon);
                    displayWeatherByCoordinates(userLat, userLon);
                },
                (error) => {
                    console.log('Błąd GPS, kod:', error.code);
                    // Fallback na IP
                    getCityAndCoordinates().then(({ city, latitude, longitude }) => {
                        console.log('Używam IP zamiast GPS - Miasto:', city);
                        displayWeather(latitude, longitude, city);
                    });
                },
                {
                    enableHighAccuracy: true,
                    timeout: 8000,
                    maximumAge: 0
                }
            );
        } else {
            console.log('GPS niedostępne, używam IP');
            getCityAndCoordinates().then(({ city, latitude, longitude }) => {
                console.log('Pobrano z IP - Miasto:', city);
                displayWeather(latitude, longitude, city);
            });
        }
    } else {
        displayWeatherByCoordinates(lat, lon);
    }
}

function displayWeatherByCoordinates(lat, lon) {
    // Pobierz nazwę miasta z GPS współrzędnych
    getCityName(lat, lon).then(cityName => {
        displayWeather(lat, lon, cityName);
    });
}

function getCityAndCoordinates() {
    // Pobierz miasto I współrzędne z IP (ipapi.co)
    return fetch('https://ipapi.co/json/')
        .then(response => response.json())
        .then(data => {
            console.log('Dane z ipapi.co:', data);
            return {
                city: data.city || data.region || 'Nieznana lokalizacja',
                latitude: parseFloat(data.latitude),
                longitude: parseFloat(data.longitude)
            };
        })
        .catch(error => {
            console.error('Błąd pobierania z ipapi.co:', error);
            // Fallback na Gdańsk
            return {
                city: 'Gdańsk',
                latitude: 54.37,
                longitude: 18.64
            };
        });
}

function displayWeather(lat, lon, cityName) {
    // Prawidłowy URL dla Open-Meteo API
    const url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current=temperature_2m,weather_code,wind_speed_10m&temperature_unit=celsius&wind_speed_unit=kmh`;
    
    console.log('Pobieranie pogody z URL:', url);
    
    fetch(url)
        .then(response => {
            console.log('Odpowiedź API, status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Dane pogody otrzymane:', JSON.stringify(data, null, 2));
            
            const weatherContainer = document.querySelector('.weather-widget');
            if (weatherContainer) {
                // Open-Meteo /forecast endpoint zwraca current
                if (data.current) {
                    const current = data.current;
                    const temperature = current.temperature_2m;
                    const windspeed = current.wind_speed_10m;
                    const weatherCode = current.weather_code;
                    
                    console.log('Weather code:', weatherCode, 'Temp:', temperature, 'Wind:', windspeed);
                    
                    const weatherDesc = getWeatherDescription(weatherCode);
                    const weatherIcon = getWeatherIcon(weatherCode);
                    
                    console.log('Weather description:', weatherDesc, 'Icon:', weatherIcon);
                    
                    weatherContainer.innerHTML = `
                        <h4>Pogoda - ${cityName}</h4>
                        <div class="weather-icon">${weatherIcon}</div>
                        <p><strong>Temp: ${temperature}°C</strong></p>
                        <p>Wiatr: ${windspeed} km/h</p>
                        <p>${weatherDesc}</p>
                    `;
                    console.log('Pogoda wyświetlona pomyślnie');
                } else {
                    throw new Error('Brak danych current w odpowiedzi');
                }
            } else {
                console.warn('Kontener .weather-widget nie znaleziony');
            }
        })
        .catch(error => {
            console.error('Błąd pobierania pogody:', error);
            const weatherContainer = document.querySelector('.weather-widget');
            if (weatherContainer) {
                weatherContainer.innerHTML = `
                    <h4>Pogoda</h4>
                    <div class="weather-icon">⛅</div>
                    <p>Błąd: ${error.message}</p>
                `;
            }
        });
}

function getCityName(lat, lon) {
    // Nominatim - tylko dla fallbacku
    return fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
        .then(response => response.json())
        .then(data => {
            const address = data.address || {};
            return address.city || address.town || address.village || `${lat.toFixed(2)}°N, ${lon.toFixed(2)}°E`;
        })
        .catch(error => {
            console.warn('Nominatim nie zadziałał');
            return `${lat.toFixed(2)}°N, ${lon.toFixed(2)}°E`;
        });
}

function getWeatherDescription(code) {
    // WMO Weather interpretation codes
    const codes = {
        0: 'Bezchmurnie',
        1: 'Głównie bezchmurnie',
        2: 'Częściowo pochmurnie',
        3: 'Pochmurnie',
        45: 'Mglisty',
        48: 'Mgła szroniowa',
        51: 'Mżawka leciutka',
        53: 'Mżawka umiarkowana',
        55: 'Mżawka intensywna',
        61: 'Deszcz leciutki',
        63: 'Deszcz umiarkowany',
        65: 'Deszcz intensywny',
        71: 'Śnieg leciutki',
        73: 'Śnieg umiarkowany',
        75: 'Śnieg intensywny',
        77: 'Ziarna śniegu',
        80: 'Przelotne opady',
        81: 'Przelotne opady umiarkowane',
        82: 'Przelotne opady intensywne',
        85: 'Przelotny śnieg',
        86: 'Przelotny śnieg intensywny',
        95: 'Burza',
        96: 'Burza z gradem',
        99: 'Burza z gradem intensywny'
    };
    return codes[code] || 'Nieznane';
}

function getWeatherIcon(weatherCode) {
    // Mapy ikon na podstawie kodów WMO
    if (weatherCode === 0 || weatherCode === 1) return '☀️';
    if (weatherCode === 2 || weatherCode === 3) return '☁️';
    if (weatherCode >= 45 && weatherCode <= 48) return '🌫️';
    if ((weatherCode >= 51 && weatherCode <= 67) || (weatherCode >= 80 && weatherCode <= 82)) return '🌧️';
    if (weatherCode >= 71 && weatherCode <= 77 || weatherCode === 85 || weatherCode === 86) return '❄️';
    if (weatherCode === 80 || weatherCode === 81) return '🌦️';
    if (weatherCode >= 95 && weatherCode <= 99) return '⛈️';
    return '🌡️';
}

// ============= INICJALIZACJA ============= 
document.addEventListener('DOMContentLoaded', () => {
    initCalendar();
    
    // Inicjalizacja pogody z opóźnieniem
    setTimeout(() => {
        getWeather();
    }, 500);
    
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
