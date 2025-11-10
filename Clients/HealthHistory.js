
        // Sample health assessment data
        const assessments = {
            1: {
                title: "Annual Physical Exam",
                date: "2024-06-01",
                info: "Height: 175cm<br>Weight: 70kg<br>Blood Pressure: 120/80<br>Notes: All results normal."
            },
            2: {
                title: "Blood Pressure Check",
                date: "2024-05-15",
                info: "Blood Pressure: 130/85<br>Notes: Slightly elevated, advised to monitor."
            }
        };

        // Sort dropdown logic
        const sortBtn = document.getElementById('sortBtn');
        const sortDropdown = document.getElementById('sortDropdown');
        let sortOrder = 'newest';

        sortBtn.addEventListener('click', function(e) {
            sortDropdown.classList.toggle('d-none');
        });

        document.addEventListener('click', function(e) {
            if (!sortBtn.contains(e.target) && !sortDropdown.contains(e.target)) {
                sortDropdown.classList.add('d-none');
            }
        });

        sortDropdown.querySelectorAll('.sort-dropdown-option').forEach(opt => {
            opt.addEventListener('click', function() {
                sortOrder = this.getAttribute('data-sort');
                sortDropdown.classList.add('d-none');
                sortHistoryList();
            });
        });

        // Sorting logic
        function sortHistoryList() {
            const list = document.getElementById('historyList');
            const items = Array.from(list.querySelectorAll('.history-item'));
            items.sort((a, b) => {
                const dateA = new Date(a.querySelector('.history-date').textContent);
                const dateB = new Date(b.querySelector('.history-date').textContent);
                return sortOrder === 'newest' ? dateB - dateA : dateA - dateB;
            });
            items.forEach(item => list.appendChild(item));
        }

        // Health assessment modal logic
        document.querySelectorAll('.history-item').forEach(item => {
            item.addEventListener('click', function(e) {
                // Prevent archive icon click from opening assessment modal
                if (e.target.closest('.archive-btn')) return;
                const id = this.getAttribute('data-id');
                const assessment = assessments[id];
                if (assessment) {
                    document.getElementById('assessmentModalLabel').textContent = assessment.title;
                    document.getElementById('assessmentModalBody').innerHTML = `
                        <div><strong>Date:</strong> ${assessment.date}</div>
                        <div class="mt-2">${assessment.info}</div>
                    `;
                    const modal = new bootstrap.Modal(document.getElementById('assessmentModal'));
                    modal.show();
                }
            });
        });

        // Archive modal logic
        let archiveId = null;
        document.querySelectorAll('.archive-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                archiveId = this.getAttribute('data-id');
            });
        });

        document.getElementById('archiveYesBtn').addEventListener('click', function() {
            if (archiveId) {
                const item = document.querySelector(`.history-item[data-id="${archiveId}"]`);
                if (item) item.remove();
                archiveId = null;
                const modal = bootstrap.Modal.getInstance(document.getElementById('archiveModal'));
                modal.hide();
            }
        });