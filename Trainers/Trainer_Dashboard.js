document.addEventListener('DOMContentLoaded', function() {
    // --- Data
    let currentDate = new Date();
    let schedules = [
        // Sample initial schedules for trainer (use real dates as needed)
        { id: 'sch1', date: new Date(new Date().setDate(new Date().getDate() + 1)), text: "Client Session: John D.", type: "session" },
        { id: 'sch2', date: new Date(new Date().setDate(new Date().getDate() + 2)), text: "Team Meeting", type: "meeting" },
        { id: 'sch3', date: new Date(new Date().setDate(new Date().getDate() - 1)), text: "Review Client Assessments", type: "admin" },
        { id: 'sch4', date: new Date(), text: "Client Session: Clai D.", type: "session" } // Today's schedule
    ];

    // --- Calendar rendering
    const calendarDates = document.getElementById('calendarDates');
    const calendarMonthYear = document.getElementById('calendarMonthYear');
    // Removed scheduleTypeIcons as we are only using a generic dot/circle
    
    function renderCalendar(date) {
        const year = date.getFullYear();
        const month = date.getMonth();

        calendarMonthYear.textContent = `${date.toLocaleString('default', { month: 'long' })} ${year}`;

        // Header days
        const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        let html = '';
        days.forEach(d => html += `<div class="calendar-day">${d}</div>`);

        // Dates
        const firstDay = new Date(year, month, 1).getDay();
        const lastDate = new Date(year, month + 1, 0).getDate();
        const today = new Date();
        for (let i = 0; i < firstDay; i++) { html += `<div></div>`; }
        for (let d = 1; d <= lastDate; d++) {
            const thisDate = new Date(year, month, d);
            const isToday = thisDate.toDateString() === today.toDateString();

            // Find all schedules for this date
            const daySchedules = schedules.filter(s => {
                const sDate = new Date(s.date);
                return sDate.getFullYear() === year && sDate.getMonth() === month && sDate.getDate() === d;
            });

            // Determine if there are schedules
            const hasSchedule = daySchedules.length > 0;
            
            const classes = [
                'calendar-date',
                isToday ? 'today' : '',
                hasSchedule ? 'has-schedule' : '' // This class will add the generic dot
            ].join(' ');

            html += `<div class="${classes}" data-date="${thisDate.toISOString()}" tabindex="0">${d}</div>`;
        }
        calendarDates.innerHTML = html;

        // Attach click handler for date items (open day view/add schedule)
        document.querySelectorAll('.calendar-date').forEach(el => {
            el.onclick = () => {
                const selectedDate = new Date(el.dataset.date);
                openScheduleModalForDate(selectedDate);

                // Highlight selected date (optional)
                document.querySelectorAll('.calendar-date').forEach(x => x.classList.remove('selected'));
                el.classList.add('selected');
            };
        });
    }

    renderCalendar(currentDate);

    // prev/next
    document.getElementById('prevMonthBtn').onclick = () => {
        currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth() - 1, 1);
        renderCalendar(currentDate);
    };
    document.getElementById('nextMonthBtn').onclick = () => {
        currentDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 1);
        renderCalendar(currentDate);
    };

    // --- Month & Year pickers
    const monthPanel = document.getElementById('monthPanel');
    const yearPanel = document.getElementById('yearPanel');
    const monthsList = document.getElementById('monthsList');
    const yearsList = document.getElementById('yearsList');
    const monthPickerBtn = document.getElementById('monthPickerBtn');
    const yearPickerBtn = document.getElementById('yearPickerBtn');

    const monthNames = Array.from({ length: 12 }, (_, i) => new Date(0, i).toLocaleString('default', { month: 'long' }));
    function populateMonths() {
        monthsList.innerHTML = '';
        monthNames.forEach((m, idx) => {
            const div = document.createElement('div');
            div.className = 'picker-item';
            div.textContent = m;
            div.onclick = () => {
                currentDate = new Date(currentDate.getFullYear(), idx, 1);
                renderCalendar(currentDate);
                hidePanels();
            };
            monthsList.appendChild(div);
        });
    }
    function populateYears() {
        yearsList.innerHTML = '';
        const thisYear = new Date().getFullYear();
        const start = thisYear - 50;
        const end = thisYear + 50; // past to future
        for (let y = start; y <= end; y++) {
            const div = document.createElement('div');
            div.className = 'picker-item';
            div.textContent = y;
            div.onclick = () => {
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

    function hidePanels() { monthPanel.classList.add('d-none'); yearPanel.classList.add('d-none'); }
    function togglePanel(panel) {
        if (panel.classList.contains('d-none')) {
            hidePanels();
            panel.classList.remove('d-none');
        } else {
            panel.classList.add('d-none');
        }
    }
    monthPickerBtn.onclick = () => togglePanel(monthPanel);
    yearPickerBtn.onclick = () => togglePanel(yearPanel);

    // close panels if clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.picker-panel') && !e.target.closest('#monthPickerBtn') && !e.target.closest('#yearPickerBtn')) {
            hidePanels();
        }
    });

    // --- Schedules display
    const schedulesListEl = document.getElementById('schedulesList');
    function updateSchedulesList() {
        schedulesListEl.innerHTML = '';
        // Sort schedules by date, then by time
        const sortedSchedules = schedules.slice().sort((a, b) => new Date(a.date) - new Date(b.date));

        // Show latest 3 for dashboard overview
        const recent = sortedSchedules.filter(s => new Date(s.date) >= new Date()).slice(0, 3); // Only future/current schedules

        if (recent.length === 0) {
            schedulesListEl.innerHTML = '<div class="schedule-bar">No upcoming schedules.</div>';
            return;
        }

        recent.forEach(s => {
            const d = new Date(s.date);
            const text = `${s.text} — ${d.toLocaleDateString()} ${d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`;
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
    const modalScheduleType = document.getElementById('modalScheduleType');
    const modalScheduleDate = document.getElementById('modalScheduleDate');
    const modalScheduleTime = document.getElementById('modalScheduleTime');
    const modalScheduleId = document.getElementById('modalScheduleId');
    const addScheduleModalLabel = document.getElementById('addScheduleModalLabel');
    const saveScheduleBtn = document.getElementById('saveScheduleBtn');
    const deleteScheduleBtn = document.getElementById('deleteScheduleBtn');


    function openScheduleModalForDate(date) {
        addScheduleModalLabel.textContent = "Add Schedule";
        modalScheduleTitle.value = document.getElementById('scheduleInput').value; // Prefill from input bar
        modalScheduleType.value = "default"; // Reset type
        modalScheduleDate.valueAsDate = date;
        modalScheduleTime.value = '07:00';
        modalScheduleId.value = ''; // Clear ID for new schedule
        deleteScheduleBtn.style.display = 'none';
        addModal.show();
    }

    function openScheduleModalForEdit(schedule) {
        addScheduleModalLabel.textContent = "Edit Schedule";
        modalScheduleTitle.value = schedule.text;
        modalScheduleType.value = schedule.type || "default";
        modalScheduleDate.valueAsDate = new Date(schedule.date);
        modalScheduleTime.value = new Date(schedule.date).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' });
        modalScheduleId.value = schedule.id; // Set ID for editing
        deleteScheduleBtn.style.display = 'inline-block';
        addModal.show();
    }


    document.getElementById('openAddModal').onclick = () => {
        openScheduleModalForDate(new Date()); // Open for today by default from main input
    };

    saveScheduleBtn.onclick = () => {
        const title = modalScheduleTitle.value.trim();
        const type = modalScheduleType.value;
        const dateVal = modalScheduleDate.value;
        const timeVal = modalScheduleTime.value || '00:00';
        const id = modalScheduleId.value;

        if (!title) return alert('Schedule title cannot be empty.');
        if (type === 'default') return alert('Please select a schedule type.');
        if (!dateVal) return alert('Choose a date.');

        const [y, m, d] = dateVal.split('-').map(Number);
        const [hh, mm] = timeVal.split(':').map(Number);
        const dt = new Date(y, m - 1, d, hh, mm);

        if (id) {
            // Edit existing schedule
            const index = schedules.findIndex(s => s.id === id);
            if (index !== -1) {
                schedules[index] = { ...schedules[index], date: dt, text: title, type: type };
            }
        } else {
            // Add new schedule
            schedules.push({ id: `sch${Date.now()}`, date: dt, text: title, type: type });
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

    document.getElementById('viewAllSchedules').onclick = () => {
        const modalList = document.getElementById('modalSchedulesList');
        const modalTitle = document.getElementById('schedulesModalLabel');
        modalList.innerHTML = '';

        modalTitle.textContent = "All My Schedules";
        
        // Sort all schedules for the modal
        const allSortedSchedules = schedules.slice().sort((a, b) => new Date(a.date) - new Date(b.date));

        if (allSortedSchedules.length === 0) {
            modalList.innerHTML = '<div class="schedule-bar">No schedules added yet.</div>';
        } else {
            allSortedSchedules.forEach(s => {
                const d = new Date(s.date);
                const text = `${s.text} (${s.type}) — ${d.toLocaleDateString()} ${d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`;
                const div = document.createElement('div');
                div.className = 'schedule-bar';
                div.innerHTML = `
                    <span>${text}</span>
                    <div class="schedule-actions">
                        <button class="btn btn-sm btn-outline-light edit-schedule-btn" data-id="${s.id}" aria-label="Edit schedule"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-sm btn-outline-danger delete-schedule-btn" data-id="${s.id}" aria-label="Delete schedule"><i class="bi bi-trash"></i></button>
                    </div>
                `;
                modalList.appendChild(div);
            });
             // Re-attach listeners for modal items (as they are dynamically added)
            document.querySelectorAll('#modalSchedulesList .edit-schedule-btn').forEach(btn => {
                btn.onclick = (e) => {
                    const scheduleId = e.currentTarget.dataset.id;
                    const scheduleToEdit = schedules.find(s => s.id === scheduleId);
                    if (scheduleToEdit) {
                        const m = bootstrap.Modal.getInstance(document.getElementById('schedulesModal'));
                        if(m) m.hide(); // Hide the "view all" modal
                        openScheduleModalForEdit(scheduleToEdit);
                    }
                };
            });
            document.querySelectorAll('#modalSchedulesList .delete-schedule-btn').forEach(btn => {
                btn.onclick = (e) => {
                    const scheduleId = e.currentTarget.dataset.id;
                    if (confirm('Are you sure you want to delete this schedule?')) {
                        schedules = schedules.filter(s => s.id !== scheduleId);
                        // Update both the main list and re-render the modal
                        updateSchedulesList();
                        renderCalendar(currentDate);
                        
                        const m = bootstrap.Modal.getInstance(document.getElementById('schedulesModal'));
                        if(m) m.hide(); // Hide and reopen to reflect changes, or re-render modal content
                        document.getElementById('viewAllSchedules').click(); // Reopen to show updated list
                    }
                };
            });
        }
        const m = new bootstrap.Modal(document.getElementById('schedulesModal'));
        m.show();
    };

    // Accessibility: close pickers on ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') hidePanels();
    });

    // Sidebar toggle for mobile (re-using the logic from ClientPage.js and Sidebar.js)
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.querySelector('.sidebar');
    const contentWrapper = document.querySelector('.content-wrapper'); // Get content wrapper

    if (menuToggle && sidebar && contentWrapper) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show'); // 'show' class from ClientPage.css mobile section
            // Adjust content-wrapper margin if sidebar is shown on mobile
            if (window.innerWidth <= 1024) { // Assuming 1024px is breakpoint for mobile sidebar behavior
                 if (sidebar.classList.contains('show')) {
                    contentWrapper.style.marginLeft = '0'; // Keep content full width
                }
            }
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 1024) { // Assuming 1024px is breakpoint for mobile
                if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
    }

    // Toggle collapse for sidebar on logo click (re-using from Sidebar.js)
    const logo = document.getElementById("logo");
    if (logo) {
        logo.addEventListener("click", function() {
            // Only collapse if screen is not small (otherwise it hides completely)
            if (window.innerWidth > 1024) { 
                sidebar.classList.toggle("collapsed"); // 'collapsed' class from SideBar.css
                // Adjust content-wrapper margin when sidebar collapses/expands
                if (sidebar.classList.contains('collapsed')) {
                    contentWrapper.style.marginLeft = '90px'; // Match collapsed sidebar width
                } else {
                    contentWrapper.style.marginLeft = '270px'; // Match expanded sidebar width
                }
            }
        });
    }

    // Adjust content-wrapper on initial load and resize
    function adjustContentWrapper() {
        if (window.innerWidth <= 1024) {
            contentWrapper.style.marginLeft = '0';
        } else if (sidebar.classList.contains('collapsed')) {
            contentWrapper.style.marginLeft = '90px';
        } else {
            contentWrapper.style.marginLeft = '270px';
        }
    }

    // Run on load
    adjustContentWrapper();
    // Run on resize
    window.addEventListener('resize', adjustContentWrapper);
});