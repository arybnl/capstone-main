// dashboard.js

// --- Data
let currentDate = new Date();
let schedules = [
  // sample initial schedules (use real dates as needed)
  // Added a unique ID and workoutType for each schedule
  { id: 'sch1', date: new Date(new Date().setDate(new Date().getDate() + 1)), text: "Yoga at 7:00 AM", workoutType: "yoga" },
  { id: 'sch2', date: new Date(new Date().setDate(new Date().getDate() + 2)), text: "HIIT at 17:00", workoutType: "cardio" },
  { id: 'sch3', date: new Date(new Date().setDate(new Date().getDate() - 1)), text: "Strength Training", workoutType: "weights" },
  { id: 'sch4', date: new Date(), text: "Morning Run", workoutType: "cardio" } // Today's schedule
];

// Example historical data for detailed progress chart
let historicalProgress = {
    labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6'],
    caloriesBurned: [1500, 1800, 1700, 2000, 2200, 2100],
    workoutStreak: [3, 4, 3, 5, 4, 6]
};


// --- Chart (doughnut with center text)
const ctx = document.getElementById('progressPieChart').getContext('2d');
const completedColor = '#9E0A0A';
const remainingColor = '#CC0000';

// Example values; you can set dynamically from data
let completedPct = 70;
let remainingPct = 100 - completedPct;

const doughnut = new Chart(ctx, {
  type: 'doughnut',
  data: {
    labels: ['Completed','Remaining'],
    datasets: [{
      data: [completedPct, remainingPct],
      backgroundColor: [completedColor, remainingColor],
      borderWidth: 0
    }]
  },
  options: {
    maintainAspectRatio: false,
    cutout: '70%',
    plugins: {
      legend: { display: false },
      tooltip: { enabled: false }
    }
  }
});

// set center percentage text
document.getElementById('progressPerc').textContent = `${completedPct}%`;

// --- Calendar rendering
const calendarDates = document.getElementById('calendarDates');
const calendarMonthYear = document.getElementById('calendarMonthYear');
const workoutTypeIcons = {
  'weights': '\uf6f3', // bi-barbell
  'yoga': '\uf489',    // bi-flower1
  'cardio': '\uf4b7',  // bi-speedometer
  'other': '\uf4b6',   // bi-three-dots
  'default': '\uf2dc'  // bi-circle-fill (generic dot)
};

function renderCalendar(date) {
  const year = date.getFullYear();
  const month = date.getMonth();

  calendarMonthYear.textContent = `${date.toLocaleString('default', { month: 'long' })} ${year}`;

  // header days
  const days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
  let html = '';
  days.forEach(d => html += `<div class="calendar-day">${d}</div>`);

  // dates
  const firstDay = new Date(year, month, 1).getDay();
  const lastDate = new Date(year, month+1, 0).getDate();
  const today = new Date();
  for (let i=0;i<firstDay;i++){ html += `<div></div>`; }
  for (let d=1; d<= lastDate; d++){
    const thisDate = new Date(year, month, d);
    const isToday = thisDate.toDateString() === today.toDateString();
    
    // Find all schedules for this date
    const daySchedules = schedules.filter(s => {
      const sDate = new Date(s.date);
      return sDate.getFullYear() === year && sDate.getMonth() === month && sDate.getDate() === d;
    });

    // Determine if there are schedules and what icon to show
    const hasSchedule = daySchedules.length > 0;
    let icon = '';
    if (hasSchedule) {
      // Prioritize icons, or just show a generic if multiple types
      if (daySchedules.length === 1) {
        icon = workoutTypeIcons[daySchedules[0].workoutType] || workoutTypeIcons['default'];
      } else {
        icon = workoutTypeIcons['default']; // Generic dot for multiple schedules
      }
    }

    const classes = [
      'calendar-date',
      isToday ? 'today' : '',
      hasSchedule ? 'has-schedule' : ''
    ].join(' ');
    
    html += `<div class="${classes}" data-date="${thisDate.toISOString()}" data-icon="${icon}" tabindex="0">${d}</div>`;
  }
  calendarDates.innerHTML = html;

  // attach click handler for date items (open day view/add schedule)
  document.querySelectorAll('.calendar-date').forEach(el=>{
    el.onclick = ()=> {
      const selectedDate = new Date(el.dataset.date);
      openScheduleModalForDate(selectedDate);
      
      // Highlight selected date (optional)
      document.querySelectorAll('.calendar-date').forEach(x=>x.classList.remove('selected'));
      el.classList.add('selected');
    };
  });
}

renderCalendar(currentDate);

// prev/next
document.getElementById('prevMonthBtn').onclick = ()=>{
  currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth()-1, 1);
  renderCalendar(currentDate);
};
document.getElementById('nextMonthBtn').onclick = ()=>{
  currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth()+1, 1);
  renderCalendar(currentDate);
};

// --- Month & Year pickers
const monthPanel = document.getElementById('monthPanel');
const yearPanel = document.getElementById('yearPanel');
const monthsList = document.getElementById('monthsList');
const yearsList = document.getElementById('yearsList');
const monthPickerBtn = document.getElementById('monthPickerBtn');
const yearPickerBtn = document.getElementById('yearPickerBtn');

const monthNames = Array.from({length:12}, (_,i)=>new Date(0,i).toLocaleString('default',{month:'long'}));
function populateMonths(){
  monthsList.innerHTML = '';
  monthNames.forEach((m, idx)=>{
    const div = document.createElement('div');
    div.className = 'picker-item';
    div.textContent = m;
    div.onclick = ()=>{
      currentDate = new Date(currentDate.getFullYear(), idx, 1);
      renderCalendar(currentDate);
      hidePanels();
    };
    monthsList.appendChild(div);
  });
}
function populateYears(){
  yearsList.innerHTML = '';
  const thisYear = new Date().getFullYear();
  const start = thisYear - 50;
  const end = thisYear + 50; // past to future
  for (let y = start; y<=end; y++){
    const div = document.createElement('div');
    div.className = 'picker-item';
    div.textContent = y;
    div.onclick = ()=>{
      // keep the same month, change year
      currentDate = new Date(y, currentDate.getMonth(), 1);
      renderCalendar(currentDate);
      hidePanels();
    };
    yearsList.appendChild(div);
  }
}
populateMonths();
populateYears();

function hidePanels(){ monthPanel.classList.add('d-none'); yearPanel.classList.add('d-none'); }
function togglePanel(panel){
  if (panel.classList.contains('d-none')) {
    hidePanels();
    panel.classList.remove('d-none');
  } else {
    panel.classList.add('d-none');
  }
}
monthPickerBtn.onclick = ()=> togglePanel(monthPanel);
yearPickerBtn.onclick = ()=> togglePanel(yearPanel);

// close panels if clicking outside
document.addEventListener('click', (e)=>{
  if (!e.target.closest('.picker-panel') && !e.target.closest('#monthPickerBtn') && !e.target.closest('#yearPickerBtn')){
    hidePanels();
  }
});

// --- Schedules display
const schedulesListEl = document.getElementById('schedulesList');
function updateSchedulesList(){
  schedulesListEl.innerHTML = '';
  // Sort schedules by date, then by time
  const sortedSchedules = schedules.slice().sort((a, b) => new Date(a.date) - new Date(b.date));

  // show latest 3 for dashboard overview
  const recent = sortedSchedules.filter(s => new Date(s.date) >= new Date()).slice(0,3); // Only future/current schedules

  if (recent.length === 0) {
    schedulesListEl.innerHTML = '<div class="schedule-bar">No upcoming schedules.</div>';
    return;
  }

  recent.forEach(s=>{
    const d = new Date(s.date);
    const text = `${s.text} — ${d.toLocaleDateString()} ${d.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})}`;
    const div = document.createElement('div');
    div.className = 'schedule-bar';
    div.innerHTML = `
        <span>${text}</span>
        <div class="schedule-actions">
            <button class="btn btn-sm btn-outline-light edit-schedule-btn" data-id="${s.id}" aria-label="Edit schedule"><i class="bi bi-pencil"></i></button>
            <button class="btn btn-sm btn-outline-danger delete-schedule-btn" data-id="${s.id}" aria-label="Delete schedule"><i class="bi bi-trash"></i></button>
        </div>
    `;
    schedulesListEl.appendChild(div);
  });
  attachScheduleActionListeners();
}
updateSchedulesList();

function attachScheduleActionListeners() {
    document.querySelectorAll('.edit-schedule-btn').forEach(btn => {
        btn.onclick = (e) => {
            const scheduleId = e.currentTarget.dataset.id;
            const scheduleToEdit = schedules.find(s => s.id === scheduleId);
            if (scheduleToEdit) {
                openScheduleModalForEdit(scheduleToEdit);
            }
        };
    });
    document.querySelectorAll('.delete-schedule-btn').forEach(btn => {
        btn.onclick = (e) => {
            const scheduleId = e.currentTarget.dataset.id;
            if (confirm('Are you sure you want to delete this schedule?')) {
                schedules = schedules.filter(s => s.id !== scheduleId);
                updateSchedulesList();
                renderCalendar(currentDate);
            }
        };
    });
}


// --- Add/Edit schedule flow (modal)
const addModalEl = document.getElementById('addScheduleModal');
const addModal = new bootstrap.Modal(addModalEl);
const modalScheduleTitle = document.getElementById('modalScheduleTitle');
const modalWorkoutType = document.getElementById('modalWorkoutType');
const modalScheduleDate = document.getElementById('modalScheduleDate');
const modalScheduleTime = document.getElementById('modalScheduleTime');
const modalScheduleId = document.getElementById('modalScheduleId');
const addScheduleModalLabel = document.getElementById('addScheduleModalLabel');
const saveScheduleBtn = document.getElementById('saveScheduleBtn');
const deleteScheduleBtn = document.getElementById('deleteScheduleBtn');


function openScheduleModalForDate(date) {
    addScheduleModalLabel.textContent = "Add Schedule";
    modalScheduleTitle.value = document.getElementById('scheduleInput').value; // Prefill from input bar
    modalWorkoutType.value = "default"; // Reset type
    modalScheduleDate.valueAsDate = date;
    modalScheduleTime.value = '07:00';
    modalScheduleId.value = ''; // Clear ID for new schedule
    deleteScheduleBtn.style.display = 'none';
    addModal.show();
}

function openScheduleModalForEdit(schedule) {
    addScheduleModalLabel.textContent = "Edit Schedule";
    modalScheduleTitle.value = schedule.text;
    modalWorkoutType.value = schedule.workoutType || "default";
    modalScheduleDate.valueAsDate = new Date(schedule.date);
    modalScheduleTime.value = new Date(schedule.date).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' });
    modalScheduleId.value = schedule.id; // Set ID for editing
    deleteScheduleBtn.style.display = 'inline-block';
    addModal.show();
}


document.getElementById('openAddModal').onclick = ()=> {
    openScheduleModalForDate(new Date()); // Open for today by default from main input
};

saveScheduleBtn.onclick = ()=>{
  const title = modalScheduleTitle.value.trim();
  const workoutType = modalWorkoutType.value;
  const dateVal = modalScheduleDate.value;
  const timeVal = modalScheduleTime.value || '00:00';
  const id = modalScheduleId.value;

  if (!title) return alert('Schedule title cannot be empty.');
  if (workoutType === 'default') return alert('Please select a workout type.');
  if (!dateVal) return alert('Choose a date.');

  const [y,m,d] = dateVal.split('-').map(Number);
  const [hh,mm] = timeVal.split(':').map(Number);
  const dt = new Date(y, m-1, d, hh, mm);

  if (id) {
    // Edit existing schedule
    const index = schedules.findIndex(s => s.id === id);
    if (index !== -1) {
      schedules[index] = { ...schedules[index], date: dt, text: title, workoutType: workoutType };
    }
  } else {
    // Add new schedule
    schedules.push({ id: `sch${Date.now()}`, date: dt, text: title, workoutType: workoutType });
  }
  addModal.hide();
  document.getElementById('scheduleInput').value = ''; // Clear the input bar
  updateSchedulesList();
  renderCalendar(currentDate);
};

deleteScheduleBtn.onclick = () => {
    const idToDelete = modalScheduleId.value;
    if (confirm('Are you sure you want to delete this schedule?')) {
        schedules = schedules.filter(s => s.id !== idToDelete);
        addModal.hide();
        updateSchedulesList();
        renderCalendar(currentDate);
    }
};

document.getElementById('viewAllPrograms').onclick = (e)=>{
    const type = e.currentTarget.dataset.type;
    const modalList = document.getElementById('modalSchedulesList');
    const modalTitle = document.getElementById('schedulesModalLabel');
    modalList.innerHTML = '';

    if (type === 'programs') {
        modalTitle.textContent = "All Programs Joined";
        // programs are the program-notification items; replicate them here
        const programs = ['HIIT Challenge','Yoga Flow','Cardio Blast'];
        if (programs.length === 0) {
            modalList.innerHTML = '<div class="schedule-bar">No programs joined yet.</div>';
        } else {
            programs.forEach(p=>{
                const div = document.createElement('div');
                div.className = 'schedule-bar';
                div.textContent = p;
                modalList.appendChild(div);
            });
        }
    }
    const m = new bootstrap.Modal(document.getElementById('schedulesModal'));
    m.show();
};


// --- Detailed Progress Chart (for modal)
const detailedProgressModalEl = document.getElementById('detailedProgressModal');
let detailedProgressChart;

detailedProgressModalEl.addEventListener('shown.bs.modal', function () {
    const detailedCtx = document.getElementById('detailedProgressChart').getContext('2d');

    // Destroy existing chart if it exists to prevent re-renders
    if (detailedProgressChart) {
        detailedProgressChart.destroy();
    }

    detailedProgressChart = new Chart(detailedCtx, {
        type: 'line', // Line graph for progress over time
        data: {
            labels: historicalProgress.labels,
            datasets: [
                {
                    label: 'Calories Burned (KCal)',
                    data: historicalProgress.caloriesBurned,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Workout Streak (Days)',
                    data: historicalProgress.workoutStreak,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: 'var(--text-light)' // Set legend text color
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('en-US').format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    ticks: {
                        color: 'var(--text-muted)' // Set x-axis tick color
                    },
                    grid: {
                        color: 'rgba(255,255,255,0.1)' // Set x-axis grid color
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: 'var(--text-muted)' // Set y-axis tick color
                    },
                    grid: {
                        color: 'rgba(255,255,255,0.1)' // Set y-axis grid color
                    }
                }
            }
        }
    });
});

// Accessibility: close pickers on ESC
document.addEventListener('keydown', (e)=>{
  if (e.key === 'Escape') hidePanels();
});

// Resize: keep chart responsive by resizing Chart
window.addEventListener('resize', ()=> {
  doughnut.resize();
  if (detailedProgressChart) {
    detailedProgressChart.resize();
  }
});
