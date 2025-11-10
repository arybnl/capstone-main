// Tracker.js

// Utility function to generate unique IDs
const generateId = () => `id${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;

// --- Local Storage Management ---
let schedules = [];

const saveToLocalStorage = () => {
    localStorage.setItem('workoutSchedules', JSON.stringify(schedules));
};

const loadFromLocalStorage = () => {
    const storedSchedules = localStorage.getItem('workoutSchedules');

    // Example Data - Template (Load if no stored data)
    const exampleSchedules = [
        // Today's workout (example)
        { id: generateId(), date: new Date(), text: "Morning Yoga Flow", workoutType: "yoga", completed: false, originalRecurringId: null, recurrenceType: "none", recurrenceEndDate: null },
        // Tomorrow's workout (example)
        { id: generateId(), date: new Date(new Date().setDate(new Date().getDate() + 1)), text: "Strength Training - Upper Body", workoutType: "weights", completed: false, originalRecurringId: null, recurrenceType: "none", recurrenceEndDate: null },
        // A future recurring workout (starts tomorrow, weekly for 2 months)
        { id: generateId(), date: new Date(new Date().setDate(new Date().getDate() + 1)), text: "Cardio Blast", workoutType: "cardio", completed: false, originalRecurringId: generateId(), recurrenceType: "weekly", recurrenceEndDate: new Date(new Date().setMonth(new Date().getMonth() + 2)) },
        // A completed workout from yesterday (example)
        { id: generateId(), date: new Date(new Date().setDate(new Date().getDate() - 1)), text: "Pilates for Core Strength", workoutType: "yoga", completed: true, originalRecurringId: null, recurrenceType: "none", recurrenceEndDate: null },
        // A completed workout from two days ago (example)
        { id: generateId(), date: new Date(new Date().setDate(new Date().getDate() - 2)), text: "HIIT Session", workoutType: "cardio", completed: true, originalRecurringId: null, recurrenceType: "none", recurrenceEndDate: null }
    ];

    schedules = storedSchedules ? JSON.parse(storedSchedules).map(s => ({
        ...s,
        date: new Date(s.date),
        recurrenceEndDate: s.recurrenceEndDate ? new Date(s.recurrenceEndDate) : null
    })) : []; // Start with an empty array if no stored schedules to populate with generated recurring ones

    // If no schedules were loaded, populate with example data
    if (schedules.length === 0) {
        exampleSchedules.forEach(s => {
            if (s.recurrenceType && s.recurrenceType !== 'none') {
                const newRecurringId = s.originalRecurringId || generateId();
                generateRecurringSchedules(s.date, s.text, s.workoutType, s.recurrenceType, s.recurrenceEndDate, newRecurringId, s.completed);
            } else {
                schedules.push(s);
            }
        });
    }

    // After loading/populating, ensure that any recurring schedules without an originalRecurringId
    // get one assigned if they are marked as recurring. This helps manage generated instances.
    schedules = schedules.map(s => {
        if (s.recurrenceType && s.recurrenceType !== 'none' && !s.originalRecurringId) {
            return { ...s, originalRecurringId: s.id }; // Treat itself as the original if it starts a series
        }
        return s;
    });
};


// --- DOM Elements ---
const addScheduleModalEl = document.getElementById('addScheduleModal');
const addScheduleModal = new bootstrap.Modal(addScheduleModalEl);
const modalScheduleTitle = document.getElementById('modalScheduleTitle');
const modalWorkoutType = document.getElementById('modalWorkoutType');
const modalScheduleDate = document.getElementById('modalScheduleDate');
const modalScheduleTime = document.getElementById('modalScheduleTime');
const modalRecurrenceType = document.getElementById('modalRecurrenceType');
const recurrenceEndDateGroup = document.getElementById('recurrenceEndDateGroup');
const modalRecurrenceEndDate = document.getElementById('modalRecurrenceEndDate');
const modalScheduleId = document.getElementById('modalScheduleId');
const modalOriginalRecurringId = document.getElementById('modalOriginalRecurringId');
const addScheduleModalLabel = document.getElementById('addScheduleModalLabel');
const saveScheduleBtn = document.getElementById('saveScheduleBtn');
const deleteScheduleBtn = document.getElementById('deleteScheduleBtn');

const editRecurringModalEl = document.getElementById('editRecurringModal');
const editRecurringModal = new bootstrap.Modal(editRecurringModalEl);
const editThisInstanceBtn = document.getElementById('editThisInstanceBtn');
const editFutureInstancesBtn = document.getElementById('editFutureInstancesBtn');
const deleteAllFutureInstancesBtn = document.getElementById('deleteAllFutureInstancesBtn');

// Calendar elements
const workoutCalendarGrid = document.getElementById('workoutCalendarGrid');
const currentMonthYear = document.getElementById('currentMonthYear');
const prevMonthBtn = document.getElementById('prevMonthBtn');
const nextMonthBtn = document.getElementById('nextMonthBtn');
let currentCalendarDate = new Date(); // Tracks the month displayed in the calendar

// Workout for the Day elements
const selectedDayHeader = document.getElementById('selectedDayHeader');
const workoutForDayList = document.getElementById('workoutForDayList');
const noWorkoutsForDayMessage = document.getElementById('noWorkoutsForDayMessage');

// All Scheduled Workouts table
const allWorkoutsList = document.getElementById('allWorkoutsList');

// --- Global State for selected date in calendar ---
let selectedDate = new Date(); // Defaults to today

// --- Scheduled Workouts Functions ---

function renderAllScheduledWorkoutsTable() {
    allWorkoutsList.innerHTML = '';

    // Filter out past instances of recurring events that were completed individually
    // Or filter out future instances that should not exist anymore after editing
    const effectiveSchedules = filterAndSortSchedules(schedules);

    if (effectiveSchedules.length === 0) {
        allWorkoutsList.innerHTML = `<tr><td colspan="6" class="text-center text-muted py-4">No workouts scheduled. Click "Add New Schedule" to get started!</td></tr>`;
        return;
    }

    effectiveSchedules.forEach(s => {
        const row = createScheduleTableRow(s);
        allWorkoutsList.appendChild(row);
    });
    attachScheduleActionListeners();
    saveToLocalStorage();
}

function createScheduleTableRow(s) {
    const row = document.createElement('tr');
    const sDate = new Date(s.date);
    const timeString = sDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    const isCompleted = s.completed ? 'checked' : '';
    const rowClass = s.completed ? 'table-completed-workout' : '';

    row.className = rowClass;
    row.innerHTML = `
        <td class="text-center">
            <input type="checkbox" class="form-check-input workout-complete-checkbox" data-id="${s.id}" ${isCompleted}>
        </td>
        <td>${sDate.toLocaleDateString()}</td>
        <td>${timeString}</td>
        <td>${s.text}</td>
        <td>${s.workoutType.charAt(0).toUpperCase() + s.workoutType.slice(1)}</td>
        <td class="text-center">
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-light dropdown-toggle-no-caret" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-dark">
                    <li><a class="dropdown-item edit-schedule-btn" href="#" data-id="${s.id}"><i class="bi bi-pencil"></i> Edit</a></li>
                    <li><a class="dropdown-item delete-schedule-btn" href="#" data-id="${s.id}"><i class="bi bi-trash"></i> Delete</a></li>
                </ul>
            </div>
        </td>
    `;
    return row;
}

function attachScheduleActionListeners() {
    document.querySelectorAll('.edit-schedule-btn').forEach(btn => {
        btn.onclick = (e) => {
            e.preventDefault();
            const scheduleId = e.currentTarget.dataset.id;
            const scheduleToEdit = schedules.find(s => s.id === scheduleId);
            if (scheduleToEdit) {
                if (scheduleToEdit.recurrenceType && scheduleToEdit.recurrenceType !== 'none') {
                    // It's a recurring schedule, show the options modal
                    editRecurringModalEl.dataset.scheduleId = scheduleId; // Store ID for modal buttons
                    editRecurringModal.show();
                } else {
                    // Not recurring, open directly for edit
                    openScheduleModalForEdit(scheduleToEdit);
                }
            }
        };
    });
    document.querySelectorAll('.delete-schedule-btn').forEach(btn => {
        btn.onclick = (e) => {
            e.preventDefault();
            const scheduleId = e.currentTarget.dataset.id;
            const scheduleToDelete = schedules.find(s => s.id === scheduleId);
            if (scheduleToDelete) {
                if (scheduleToDelete.recurrenceType && scheduleToDelete.recurrenceType !== 'none') {
                     // Check if it's the original or a detached instance
                     if (scheduleToDelete.originalRecurringId === scheduleToDelete.id) { // This is the original recurring event
                        if (confirm('This is the original event in a recurring series. Do you want to delete only this instance, or this and all future instances? \n\n Press OK to delete this instance only. \n Press Cancel to delete this and all future instances.')) {
                            // Convert this specific instance to a non-recurring, effectively deleting it from the series logic
                            schedules = schedules.map(s => s.id === scheduleId ? { ...s, recurrenceType: 'none', originalRecurringId: null, recurrenceEndDate: null } : s);
                            // Also need to adjust the future generated ones that were linked to this original if it was the first instance
                            // For simplicity, we'll just delete this one and let the future instances exist independently or be edited.
                            // A more robust solution might require re-parenting. For now, we'll remove it.
                            schedules = schedules.filter(s => s.id !== scheduleId);
                        } else {
                            // Delete this and all future instances of the original recurring ID
                            schedules = schedules.filter(s => s.originalRecurringId !== scheduleToDelete.originalRecurringId || new Date(s.date) < new Date(scheduleToDelete.date));
                        }
                     } else { // It's a generated instance of a recurring event
                        if (confirm('This is a recurring workout. Do you want to delete only this instance, or this and all future instances? \n\n Press OK to delete this instance only. \n Press Cancel to delete this and all future instances.')) {
                            schedules = schedules.filter(s => s.id !== scheduleId); // Delete only this instance
                        } else {
                            // Delete this and all future instances of the original recurring ID starting from this instance's date
                            schedules = schedules.filter(s => s.originalRecurringId !== scheduleToDelete.originalRecurringId || new Date(s.date) < new Date(scheduleToDelete.date));
                        }
                     }

                } else {
                    if (confirm('Are you sure you want to delete this schedule?')) {
                        schedules = schedules.filter(s => s.id !== scheduleId);
                    }
                }
                renderAllData();
            }
        };
    });
    document.querySelectorAll('.workout-complete-checkbox').forEach(checkbox => {
        checkbox.onchange = (e) => {
            const scheduleId = e.target.dataset.id;
            const isChecked = e.target.checked;
            const scheduleIndex = schedules.findIndex(s => s.id === scheduleId);
            if (scheduleIndex !== -1) {
                schedules[scheduleIndex].completed = isChecked;
                e.target.parentNode.parentNode.classList.toggle('table-completed-workout', isChecked); // Apply/remove strikethrough instantly
                // Add a small animation effect
                e.target.style.transform = isChecked ? 'scale(1.1)' : 'scale(1)';
                setTimeout(() => {
                    e.target.style.transform = 'scale(1)'; // Reset after animation
                    renderAllData(); // Re-render all to update calendar and table
                }, 200); // Match animation duration
            }
        };
    });
}

function openScheduleModalForAdd(date = new Date()) {
    addScheduleModalLabel.textContent = "Add New Schedule";
    modalScheduleTitle.value = '';
    modalWorkoutType.value = "default";
    modalScheduleDate.valueAsDate = new Date(date.getFullYear(), date.getMonth(), date.getDate()); // Set date to the clicked calendar day
    modalScheduleTime.value = '07:00'; // Default to 7 AM
    modalRecurrenceType.value = 'none';
    modalRecurrenceEndDate.value = '';
    recurrenceEndDateGroup.style.display = 'none';
    modalScheduleId.value = '';
    modalOriginalRecurringId.value = '';
    deleteScheduleBtn.style.display = 'none';
    addScheduleModal.show();
}

function openScheduleModalForEdit(schedule) {
    addScheduleModalLabel.textContent = "Edit Schedule";
    modalScheduleTitle.value = schedule.text;
    modalWorkoutType.value = schedule.workoutType || "default";
    modalScheduleDate.valueAsDate = new Date(schedule.date);
    modalScheduleTime.value = new Date(schedule.date).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hourCycle: 'h23' });
    modalRecurrenceType.value = schedule.recurrenceType || 'none';
    modalRecurrenceEndDate.valueAsDate = schedule.recurrenceEndDate || null;
    recurrenceEndDateGroup.style.display = (schedule.recurrenceType && schedule.recurrenceType !== 'none') ? 'block' : 'none';
    modalScheduleId.value = schedule.id;
    modalOriginalRecurringId.value = schedule.originalRecurringId || schedule.id; // If it's the original, its own ID is the recurring ID
    deleteScheduleBtn.style.display = 'inline-block';
    addScheduleModal.show();
}

modalRecurrenceType.onchange = () => {
    recurrenceEndDateGroup.style.display = (modalRecurrenceType.value !== 'none') ? 'block' : 'none';
};

saveScheduleBtn.onclick = () => {
    const title = modalScheduleTitle.value.trim();
    const workoutType = modalWorkoutType.value;
    const dateVal = modalScheduleDate.value;
    const timeVal = modalScheduleTime.value || '00:00';
    const recurrenceType = modalRecurrenceType.value;
    const recurrenceEndDateVal = (recurrenceType !== 'none') ? modalRecurrenceEndDate.value : null;
    const id = modalScheduleId.value;
    const originalRecurringId = modalOriginalRecurringId.value; // Used for recurring edits

    if (!title) { alert('Schedule title cannot be empty.'); return; }
    if (workoutType === 'default') { alert('Please select a workout type.'); return; }
    if (!dateVal) { alert('Choose a date.'); return; }
    if (recurrenceType !== 'none' && !recurrenceEndDateVal) { alert('Please set a recurrence end date.'); return; }

    const [y, m, d] = dateVal.split('-').map(Number);
    const [hh, mm] = timeVal.split(':').map(Number);
    let dt = new Date(y, m - 1, d, hh, mm);

    let recurrenceEndDate = recurrenceEndDateVal ? new Date(recurrenceEndDateVal) : null;
    if (recurrenceEndDate) {
        recurrenceEndDate.setHours(23, 59, 59, 999); // End of the day
    }

    if (id) {
        // Editing existing schedule
        const index = schedules.findIndex(s => s.id === id);
        if (index !== -1) {
            // Check if it's an instance of a recurring schedule that's being detached
            if (schedules[index].originalRecurringId && schedules[index].originalRecurringId !== id && recurrenceType === 'none') {
                // User is converting a recurring instance to a one-time event
                schedules[index] = { ...schedules[index], date: dt, text: title, workoutType: workoutType, recurrenceType: 'none', recurrenceEndDate: null, originalRecurringId: null };
            } else if (originalRecurringId && recurrenceType !== 'none') {
                 // Editing the original recurring event or making a one-time event recurring
                 // Remove all future instances of this recurring series from the 'dt' onwards
                 schedules = schedules.filter(s => !(s.originalRecurringId === originalRecurringId && new Date(s.date) >= dt));

                 // Add the updated original and new recurring instances
                 generateRecurringSchedules(dt, title, workoutType, recurrenceType, recurrenceEndDate, originalRecurringId, schedules[index].completed);
            } else {
                // Simple edit of a one-time event, or making a recurring event into a one-time event without detaching
                schedules[index] = { ...schedules[index], date: dt, text: title, workoutType: workoutType, recurrenceType: recurrenceType, recurrenceEndDate: recurrenceEndDate, originalRecurringId: recurrenceType !== 'none' ? id : null };
            }
        }
    } else {
        // Add new schedule
        if (recurrenceType !== 'none') {
            const newRecurringId = generateId(); // Unique ID for this recurring series
            generateRecurringSchedules(dt, title, workoutType, recurrenceType, recurrenceEndDate, newRecurringId, false);
        } else {
            schedules.push({ id: generateId(), date: dt, text: title, workoutType: workoutType, completed: false, recurrenceType: 'none', recurrenceEndDate: null, originalRecurringId: null });
        }
    }
    addScheduleModal.hide();
    renderAllData();
};


deleteScheduleBtn.onclick = () => {
    const idToDelete = modalScheduleId.value;
    const scheduleToDelete = schedules.find(s => s.id === idToDelete);

    if (!scheduleToDelete) {
        addScheduleModal.hide();
        return;
    }

    if (scheduleToDelete.recurrenceType && scheduleToDelete.recurrenceType !== 'none') {
        // If it's the original or an instance that is part of a recurring series
        if (scheduleToDelete.originalRecurringId === scheduleToDelete.id) { // This is the original recurring event
            if (confirm('This is the original event in a recurring series. Do you want to delete only this instance, or this and all future instances? \n\n Press OK to delete this instance only. \n Press Cancel to delete this and all future instances.')) {
                // Convert this specific instance to a non-recurring, effectively deleting it from the series logic
                schedules = schedules.map(s => s.id === idToDelete ? { ...s, recurrenceType: 'none', originalRecurringId: null, recurrenceEndDate: null } : s);
                schedules = schedules.filter(s => s.id !== idToDelete);
            } else {
                // Delete this and all future instances of the original recurring ID
                schedules = schedules.filter(s => s.originalRecurringId !== scheduleToDelete.originalRecurringId || new Date(s.date) < new Date(scheduleToDelete.date));
            }
        } else { // It's a generated instance of a recurring event
            if (confirm('This is a recurring workout. Do you want to delete only this instance, or this and all future instances? \n\n Press OK to delete this instance only. \n Press Cancel to delete this and all future instances.')) {
                schedules = schedules.filter(s => s.id !== idToDelete); // Delete only this instance
            } else {
                // Delete this and all future instances of the original recurring ID starting from this instance's date
                schedules = schedules.filter(s => s.originalRecurringId !== scheduleToDelete.originalRecurringId || new Date(s.date) < new Date(scheduleToDelete.date));
            }
        }
    } else {
        // One-time schedule
        if (confirm('Are you sure you want to delete this schedule?')) {
            schedules = schedules.filter(s => s.id !== idToDelete);
        }
    }
    addScheduleModal.hide();
    renderAllData();
};

document.getElementById('addNewScheduleBtn').onclick = () => openScheduleModalForAdd();
document.getElementById('addWorkoutForDayBtn').onclick = () => openScheduleModalForAdd(selectedDate);


// --- Recurring Schedule Generation ---
function generateRecurringSchedules(startDate, title, workoutType, recurrenceType, recurrenceEndDate, originalRecurringId, completedStatus) {
    let current = new Date(startDate);
    current.setHours(startDate.getHours(), startDate.getMinutes(), startDate.getSeconds(), startDate.getMilliseconds()); // Preserve time
    let tempSchedules = [];

    // Ensure recurrenceEndDate is at the end of the day for accurate comparison
    const end = new Date(recurrenceEndDate);
    end.setHours(23, 59, 59, 999);

    // If an existing instance with this ID already exists, preserve its completed status.
    // This assumes if you're editing and regenerating, the original event (if it was an original)
    // should retain its completion status, but new future instances are false.
    const originalInstance = schedules.find(s => s.id === originalRecurringId && s.originalRecurringId === s.id);
    const initialCompletedStatus = originalInstance ? originalInstance.completed : completedStatus;

    while (current.getTime() <= end.getTime()) {
        const existingInstance = schedules.find(s =>
            s.originalRecurringId === originalRecurringId &&
            s.date.toDateString() === current.toDateString() &&
            s.text === title && // Also check title to ensure it's the *same* recurring event
            s.workoutType === workoutType
        );

        tempSchedules.push({
            id: existingInstance ? existingInstance.id : generateId(), // Keep existing ID if found
            date: new Date(current),
            text: title,
            workoutType: workoutType,
            completed: existingInstance ? existingInstance.completed : initialCompletedStatus, // Preserve completed status if already exists, else use initial
            recurrenceType: recurrenceType,
            recurrenceEndDate: recurrenceEndDate,
            originalRecurringId: originalRecurringId
        });

        if (recurrenceType === 'daily') {
            current.setDate(current.getDate() + 1);
        } else if (recurrenceType === 'weekly') {
            current.setDate(current.getDate() + 7);
        } else if (recurrenceType === 'monthly') {
            const originalDay = current.getDate();
            current.setMonth(current.getMonth() + 1);
            if (current.getDate() !== originalDay) {
                current.setDate(0);
            }
        } else {
            break;
        }
    }

    // Filter out old instances of this recurring series
    schedules = schedules.filter(s => s.originalRecurringId !== originalRecurringId);
    schedules.push(...tempSchedules);
}


// --- Edit Recurring Modal Actions ---
editThisInstanceBtn.onclick = () => {
    const scheduleId = editRecurringModalEl.dataset.scheduleId;
    const scheduleToEdit = schedules.find(s => s.id === scheduleId);
    if (scheduleToEdit) {
        // Detach this instance from the recurring series and open for individual edit
        const detachedSchedule = { ...scheduleToEdit, recurrenceType: 'none', recurrenceEndDate: null, originalRecurringId: null };
        openScheduleModalForEdit(detachedSchedule);
    }
    editRecurringModal.hide();
};

editFutureInstancesBtn.onclick = () => {
    const scheduleId = editRecurringModalEl.dataset.scheduleId;
    const scheduleToEdit = schedules.find(s => s.id === scheduleId);
    if (scheduleToEdit) {
        // Open the current instance for editing, but its originalRecurringId will ensure
        // that 'saveScheduleBtn' regeneration logic updates all future instances from this date.
        openScheduleModalForEdit(scheduleToEdit);
    }
    editRecurringModal.hide();
};

deleteAllFutureInstancesBtn.onclick = () => {
    const scheduleId = editRecurringModalEl.dataset.scheduleId;
    const scheduleToDelete = schedules.find(s => s.id === scheduleId);

    if (scheduleToDelete && confirm('Are you sure you want to delete this and all future instances of this recurring workout?')) {
        // Remove all instances of this recurring series from the current schedule's date onwards
        schedules = schedules.filter(s => !(s.originalRecurringId === scheduleToDelete.originalRecurringId && new Date(s.date) >= new Date(scheduleToDelete.date)));
        renderAllData();
    }
    editRecurringModal.hide();
};

// --- Calendar Functions ---
function renderCalendar(date) {
    workoutCalendarGrid.innerHTML = '';
    currentMonthYear.textContent = date.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

    const firstDayOfMonth = new Date(date.getFullYear(), date.getMonth(), 1);
    const lastDayOfMonth = new Date(date.getFullYear(), date.getMonth() + 1, 0);
    const today = new Date();
    today.setHours(0, 0, 0, 0); // Normalize today for comparison

    // Get the day of the week for the first day (0 for Sunday, 1 for Monday, etc.)
    const startDay = firstDayOfMonth.getDay(); // 0 is Sunday

    // Filter and sort schedules once for efficiency
    const effectiveSchedules = filterAndSortSchedules(schedules);

    // Fill in leading empty days
    for (let i = 0; i < startDay; i++) {
        const emptyDay = document.createElement('div');
        emptyDay.classList.add('calendar-day-box', 'empty');
        workoutCalendarGrid.appendChild(emptyDay);
    }

    for (let i = 1; i <= lastDayOfMonth.getDate(); i++) {
        const dayElement = document.createElement('div');
        dayElement.classList.add('calendar-day-box');
        dayElement.dataset.date = new Date(date.getFullYear(), date.getMonth(), i).toDateString(); // Store date string

        const currentDay = new Date(date.getFullYear(), date.getMonth(), i);
        currentDay.setHours(0,0,0,0); // Normalize currentDay for comparison

        const dayNumberSpan = document.createElement('span');
        dayNumberSpan.classList.add('calendar-day-number');
        dayNumberSpan.textContent = i;
        dayElement.appendChild(dayNumberSpan);

        if (currentDay.toDateString() === today.toDateString()) {
            dayElement.classList.add('today');
        }

        const workoutsOnThisDay = effectiveSchedules.filter(s =>
            new Date(s.date).toDateString() === currentDay.toDateString()
        );

        if (workoutsOnThisDay.length > 0) {
            workoutsOnThisDay.forEach(workout => {
                const indicator = document.createElement('span');
                indicator.classList.add('calendar-event-indicator');
                if (workout.completed) {
                    indicator.classList.add('completed');
                }
                dayElement.appendChild(indicator);
            });
        }

        dayElement.addEventListener('click', () => {
            // Remove 'selected' class from previously selected day
            const previouslySelected = document.querySelector('.calendar-day-box.selected');
            if (previouslySelected) {
                previouslySelected.classList.remove('selected');
            }
            // Add 'selected' class to the clicked day
            dayElement.classList.add('selected');

            selectedDate = currentDay; // Update the global selectedDate
            renderWorkoutForDay(selectedDate);
        });

        workoutCalendarGrid.appendChild(dayElement);
    }

    // After rendering, simulate a click on the selectedDate to highlight and load workouts
    const selectedDayBox = document.querySelector(`.calendar-day-box[data-date="${selectedDate.toDateString()}"]`);
    if (selectedDayBox) {
        selectedDayBox.click();
    } else {
        // If selectedDate is not in the current view, default to today if it is.
        // Or re-select the 1st of the month if it's a new month view
        const todayBox = document.querySelector(`.calendar-day-box.today`);
        if (todayBox) {
            todayBox.click();
        } else if (firstDayOfMonth.getMonth() === date.getMonth()) {
            // If it's a new month and today isn't in it, select the first day
            document.querySelector('.calendar-day-box.current-month:not(.empty)')?.click();
        }
    }
}


prevMonthBtn.onclick = () => {
    currentCalendarDate.setMonth(currentCalendarDate.getMonth() - 1);
    renderCalendar(currentCalendarDate);
};

nextMonthBtn.onclick = () => {
    currentCalendarDate.setMonth(currentCalendarDate.getMonth() + 1);
    renderCalendar(currentCalendarDate);
};


// --- Workout for the Day Section Functions ---
function renderWorkoutForDay(date) {
    workoutForDayList.innerHTML = '';
    const today = new Date();
    today.setHours(0, 0, 0, 0); // Normalize today for comparison

    if (date.toDateString() === today.toDateString()) {
        selectedDayHeader.textContent = "Today";
    } else {
        selectedDayHeader.textContent = date.toLocaleDateString('en-US', { weekday: 'long', month: 'short', day: 'numeric' });
    }

    const effectiveSchedules = filterAndSortSchedules(schedules);

    const workoutsOnSelectedDay = effectiveSchedules.filter(s =>
        new Date(s.date).toDateString() === date.toDateString()
    ).sort((a, b) => new Date(a.date) - new Date(b.date)); // Sort by time

    if (workoutsOnSelectedDay.length === 0) {
        noWorkoutsForDayMessage.style.display = 'block';
    } else {
        noWorkoutsForDayMessage.style.display = 'none';
        workoutsOnSelectedDay.forEach(s => {
            const workoutItem = document.createElement('div');
            workoutItem.classList.add('workout-item-card');
            if (s.completed) {
                workoutItem.classList.add('completed-day');
            }

            const timeString = new Date(s.date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            workoutItem.innerHTML = `
                <input type="checkbox" class="form-check-input workout-complete-checkbox" data-id="${s.id}" ${s.completed ? 'checked' : ''}>
                <div class="workout-details">
                    <div class="workout-title">${s.text}</div>
                    <div class="workout-meta">
                        <span>${timeString}</span>
                        <span>${s.workoutType.charAt(0).toUpperCase() + s.workoutType.slice(1)}</span>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-light dropdown-toggle-no-caret" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark">
                        <li><a class="dropdown-item edit-schedule-btn" href="#" data-id="${s.id}"><i class="bi bi-pencil"></i> Edit</a></li>
                        <li><a class="dropdown-item delete-schedule-btn" href="#" data-id="${s.id}"><i class="bi bi-trash"></i> Delete</a></li>
                    </ul>
                </div>
            `;
            workoutForDayList.appendChild(workoutItem);
        });
        attachScheduleActionListeners(); // Re-attach listeners for new elements
    }
}

// --- Combined Render Function ---
function renderAllData() {
    renderCalendar(currentCalendarDate); // Re-render calendar to show updated event indicators
    renderWorkoutForDay(selectedDate); // Re-render workout for the day based on the selected date
    renderAllScheduledWorkoutsTable(); // Re-render the main table
    saveToLocalStorage();
}

// Helper to filter out outdated recurring instances and sort
function filterAndSortSchedules(schedulesArray) {
    const now = new Date();
    now.setHours(0, 0, 0, 0);

    // Step 1: Identify all recurring series and their original event
    const recurringSeriesMap = new Map(); // originalRecurringId -> [instances]
    schedulesArray.forEach(s => {
        if (s.originalRecurringId) {
            if (!recurringSeriesMap.has(s.originalRecurringId)) {
                recurringSeriesMap.set(s.originalRecurringId, []);
            }
            recurringSeriesMap.get(s.originalRecurringId).push(s);
        }
    });

    let filtered = [];

    // Step 2: Process non-recurring events directly
    schedulesArray.filter(s => !s.originalRecurringId).forEach(s => filtered.push(s));

    // Step 3: Process recurring series
    recurringSeriesMap.forEach(seriesInstances => {
        // Sort instances by date to ensure chronological order
        seriesInstances.sort((a, b) => new Date(a.date) - new Date(b.date));

        let lastKeptInstance = null;
        seriesInstances.forEach(instance => {
            if (instance.recurrenceType === 'none' && !instance.originalRecurringId) {
                // This instance was detached. Add it as a standalone event.
                filtered.push(instance);
            } else {
                // For recurring instances, only add if it's the first in a continuous sequence,
                // or if its recurrence details are still valid.
                // This logic is primarily to handle instances that might have been "deleted" from the series
                // by updating the original event and regenerating.

                // If this instance has completed status, we always want to show it as a historical record
                // even if it falls out of a newly defined recurrence range.
                if (instance.completed || new Date(instance.date).getTime() >= now.getTime()) {
                    // This heuristic attempts to keep the "most up-to-date" version of a recurring event for a given day
                    // If there are multiple instances for the same day (e.g., due to regeneration issues), pick the latest edited or the first.
                    const existingForDay = filtered.find(f =>
                        f.originalRecurringId === instance.originalRecurringId &&
                        new Date(f.date).toDateString() === new Date(instance.date).toDateString()
                    );
                    if (!existingForDay) {
                        filtered.push(instance);
                    } else {
                        // If there's already one, replace it if the current 'instance' is more recent
                        // (e.g., has a newer modification timestamp or is simply the one from the regeneration run)
                        // This assumes the regeneration creates the most "correct" set.
                        const existingIndex = filtered.findIndex(f => f === existingForDay);
                        if (existingIndex !== -1) {
                            filtered[existingIndex] = instance; // Replace with the current instance
                        }
                    }
                }
            }
        });
    });

    // Final sort for display
    return filtered.sort((a, b) => new Date(a.date) - new Date(b.date));
}

// --- Initialization ---
document.addEventListener('DOMContentLoaded', () => {
    loadFromLocalStorage();
    // Set selectedDate to today initially
    selectedDate = new Date();
    selectedDate.setHours(0, 0, 0, 0); // Normalize

    renderAllData(); // Initial render of all components
});
