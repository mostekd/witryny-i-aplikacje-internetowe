// weather.js - simple weather widget using open-meteo.com (no API key)
// Renders into element with id="weather"
(function(){
  const el = document.getElementById('weather');
  if (!el) return;

  const DEFAULT = {lat:54.3520, lon:18.6466, name: 'Gdańsk'}; // default coordinates

  function renderLoading(){
    el.innerHTML = '<div class="w-loading">Ładowanie pogody…</div>';
  }

  function formatTemp(t){ return Math.round(t) + '°C'; }

  function getIcon(code){
    // very small mapping for open-meteo weathercode
    if (code === 0) return '☀️';
    if (code <= 3) return '🌤️';
    if (code <= 48) return '🌫️';
    if (code <= 67) return '🌧️';
    if (code <= 77) return '🌨️';
    if (code <= 99) return '⛈️';
    return '🌡️';
  }

  async function fetchWeather(lat, lon){
    const url = `https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current_weather=true&daily=temperature_2m_max,temperature_2m_min,weathercode&timezone=auto`;
    const resp = await fetch(url);
    if (!resp.ok) throw new Error('weather fetch failed');
    return resp.json();
  }

  function render(data, placeName){
    const cur = data.current_weather;
    const daily = data.daily;
    const html = `
      <div class="weather-card">
        <div class="weather-top">
          <div class="w-place">${placeName}</div>
          <div class="w-now">${getIcon(cur.weathercode)} <span class="w-temp">${formatTemp(cur.temperature)}</span></div>
        </div>
        <div class="weather-forecast">
          ${daily.time.slice(0,3).map((d, i)=>{
            return `<div class="w-day"><div class="w-day-date">${d}</div><div class="w-day-range">${formatTemp(daily.temperature_2m_min[i])} / ${formatTemp(daily.temperature_2m_max[i])}</div></div>`
          }).join('')}
        </div>
      </div>
    `;
    el.innerHTML = html;
  }

  function handleError(err){
    el.innerHTML = '<div class="w-error">Nie można pobrać pogody</div>';
    console.error(err);
  }

  async function init(){
    renderLoading();
    // try geolocation
    if (navigator.geolocation){
      navigator.geolocation.getCurrentPosition(async pos => {
        try{
          const lat = pos.coords.latitude;
          const lon = pos.coords.longitude;
          const data = await fetchWeather(lat, lon);
          render(data, 'Twoja lokalizacja');
        }catch(e){ handleError(e); }
      }, async () => {
        // fallback
        try{ const data = await fetchWeather(DEFAULT.lat, DEFAULT.lon); render(data, DEFAULT.name); }catch(e){ handleError(e); }
      }, {timeout:8000});
    } else {
      try{ const data = await fetchWeather(DEFAULT.lat, DEFAULT.lon); render(data, DEFAULT.name); }catch(e){ handleError(e); }
    }
  }

  // run
  document.addEventListener('DOMContentLoaded', init);
})();
