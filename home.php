    <?php
    // Start the session
    session_start();

    // Check if the user is logged in
    if (!isset($_SESSION['email'])) {
        // Redirect to login page if not logged in
        header("Location: index.html");
        exit();
    }

    echo "<h1>Welcome, " . $_SESSION['email'] . "!</h1>";
    echo "<p>You are logged in successfully.</p>";
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Campus Navigation System</title>
        <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
        <style>
            body {
                font-family: Arial, sans-serif;
                display: flex;
                flex-direction: column;
                align-items: center;
                margin: 0;
                padding: 0;
                background-color: #f1f1f1;
            }

            h1 {
                font-size: 1.5em;
                color: #444;
            }

            #form-container {
                position: absolute;
                top: 20px;
                left: 50%;
                transform: translateX(-50%);
                display: flex;
                align-items: center;
                padding: 10px;
                background: white;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                border-radius: 8px;
                z-index: 1000;
            }

            #form-container select {
                padding: 8px;
                margin-right: 10px;
                border: 1px solid #ddd;
                border-radius: 4px;
                font-size: 1em;
                color: #333;
            }

            #form-container button {
                padding: 8px 12px;
                background-color: #4285f4;
                color: white;
                border: none;
                border-radius: 4px;
                font-size: 1em;
                cursor: pointer;
            }

            #form-container button:hover {
                background-color: #357ae8;
            }

            #map {
                height: 90vh;
                width: 100vw;
                max-width: 1200px;
                border: 1px solid #ccc;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
                margin-top: 80px;
            }
        </style>
    </head>
    <body>

    <div id="form-container">
        <label for="start">From: </label>
        <select id="start"></select>

        <label for="end">To: </label>
        <select id="end"></select>
        
        <button type="button" onclick="findRoute()">Show Route</button>
    </div>

    <div id="map"></div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        const map = L.map('map').setView([31.396, 75.535], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const locations = {
        "Main Gate": {lat: 31.394231, lng: 75.533087, connections: {"Open Air Theatre": 1, "Right Round About": 1, "Left Round About": 1}},
        "Open Air Theatre": {lat: 31.394643, lng: 75.533784, connections: {"Main Gate": 1, "OAT Road Front": 1}},
        "Right Round About": {lat: 31.395797, lng: 75.533451, connections: {"Main Gate": 1, "MID OAT and right Round about": 1, "BasketBall Court": 1}},
        "Left Round About": {lat: 31.393987, lng: 75.534677, connections: {"Main Gate": 1, "MID OAT and left round about": 1, "Guest House": 1}},
        "OAT Road Front": {lat: 31.395273, lng: 75.534746, connections: {"MID OAT and right Round about": 1, "MID OAT and left round about": 1, "Open Air Theatre": 1}},
        "Central Seminar Hall": {lat: 31.395735, lng: 75.534689, connections: {"MID OAT and right Round about": 1, "Administrative Building": 1, "Lib Walkway Front": 1}},
        "MID OAT and right Round about": {lat: 31.395509, lng: 75.534470, connections: {"OAT Road Front": 1, "Central Seminar Hall": 1, "Right Round About": 1}},
        "MID OAT and left round about": {lat: 31.394763, lng: 75.534999, connections: {"OAT Road Front": 1, "IT Building": 1, "Left Round About": 1}},
        "IT Building": {lat: 31.395136, lng: 75.535654, connections: {"MID OAT and left round about": 1, "Administrative Building": 1, "IT Building walkway front": 1}},
        "Administrative Building": {lat: 31.395685, lng: 75.535524, connections: {"Central Seminar Hall": 1, "IT Building": 1, "Lib Walkway Front": 1, "IT Building walkway front": 1, "Administrative building back": 1}},
        "IT Building walkway front": {lat: 31.395419, lng: 75.535739, connections: {"Nescafe": 1, "Administrative Building": 1, "IT Building": 1}},
        "Lib Walkway Front": {lat: 31.395936, lng: 75.535300, connections: {"Administrative Building": 1, "Library Front": 1, "Central Seminar Hall": 1}},
        "Library Front": {lat: 31.396356, lng: 75.534933, connections: {"Library": 1, "Lib Walkway Front": 1, "Snackers Front": 1}},
        "Library": {lat: 31.396423, lng: 75.535269, connections: {"Library Front": 1}},
        "Nescafe": {lat: 31.394155, lng: 75.536844, connections: {"Guest House": 1, "IT Building walkway front": 1, "GH round about": 1}},
        "Snackers Front": {lat: 31.397119, lng: 75.534284, connections: {"Snackers": 1, "Department of physics and chemistry": 1, "Towards IT Building": 1}},
        "Snackers": {lat: 31.397006, lng: 75.534070, connections: {"Snackers Front": 1}},
        "Towards IT Building": {lat: 31.397493, lng: 75.534002, connections: {"Snackers Front": 1, "NC Front": 1, "BH roundabout": 1}},
        "Department of physics and chemistry": {lat: 31.397279, lng: 75.534542, connections: {"Snackers Front": 1}},
        "NC Front": {lat: 31.397809, lng: 75.534532, connections: {"Towards IT Building": 1, "Night Canteen": 1, "Manufacturing Workshop": 1}},
        "Night Canteen": {lat: 31.398025, lng: 75.534134, connections: {"NC Front": 1}},
        "BasketBall Court": {lat: 31.396707, lng: 75.533638, connections: {"Right Round About": 1, "BH roundabout": 1, "Snackers": 1}},
        "Manufacturing Workshop": {lat: 31.397916, lng: 75.534743, connections: {"NC Front": 1, "Department of Biotechnology": 1}},
        "Guest House": {lat: 31.393917, lng: 75.536361, connections: {"Nescafe": 1, "Left Round About": 1}},
        "Department of Biotechnology": {lat: 31.398279, lng: 75.535291, connections: {"MBH Round about": 1, "Manufacturing Workshop": 1}},
        "MBH Round about": {lat: 31.398524, lng: 75.535671, connections: {"Mega Boys Hostel": 1, "Lecture Theatre Back": 1, "Department of Biotechnology": 1}},
        "Mega Boys Hostel": {lat: 31.398947, lng: 75.535360, connections: {"MBH Round about": 1}},
        "BH roundabout": {lat: 31.397345, lng: 75.533764, connections: {"Towards IT Building": 1, "BasketBall Court": 1}},
        "Lecture Theatre Back": {lat: 31.397378, lng: 75.537286, connections: {"Lecture Theatre": 1, "MGH": 1}},
        "Lecture Theatre": {lat: 31.397120, lng: 75.536916, connections: {"Lecture Theatre Back": 1, "Lecture Theatre Front": 1}},
        "Lecture Theatre Front": {lat: 31.396628, lng: 75.536967, connections: {"Lecture Theatre": 1, "ECE Building": 1}},
        "ECE Building": {lat: 31.396158, lng: 75.536699, connections: {"Lecture Theatre Front": 1, "Civil Engineering": 1, "Administrative Building Back": 1}},
        "Civil Engineering": {lat: 31.395613, lng: 75.536841, connections: {"ECE Building": 1, "Chemical Engineering": 1}},
        "Chemical Engineering": {lat: 31.395397, lng: 75.537053, connections: {"Textile Engineering": 1, "Civil Engineering": 1}},
        "Textile Engineering": {lat: 31.395132, lng: 75.537257, connections: {"GH round about": 1, "Chemical Engineering": 1}},
        "Administrative Building Back": {lat: 31.396220, lng: 75.536376, connections: {"Administrative Building": 1, "ECE Building": 1}},
        "GH round about": {lat: 31.394590, lng: 75.537551, connections: {"Nescafe": 1, "Textile Engineering": 1, "MGH": 1}},
        "MGH": {lat: 31.395394, lng: 75.538868, connections: {"GH round about": 1, "Lecture Theatre Back": 1}},
        // ...additional locations if needed...
    };

    const displayLocations = [
    "Main Gate", "Open Air Theatre", "Central Seminar Hall", "IT Building",
    "Administrative Building", "Library", "Nescafe", "Snackers",
    "Department of physics and chemistry", "Night Canteen", "BasketBall Court",
    "Manufacturing Workshop", "Guest House", "Department of Biotechnology",
    "Mega Boys Hostel", "Lecture Theatre", "ECE Building", "Civil Engineering",
    "Chemical Engineering", "Textile Engineering", "MGH"
];

// Populate the dropdown with only the specified locations
const startSelect = document.getElementById('start');
const endSelect = document.getElementById('end');
displayLocations.forEach(location => {
    let optionStart = document.createElement('option');
    let optionEnd = document.createElement('option');
    optionStart.value = optionEnd.value = location;
    optionStart.textContent = optionEnd.textContent = location;
    startSelect.appendChild(optionStart);
    endSelect.appendChild(optionEnd);
});

let routePath = null;

function findRoute() {
    const start = startSelect.value;
    const end = endSelect.value;

    if (!start || !end || start === end) {
        alert("Please select two different locations.");
        return;
    }

    if (routePath) {
        map.removeLayer(routePath);
    }

    const path = dijkstra(start, end);

    const latlngs = path.map(location => [locations[location].lat, locations[location].lng]);
    
    routePath = L.polyline(latlngs, { color: 'blue', weight: 4, opacity: 0.7, smoothFactor: 1 }).addTo(map);
    map.fitBounds(routePath.getBounds());
}


        function dijkstra(start, end) {
            const distances = {};
            const previous = {};
            const queue = new Set(Object.keys(locations));

            for (let location in locations) {
                distances[location] = Infinity;
                previous[location] = null;
            }
            distances[start] = 0;

            while (queue.size > 0) {
                let current = [...queue].reduce((a, b) => distances[a] < distances[b] ? a : b);
                queue.delete(current);

                if (current === end) break;

                for (let neighbor in locations[current].connections) {
                    const alt = distances[current] + locations[current].connections[neighbor];
                    if (alt < distances[neighbor]) {
                        distances[neighbor] = alt;
                        previous[neighbor] = current;
                    }
                }
            }

            const path = [];
            let u = end;
            while (previous[u]) {
                path.unshift(u);
                u = previous[u];
            }
            path.unshift(start);
            return path;
        }
    </script>

    </body>
    </html>
