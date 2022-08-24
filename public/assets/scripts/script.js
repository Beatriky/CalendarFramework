// getting the actual date
let today = new Date();
let currentDay = today.getDate();
let month = today.getMonth();
let monthPlus = month + 1;
let year = today.getFullYear();
let calendarBody = document.getElementById("days");
//url
let documentURL = new URL(window.location.href);
console.log(currentDay);
let urlLocation = documentURL.searchParams.get('location');
let urlDate = documentURL.searchParams.get('date');

let months = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December"
];
let selectedDate;

// let dateAptElement = document.getElementById("dateForm");
// dateAptElement.min = moment().format("YYYY-MM-DD");
// dateAptElement.value = moment().format("YYYY-MM-DD");
console.log(urlDate);

console.log(month + 1);

// if (urlLocation !== null) {
//     let parent = document.getElementById('locationID');
//     let children = parent.querySelectorAll('*');
//     children.forEach(child => {
//         if (child.value == urlLocation) {
//             child.setAttribute('selected', 'selected');
//         }
//     })
// }

if (urlDate !== null) {
    let dateSplit = urlDate.split('-');
    let dataText = dateSplit[2];
    let monthText = dateSplit[1];
    let yearText = dateSplit[0];
    document.getElementById('schedule').innerText = "Schedule for " + dataText + " " + months[monthText] + ' ' + yearText;
    currentDay = dataText;
    month = monthText;
    year = yearText;
} else {
    document.getElementById('schedule').innerText = "Schedule for " + currentDay + " " + months[month] + ' ' + year;
}

function dateURL() {
    document.getElementById('selectedDateForm').value = selectedDate;
    document.getElementById('selectedDateSubmit').click();
}


function showCalendar(month, year) {
    let firstDay = (new Date(year, month)).getDay();
    calendarBody.innerHTML = "";
    let totalDays = daysInMonth(month, year);
    blankDates(firstDay);
    for (let day = 1; day <= totalDays; day++) {
        let cell = document.createElement("li");
        let cellText = document.createTextNode(day);
        if (day == currentDay) {
            cell.classList.add('active');
        }
        if (currentDay === day &&
            month === today.getMonth() &&
            year === today.getFullYear()) {
            cell.classList.add("active");
        }
        cell.setAttribute("data-day", day);
        cell.setAttribute("data-month", month);
        cell.setAttribute("data-year", year);
        cell.classList.add("singleDay");
        cell.appendChild(cellText);
        calendarBody.appendChild(cell);
    }
    document.getElementById("month").innerHTML = months[month];
    document.getElementById("year").innerHTML = year;
    eventActivator();
}

function daysInMonth(month, year) {
    // day 0 here returns the last day of the PREVIOUS month
    return new Date(year, month + 1, 0).getDate();
}

function blankDates(count) {
    // looping to add the correct amount of blank days to the calendar
    for (let x = 0; x < count; x++) {
        let cell = document.createElement("li");
        let cellText = document.createTextNode("");
        cell.appendChild(cellText);
        calendarBody.appendChild(cell);
    }
}

function refillSelectedDay(year, month, day) {
    selectedDate = year + "-" + month + "-" + day;
}

// next and previous buttons
let nextButton = document.getElementById("next");
let previousButton = document.getElementById("prev");

nextButton.onclick = function () {
    document.getElementById('next').addEventListener('click', event => {
        next();
        refillSelectedDay(year, month, currentDay);
        //  dateURL();
    });

};
previousButton.onclick = function () {
    document.getElementById('prev').addEventListener('click', event => {
        previous();
        refillSelectedDay(year, month, currentDay);
        // dateURL();
    });
};

function next() {
    year = month === 11 ? year + 1 : year;
    month = (month + 1) % 12;
    // if (month == 11) {
    //     month = 0;
    //     year++;
    // } else {
    //     month++;
    // }
    showCalendar(month, year);
}

function previous() {
    // if (month == 0) {
    //     month = 11;
    //     year--;
    // } else {
    //     month--;
    // }
    year = month === 0 ? year - 1 : year;
    month = month === 0 ? 11 : month - 1;
    showCalendar(month, year);
}

let optionMonth = document.getElementById("monthSelect");
let optionYear = document.getElementById("yearSelect");

function jump() {
    year = parseInt(optionYear.value);
    month = parseInt(optionMonth.value);
    showCalendar(month, year);
}

//calling/initializing calendar
showCalendar(month, year);

function renderCalendar(year, month) {
    let startOfMonth = new Date(year, month).getDay();
    let numOfDays = 32 - new Date(year, month, 32).getDate();
    let renderNum = 1;

    let renderMonth = document.getElementById('month');
    let renderYear = document.getElementById('year');

    renderMonth.textContent = months['${month}'];
    renderYear.textContent = year;

    let tableBody = document.getElementById('table-body');

    for (let i = 0; i < 6; i++) {
        let row = document.createElement('tr');
        let c;
        for (let j = 0; j < 7; c++) {
            if (i === 0 && c < startOfMonth) {
                let td = document.createElement('td');
                td.classList.add('empty');
                row.append(td);
            } else if (renderNum > numOfDays) {
                break;
            } else {
                let td = document.createElement('td');
                td.textContent = renderNum;
                row.append(td);
                renderNum++;
            }
        }
    }

}

function eventActivator() {
    const daysVar = document.querySelectorAll(".singleDay");
    daysVar.forEach(item => {
        item.addEventListener('click', event => {
            //document.getElementById('selectedDateSubmit').click();
            let dataText = event.target.dataset.day;
            let monthText = event.target.dataset.month;
            let yearText = event.target.dataset.year;
            document.getElementById('schedule').innerText = " Schedule for " + dataText + " " + months[month] + ' ' + yearText;
            selectedDate = dataText + '-' + monthText + '-' + yearText;

            refillSelectedDay(yearText, monthText, dataText);
            sendAxios(yearText + '-' + Number(Number(monthText)+1) + '-' + dataText, document.getElementById('location').value);
            //dateURL();
        })
    })
}


console.log(document.getElementById('locationID').value);

function sendAxios(datex, location) {
    const data = new FormData();
    data.set('date', datex);
    data.set('location', location);
    data.set('_token', document.getElementById('_token').value);
    axios.post('/requestAppointments',
        data,
    ).then(response => {
        document.getElementById('appointments').innerHTML = '';
        console.log(response.data)
        if (response.data.length===0){
            document.getElementById('appointments').innerHTML = '';
        } else {
            for (let i=0; i<=response.data.length; i++) {
                document.getElementById('appointments').innerHTML += response.data[i].name;
                console.log(response.data[i].name)
            }
        }
    });
}
