// ==============================
// Fetch student info when typing
// ==============================
const studentNoInput = document.getElementById("studentNo");

studentNoInput.addEventListener("input", async () => {
    const studentNo = studentNoInput.value.trim();
    if (studentNo.length === 0) {
        clearStudentFields();
        return;
    }

    try {
        const res = await fetch(`getStudent.php?studentNo=${encodeURIComponent(studentNo)}`);
        const data = await res.json();

        if (data.error) {
            clearStudentFields();
        } else {
            document.getElementById("firstName").value = data.firstName;
            document.getElementById("middleName").value = data.middleName;
            document.getElementById("lastName").value = data.lastName;
            document.getElementById("course").value = data.course;
            document.getElementById("yearLvl").value = data.yearLvl;
        }
    } catch (err) {
        console.error(err);
    }
});

function clearStudentFields() {
    document.getElementById("firstName").value = "";
    document.getElementById("middleName").value = "";
    document.getElementById("lastName").value = "";
    document.getElementById("course").value = "";
    document.getElementById("yearLvl").value = "";
}

// ==============================
// Handle Time In / Time Out
// ==============================
function handleTimeIn() {
    recordAttendance("timein");
}

function handleTimeOut() {
    recordAttendance("timeout");
}

function recordAttendance(action) {
    const studentNo = studentNoInput.value.trim();
    if (studentNo === "") {
        alert("Please enter Student No.");
        return;
    }

    const formData = new FormData();
    formData.append("studentNo", studentNo);
    formData.append("action", action);

    fetch("Studentfunction.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            document.getElementById("accessForm").reset();
            clearStudentFields();
        } else {
            alert(data.error);
        }
    })
    .catch(err => console.error(err));
}
