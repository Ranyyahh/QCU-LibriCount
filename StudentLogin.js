function getCurrentTime() {
    const now = new Date();
    let hours = now.getHours();
    let minutes = now.getMinutes();

    const ampm = hours >= 12 ? "PM" : "AM";

    hours = hours % 12;
    hours = hours ? hours : 12; 
    
    minutes = minutes < 10 ? "0" + minutes : minutes;

    return `${hours}:${minutes} ${ampm}`;
}

function handleTimeIn() {
    const studentNo = document.getElementById('studentNo').value.trim();
    const lastName = document.getElementById('lastName').value.trim();
    const firstName = document.getElementById('firstName').value.trim();

    if (studentNo === "" || lastName === "" || firstName === "") {
        alert("Please fill in the required fields.");
        return;
    }

    const time = getCurrentTime();

    console.log("Time In Clicked");
    console.log(`Student: ${studentNo}, Name: ${firstName} ${lastName}, Time In: ${time}`);

    alert(`Success! Time IN recorded for Student No: ${studentNo}\nTime: ${time}`);
    
    // Optional: Reset the form
    document.getElementById('accessForm').reset();

    // Redirect to StudentUI.html
    window.location.href = "StudentUI.html";
}


function handleTimeOut() {
    const studentNo = document.getElementById('studentNo').value.trim();

    if (studentNo === "") {
        alert("Please enter Student No. to Time Out.");
        return;
    }

    const time = getCurrentTime();

    console.log("Time Out Clicked");
    console.log(`Student No: ${studentNo}, Time Out: ${time}`);

    alert(`Success! Time OUT recorded for Student No: ${studentNo}\nTime: ${time}`);
    
    document.getElementById('accessForm').reset();
}