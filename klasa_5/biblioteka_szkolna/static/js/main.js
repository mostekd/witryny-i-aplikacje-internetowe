document.addEventListener('DOMContentLoaded', function() {
	// Rotacja banera - przełączanie klas .active
	(function rotateBanner() {
		const slides = document.querySelectorAll('.banner .slide');
		if (!slides || slides.length <= 1) return;
		let idx = 0;
		setInterval(() => {
			slides[idx].classList.remove('active');
			idx = (idx + 1) % slides.length;
			slides[idx].classList.add('active');
		}, 4000);
	})();

	// Prosty kalendarz - pokazuje aktualny miesiąc
	(function renderCalendar() {
		const cal = document.getElementById('calendar');
		if (!cal) return;
		const now = new Date();
		const monthNames = ['Styczeń','Luty','Marzec','Kwiecień','Maj','Czerwiec','Lipiec','Sierpień','Wrzesień','Październik','Listopad','Grudzień'];
		const days = ['Nd','Pn','Wt','Śr','Cz','Pt','Sb'];
		let html = '<div class="mini-cal">';
		html += `<div class="cal-header"><strong>${monthNames[now.getMonth()]}</strong> ${now.getFullYear()}</div>`;
		html += '<div class="cal-grid">';
		days.forEach(d => html += `<div class="cal-day cal-day-name">${d}</div>`);
		const first = new Date(now.getFullYear(), now.getMonth(), 1);
		const startOffset = (first.getDay() + 6) % 7; // shift so Monday=0
		for (let i=0;i<startOffset;i++) html += '<div class="cal-day empty"></div>';
		const last = new Date(now.getFullYear(), now.getMonth()+1, 0).getDate();
		for (let d=1; d<=last; d++) {
			const cls = d === now.getDate() ? 'cal-day today' : 'cal-day';
			html += `<div class="${cls}">${d}</div>`;
		}
		html += '</div></div>';
		cal.innerHTML = html;
	})();

	// mobile nav toggle
	(function navToggle(){
		const btn = document.getElementById('nav-toggle');
		const links = document.querySelector('.nav-links');
		if (!btn || !links) return;
		btn.style.display = 'none';
		// show toggle on small screens (in case css not applied yet)
		function update(){ if (window.innerWidth <= 992) { btn.style.display='block'; links.classList.remove('open'); } else { btn.style.display='none'; links.classList.remove('open'); } }
		update(); window.addEventListener('resize', update);
		btn.addEventListener('click', function(){ links.classList.toggle('open'); });
	})();
});
