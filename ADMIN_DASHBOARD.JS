document.addEventListener("DOMContentLoaded", () => {
    const placeholderData = {
        percent: 0,
        current: 21,
        max: 50,
        activity: [
            { type: "+1 Entry", time: "10:31 AM", date: "12/05/25" },
            { type: "+1 Entry", time: "10:32 AM", date: "12/05/25" },
            { type: "-1 Entry", time: "10:35 AM", date: "12/05/25" },
            { type: "+1 Entry", time: "10:40 AM", date: "12/05/25" },
            { type: "-1 Entry", time: "10:45 AM", date: "12/05/25" },
            { type: "-1 Entry", time: "10:45 AM", date: "12/05/25" },
            { type: "-1 Entry", time: "10:45 AM", date: "12/05/25" },
            { type: "-1 Entry", time: "10:45 AM", date: "12/05/25" },
            { type: "-1 Entry", time: "10:45 AM", date: "12/05/25" },
            { type: "-1 Entry", time: "10:45 AM", date: "12/05/25" },
            { type: "-1 Entry", time: "10:45 AM", date: "12/05/25" },
            { type: "-1 Entry", time: "10:45 AM", date: "12/05/25" },
        ]
    };

    // Load dashboard with placeholder
    loadDashboard(placeholderData);

    function loadDashboard(data) {
    const percent = Math.round((data.current / data.max) * 100); // compute dynamically
    document.getElementById("percent").textContent = percent + "%";
    document.getElementById("countNum").textContent = `${data.current}/${data.max}`;
    document.querySelector(".meter").style.strokeDasharray = `${percent}, 100`;

    updateStatus(percent); // pass the dynamically calculated percent

    // Update recent activity
    loadActivity(data.activity);

    // Update date label
    document.getElementById("dateLabel").textContent = `[as of ${new Date().toLocaleDateString()}]`;
}


    function updateStatus(percent) {
    const status = document.getElementById("status");
    const meter = document.querySelector(".meter");
    const icon = document.querySelectorAll(".icon-person .head, .icon-person .body");
    const percentcolor = document.querySelector("#percent");

    let color = "";

    if (percent <= 50) {
        status.textContent = "NORMAL";
        color = "#4ca626";
    } else if (percent > 50 && percent < 90) {
        status.textContent = "NEAR FULL";
        color = "#f5bb13";
    } else {
        status.textContent = "FULL";
        color = "#b30000";
    }

    // Apply color
    status.style.color = color;
    meter.style.stroke = color;
    percentcolor.style.color = color;

    // FIX: apply to both head and body
    icon.forEach(part => part.style.background = color);
}


    function loadActivity(activity) {
        const list = document.getElementById("activityList");
        list.innerHTML = ""; // clear first

        activity.forEach(item => {
            const box = document.createElement("div");
            box.classList.add("activity-item");

            if (item.type.includes("-")) box.classList.add("minus");

            box.innerHTML = `
                <div>${item.type}</div>
                <div>${item.time}</div>
                <div>${item.date}</div>
            `;
            list.appendChild(box);
        });
    }
});