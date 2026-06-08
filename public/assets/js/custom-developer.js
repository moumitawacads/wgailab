document.addEventListener('DOMContentLoaded', function () {

    /** Clock In functionality */

    const buttons = document.querySelectorAll('.clock-in-btn');

    buttons.forEach(button => {

        button.addEventListener('click', function () {

            let id = this.dataset.id;
            let date = this.dataset.date;
            let time = this.dataset.time;
            let url = this.dataset.url; // ✅ dynamic route

            let scheduleDateTime = new Date(date + ' ' + time);
            let now = new Date();
            let scheduleDate = new Date(scheduleDateTime.toDateString());
            let todayDate = new Date(now.toDateString());

            let allowedTime = new Date(scheduleDateTime.getTime() - (15 * 60 * 1000));

            // ⛔ Validation
            if (now < allowedTime) {
                alert("You can clock in only 15 minutes before scheduled time!");
                return;
            }

            if (scheduleDate.getTime() !== todayDate.getTime()) {
                alert("You can only clock in on the scheduled day!");
                return;
            }

            // ✅ AJAX
            fetch(url, {
                method: "POST",
                credentials: "same-origin",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json", // ✅ ADD THIS
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    schedule_id: id
                })
            })
            .then(res => res.json())
            .then(data => {
                 console.log(data); 
                alert(data.message);

                if (data.status) {
                    button.innerText = "✔ Clocked In";
                    button.classList.remove('btn-success');
                    button.classList.add('btn-secondary');
                    button.disabled = true;
                    location.reload();
                }

            });

        });

    });



    /** Fetch Attendance  log for every week */
    document.addEventListener('click', function (e) {

    if (e.target.classList.contains('view-schedules')) {

        let button = e.target;

        let userId = button.dataset.user;
        let start = button.dataset.start;
        let end = button.dataset.end;
        let url = button.dataset.url;
        let notes = button.dataset.note || "";

        let modalEl = document.getElementById('scheduleModal');
        let modal = new bootstrap.Modal(modalEl);
        modal.show();

        // Reset UI
        document.getElementById('schedule-loader').style.display = 'block';
        document.getElementById('schedule-table').classList.add('d-none');
        document.getElementById('schedule-data').innerHTML = '';

        fetch(`${url}?user_id=${userId}&start=${start}&end=${end}`)
            .then(res => res.json())
            .then(data => {

                let html = '';

                data.forEach(item => {
                    let attendance = item.attendances && item.attendances.length > 0 
                        ? item.attendances[0] 
                        : null;

                     let status = '<span class="text-danger">Absent</span>';

                    if (attendance && attendance.clock_in_time) {

                        let scheduleDateTime = new Date(item.schedule_date + ' ' + item.schedule_time);
                        let clockInTime = new Date(attendance.clock_in_time);
                        let diffMinutes = (clockInTime - scheduleDateTime) / (1000 * 60);
                       
                        // within 15 mins (before or after)
                        if (Math.abs(diffMinutes) < 15) {
                            status = '<span class="text-success">Present</span>';
                        } else if (Math.abs(diffMinutes) > 15 && Math.abs(diffMinutes) < 30) {
                            status = '<span class="text-danger">Late</span>';
                        }
                        else if (Math.abs(diffMinutes) > 30) {
                            status = '<span class="text-danger">Absent</span>';
                        }
                        else {
                            status = '<span class="text-success">Present</span>'; // early but within range
                        }
                    }
                    html += `
                        <tr>
                            <td>${item.mainclass?.name ?? '-'}</td>
                            <td>${formatDate(item.schedule_date)}</td>
                            <td>${formatTime(item.schedule_time)}</td>
                            <td>
                                ${attendance && attendance.clock_in_time 
                                    ? formatTimeOnly(attendance.clock_in_time) 
                                    : 'Not Clocked In'}
                            </td>
                            <td>${status}</td>
                        </tr>
                    `;
                });

                let notesEl = document.getElementById('notes-content');
                if (notesEl) {
                    document.getElementById('notes-content').innerHTML = notes;
                    document.getElementById('schedule-notes').classList.remove('d-none');
                }
                document.getElementById('schedule-data').innerHTML = html;                
                document.getElementById('schedule-loader').style.display = 'none';
                document.getElementById('schedule-table').classList.remove('d-none');
                
            });
    }
});


    /** Fetch Attendance  log for every week */
    document.addEventListener('click', function (e) {

        if (e.target.classList.contains('compensation_form')) {

            let button = e.target;

            let userId = button.dataset.user;
            let start = button.dataset.start;
            let end = button.dataset.end;
            let reportId = button.dataset.reportId;
            let displayname = button.dataset.username;

            // Set hidden fields
            document.getElementById('comp_user_id').value = userId;
            document.getElementById('comp_week_start').value = start;
            document.getElementById('comp_week_end').value = end;
            document.getElementById('comp_report_id').value = reportId;

            // Display values
            document.getElementById('display_user').value = displayname;
            document.getElementById('display_week').value = start + ' to ' + end;

            // Show modal
            let modalEl = document.getElementById('compensationFormModal');
            let modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    });

    document.addEventListener('submit', function (e) {

        if (e.target.id === 'compensationForm') {

            e.preventDefault();

            let form = e.target;
            let formData = new FormData(form);

            fetch(COMPENSATION_URL, {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {

                if (data.success) {
                    alert(data.message);

                    // close modal
                    let modalEl = document.getElementById('compensationFormModal');
                    bootstrap.Modal.getInstance(modalEl).hide();

                    form.reset();
                } else {
                    alert(data.message);
                }

            })
            .catch(err => {
                console.error(err);
                alert('Error occurred');
            });
        }
    });

    document.addEventListener('click', function (e) {

        // Handle both approve & reject
        if (e.target.classList.contains('approve-request') || e.target.classList.contains('reject-request')) {

            e.preventDefault();

            let id = e.target.dataset.id;
            let status = e.target.dataset.status;
            let week_id=e.target.dataset.weekid;

            if (!confirm('Are you sure you want to perform this action?')) return;

            fetch('/admin/compensation/update-status', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    id: id,
                    week_id:week_id,
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);

                    // Optional: update UI without reload
                    location.reload();
                } else {
                    alert(data.message || 'Something went wrong');
                }
            })
            .catch(error => {
                console.error(error);
                alert('Error occurred while processing request');
            });
        }

    });

});


// Helper functions
function formatDate(date) {
    let d = new Date(date);
    return d.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
}

function formatTime(time) {
    let t = new Date('1970-01-01T' + time);
    return t.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
}

function formatTimeOnly(datetime) {
    if (!datetime) return '-';

    let date = new Date(datetime);

    return date.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    });
}


