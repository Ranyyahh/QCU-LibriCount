/* =======================================
   DYNAMIC GAUGE + STATUS FUNCTION
   ======================================= */

function updateGauge(percent, current, max) {

    // Update % text
    document.querySelector(".percent").textContent = percent + "%";

    // Update numeric count
    document.querySelector(".count").textContent = `${current}/${max}`;

    // Update gauge arc
    document.querySelector(".meter").style.strokeDasharray = `${percent}, 100`;

    // Select elements
    let statusText = document.querySelector(".status");
    let meterArc = document.querySelector(".meter");
    let percentText = document.querySelector(".percent");
    let head = document.querySelector(".icon-person .head");
    let body = document.querySelector(".icon-person .body");

    let color = "";

    /* STATUS LOGIC */
    if (percent <= 50) {
        statusText.textContent = "NORMAL";
        color = "#4ca626";  // green
    }
    else if (percent > 50 && percent < 90) {
        statusText.textContent = "NEAR FULL";
        color = "#f5bb13";  // yellow
    }
    else {
        statusText.textContent = "FULL";
        color = "#b30000";  // red
    }

    // Apply color to all elements
    statusText.style.color = color;
    meterArc.style.stroke = color;
    percentText.style.color = color;

    // Person icon color
    head.style.background = color;
    body.style.background = color;
}

/* =======================================
   DATE/TIME UPDATER
   ======================================= */
function updateDateTime() {
    const now = new Date();

    const options = {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        hour12: true
    };

    const formatted = now.toLocaleString("en-US", options);

    document.getElementById("datetime").textContent = `As of ${formatted}`;
}

// Update immediately and every second
updateDateTime();
setInterval(updateDateTime, 1000);


/* =======================================
   INITIAL RUN (change anytime)
   ======================================= */

let current = 50; // people inside
let max = 50;
let percent = Math.round((current / max) * 100);

updateGauge(percent, current, max);