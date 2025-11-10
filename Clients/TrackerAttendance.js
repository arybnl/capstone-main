// TrackerAttendance.js

// --- Data
let attendanceRecords = [
  // Sample attendance data
  { date: new Date(new Date().setDate(new Date().getDate() - 3)), clockIn: '08:00 AM', clockOut: '05:00 PM', hours: '9h 0m', status: 'Present' },
  { date: new Date(new Date().setDate(new Date().getDate() - 2)), clockIn: '09:15 AM', clockOut: '06:00 PM', hours: '8h 45m', status: 'Present' },
  { date: new Date(new Date().setDate(new Date().getDate() - 1)), clockIn: '07:30 AM', clockOut: '12:00 PM', hours: '4h 30m', status: 'Partial' },
  { date: new Date(new Date().setDate(new Date().getDate() - 7)), clockIn: '08:00 AM', clockOut: '05:00 PM', hours: '9h 0m', status: 'Present' },
  { date: new Date(new Date().setDate(new Date().getDate() - 10)), clockIn: '08:30 AM', clockOut: '05:30 PM', hours: '9h 0m', status: 'Present' },
  { date: new Date(new Date().setFullYear(new Date().getFullYear() - 1, 0, 15)), clockIn: '09:00 AM', clockOut: '06:00 PM', hours: '9h 0m', status: 'Present' },
  { date: new Date(new Date().setFullYear(new Date().getFullYear() - 1, 1, 20)), clockIn: '08:00 AM', clockOut: null, hours: '0h 0m', status: 'Absent' }, // Example for absent/not clocked out
];

let isClockedIn = false;
let clockInTime = null;
const today = new Date();
const todayDateEl = document.getElementById('todayDate');
const clockStatusEl = document.getElementById('clockStatus');
const clockInOutTimeEl = document.getElementById('clockInOutTime');
const qrInstructionEl = document.getElementById('qrInstruction');
const qrCodePlaceholderEl = document.getElementById('qrCodePlaceholder'); // This element isn't strictly used for showing/hiding, but the image and button within it are.
const userQRCodeEl = document.getElementById('userQRCode');
const clockInBtn = document.getElementById('clockInBtn');
const clockOutBtn = document.getElementById('clockOutBtn');
const attendanceSummaryBody = document.getElementById('attendanceSummaryBody');

// --- Helper Functions
function formatTime(date) {
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
}

function formatDateForTable(date) {
    const todayYear = new Date().getFullYear();
    const recordYear = date.getFullYear();
    const options = { weekday: 'short', month: 'short', day: 'numeric' };

    if (todayYear !== recordYear) {
        options.year = 'numeric';
    }
    return date.toLocaleDateString('en-US', options);
}

function calculateHours(inTime, outTime) {
    if (!inTime || !outTime) return '0h 0m';

    // Parse times assuming they are from the same day for simplicity in this template
    // For robust multi-day calculation, you'd need full Date objects.
    const inDate = new Date(`2000/01/01 ${inTime}`);
    const outDate = new Date(`2000/01/01 ${outTime}`);

    let diff = outDate - inDate; // Difference in milliseconds
    if (diff < 0) { // Clock out on next day (e.g., worked past midnight)
        diff += 24 * 60 * 60 * 1000;
    }

    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

    return `${hours}h ${minutes}m`;
}

function getStatus(record) {
    if (record.clockIn && record.clockOut) {
        return 'Present';
    } else if (record.clockIn && !record.clockOut) {
        return 'Partial'; // Clocked in but not out yet
    }
    return 'Absent';
}

// --- Today's Attendance Logic
function initializeTodayAttendance() {
    todayDateEl.textContent = today.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

    // Check if there's an ongoing attendance for today
    const todayRecord = attendanceRecords.find(record =>
        record.date.toDateString() === today.toDateString() && !record.clockOut
    );

    if (todayRecord) {
        isClockedIn = true;
        clockInTime = new Date(todayRecord.date); // Reconstruct full date for clock in (if needed for further calculation)
        const storedInTime = todayRecord.clockIn;

        clockStatusEl.textContent = 'You are currently Clocked In.';
        clockInOutTimeEl.textContent = `Clock In: ${storedInTime}`;
        userQRCodeEl.style.display = 'none'; // Hide QR after clock in
        qrInstructionEl.style.display = 'none';
        clockInBtn.style.display = 'none';
        clockOutBtn.style.display = 'block';
    } else {
        resetClockInOutUI();
    }
}

function resetClockInOutUI() {
    isClockedIn = false;
    clockInTime = null;
    clockStatusEl.textContent = 'You are not clocked in.';
    clockInOutTimeEl.textContent = '';
    userQRCodeEl.style.display = 'none'; // Always hide QR when not clocking in
    qrInstructionEl.style.display = 'block';
    qrInstructionEl.textContent = 'Scan this QR code at the gym to Clock In:';
    clockInBtn.style.display = 'block';
    clockOutBtn.style.display = 'none';
}

// Event listener for Clock In button
clockInBtn.addEventListener('click', () => {
    // In a real application, the QR scan would trigger this
    // For this template, we'll simulate a clock-in
    isClockedIn = true;
    clockInTime = new Date(); // Set clock-in time to now

    const newRecord = {
        date: new Date(today), // Store only date for display, actual time handled by clockInTime variable
        clockIn: formatTime(clockInTime),
        clockOut: null,
        hours: '0h 0m',
        status: 'Partial'
    };
    attendanceRecords.push(newRecord); // Add new record to data

    clockStatusEl.textContent = 'You are currently Clocked In.';
    clockInOutTimeEl.textContent = `Clock In: ${newRecord.clockIn}`;
    userQRCodeEl.style.display = 'none'; // Hide QR after clock in
    qrInstructionEl.style.display = 'none';
    clockInBtn.style.display = 'none';
    clockOutBtn.style.display = 'block';

    renderAttendanceSummary(); // Update summary table
    alert('You have successfully Clocked In!');
});

// Event listener for Clock Out button
clockOutBtn.addEventListener('click', () => {
    if (isClockedIn && clockInTime) {
        const clockOutDate = new Date();
        const outTime = formatTime(clockOutDate);

        // Find the current day's record that is not clocked out
        const todayRecordIndex = attendanceRecords.findIndex(record =>
            record.date.toDateString() === today.toDateString() && !record.clockOut
        );

        if (todayRecordIndex !== -1) {
            const currentRecord = attendanceRecords[todayRecordIndex];
            currentRecord.clockOut = outTime;
            currentRecord.hours = calculateHours(currentRecord.clockIn, outTime);
            currentRecord.status = getStatus(currentRecord);
        }

        resetClockInOutUI();
        renderAttendanceSummary(); // Update summary table
        alert('You have successfully Clocked Out!');
    } else {
        alert('You are not currently clocked in.');
    }
});


// --- Attendance Summary Logic
function renderAttendanceSummary(filter = 'weekly') {
    attendanceSummaryBody.innerHTML = ''; // Clear existing records

    const now = new Date();
    // Re-initialize 'now' for filtering to avoid issues if 'now' was modified by other filters
    const currentFilterDate = new Date();

    const filteredRecords = attendanceRecords.filter(record => {
        const recordDate = new Date(record.date);
        switch (filter) {
            case 'weekly':
                const firstDayOfWeek = new Date(currentFilterDate.setDate(currentFilterDate.getDate() - currentFilterDate.getDay())); // Sunday of current week
                const lastDayOfWeek = new Date(firstDayOfWeek);
                lastDayOfWeek.setDate(firstDayOfWeek.getDate() + 6); // Saturday of current week
                // Reset time components for accurate date comparison
                firstDayOfWeek.setHours(0,0,0,0);
                lastDayOfWeek.setHours(23,59,59,999);
                recordDate.setHours(12,0,0,0); // Set to middle of day to avoid timezone issues

                return recordDate >= firstDayOfWeek && recordDate <= lastDayOfWeek;
            case 'monthly':
                return recordDate.getMonth() === currentFilterDate.getMonth() && recordDate.getFullYear() === currentFilterDate.getFullYear();
            case 'yearly':
                return recordDate.getFullYear() === currentFilterDate.getFullYear();
            default:
                return true;
        }
    }).sort((a, b) => new Date(b.date) - new Date(a.date)); // Sort by date descending

    if (filteredRecords.length === 0) {
        attendanceSummaryBody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4">No attendance records for this period.</td></tr>`;
        return;
    }

    filteredRecords.forEach(record => {
        const row = document.createElement('tr');
        const statusClass = record.status ? record.status.toLowerCase() : 'absent'; // present, absent, partial

        row.innerHTML = `
            <td>${formatDateForTable(record.date)}</td>
            <td>${record.clockIn || '—'}</td>
            <td>${record.clockOut || '—'}</td>
            <td>${record.hours}</td>
            <td class="status-${statusClass}">${record.status || 'Absent'}</td>
        `;
        attendanceSummaryBody.appendChild(row);
    });
}

// --- Filter Buttons Event Listeners
document.getElementById('filterWeekly').addEventListener('change', () => renderAttendanceSummary('weekly'));
document.getElementById('filterMonthly').addEventListener('change', () => renderAttendanceSummary('monthly'));
document.getElementById('filterYearly').addEventListener('change', () => renderAttendanceSummary('yearly'));


// --- Initial calls on page load
document.addEventListener('DOMContentLoaded', () => {
    initializeTodayAttendance();
    renderAttendanceSummary('weekly'); // Render weekly summary by default
});

// The content for QR code will be generated on click of Clock In button
// The userQRCode element is initially hidden and will be shown by the clock-in logic
// when the user is expected to scan it. When a real QR scanning integration is in place,
// this part will be handled differently. For now, clock-in is a button click.
