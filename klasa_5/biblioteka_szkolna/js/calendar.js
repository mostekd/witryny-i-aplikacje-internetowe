// calendar.js - simple client-side month calendar widget
(function(){
  const el = document.getElementById('calendar');
  if (!el) return;

  const monthNames = ['Styczeń','Luty','Marzec','Kwiecień','Maj','Czerwiec','Lipiec','Sierpień','Wrzesień','Październik','Listopad','Grudzień'];
  const weekDays = ['Pn','Wt','Śr','Cz','Pt','Sb','Nd'];

  let viewDate = new Date();

  function clear(node){ while(node.firstChild) node.removeChild(node.firstChild); }

  function build(){
    clear(el);

    const header = document.createElement('div'); header.className = 'cal-header';
    const prev = document.createElement('button'); prev.className='cal-btn cal-prev'; prev.type='button'; prev.textContent='‹';
    const next = document.createElement('button'); next.className='cal-btn cal-next'; next.type='button'; next.textContent='›';
    const title = document.createElement('div'); title.className='cal-title'; title.textContent = monthNames[viewDate.getMonth()] + ' ' + viewDate.getFullYear();
    const todayBtn = document.createElement('button'); todayBtn.className='cal-btn cal-today'; todayBtn.type='button'; todayBtn.textContent='Dziś';

    header.appendChild(prev); header.appendChild(title); header.appendChild(todayBtn); header.appendChild(next);

    el.appendChild(header);

    const grid = document.createElement('div'); grid.className='cal-grid';
    // weekday headers (start Monday)
    weekDays.forEach(d=>{ const w = document.createElement('div'); w.className='cal-weekday'; w.textContent=d; grid.appendChild(w); });

    const first = new Date(viewDate.getFullYear(), viewDate.getMonth(), 1);
    const last = new Date(viewDate.getFullYear(), viewDate.getMonth()+1, 0);

    // determine Monday-start index
    let startOffset = (first.getDay() + 6) % 7; // 0=Mon
    // previous month's tail
    const prevLast = new Date(viewDate.getFullYear(), viewDate.getMonth(), 0).getDate();
    for(let i=0;i<startOffset;i++){
      const d = prevLast - startOffset + 1 + i;
      const cell = document.createElement('div'); cell.className='cal-day other-month'; cell.textContent = d; grid.appendChild(cell);
    }

    for(let d=1; d<=last.getDate(); d++){
      const cell = document.createElement('div'); cell.className='cal-day'; cell.textContent = d;
      const cellDate = new Date(viewDate.getFullYear(), viewDate.getMonth(), d);
      const today = new Date();
      if (cellDate.toDateString() === today.toDateString()) cell.classList.add('today');
      grid.appendChild(cell);
    }

    // next month's head to fill grid to full weeks
    const totalCells = startOffset + last.getDate();
    const tail = (7 - (totalCells % 7)) % 7;
    for(let i=1;i<=tail;i++){ const cell = document.createElement('div'); cell.className='cal-day other-month'; cell.textContent = i; grid.appendChild(cell); }

    el.appendChild(grid);

    // events
    prev.addEventListener('click', ()=>{ viewDate = new Date(viewDate.getFullYear(), viewDate.getMonth()-1, 1); build(); });
    next.addEventListener('click', ()=>{ viewDate = new Date(viewDate.getFullYear(), viewDate.getMonth()+1, 1); build(); });
    todayBtn.addEventListener('click', ()=>{ viewDate = new Date(); build(); });
  }

  document.addEventListener('DOMContentLoaded', build);
})();
