<?php
// Define database credentials. You must replace these with your actual credentials.
define('DB_HOST', 'localhost');
define('DB_NAME', 'l');
define('DB_USER', 'root');
define('DB_PASS', '');

/**
 * Connect to the database using PDO and fetch the data.
 * @return array
 */
function getPoliceStationsData()
{
    try {
        // Create a new PDO instance
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // SQL query to fetch police station data
        $sql = "SELECT police_station_code, police_station_en, officer_incharge, contact_number, contact_email,
                       latitude, longitude, district_en, zone_en, division_en, address
                FROM konkan_police_station";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stations = [];
        foreach ($results as $row) {
            $lat = cleanCoordinate($row['latitude']);
            $lng = cleanCoordinate($row['longitude']);

            if (!empty($lat) && !empty($lng)) {
                $stations[] = [
                    'code'           => $row['police_station_code'],
                    'lat'            => $lat,
                    'lng'            => $lng,
                    'station'        => $row['police_station_en'],
                    'district'       => $row['district_en'],
                    'zone'           => $row['zone_en'],
                    'division'       => $row['division_en'],
                    'address'        => $row['address'],
                    'contact_number' => $row['contact_number'],
                    'contact_email'  => $row['contact_email'],
                    'officer'        => $row['officer_incharge']
                ];
            }
        }
        return $stations;

    } catch (PDOException $e) {
        // In a real application, you would log this error and show a user-friendly message.
        // For this example, we'll just display it.
        die("Database connection failed: " . $e->getMessage());
    }
}

/**
 * Helper function to clean latitude/longitude coordinates.
 * @param string $coord
 * @return float|null
 */
function cleanCoordinate($coord)
{
    if (empty($coord)) {
        return null;
    }
    $coord = strtoupper(trim($coord));
    // Remove N, S, E, W
    $coord = str_replace(['N','S','E','W'], '', $coord);
    // Replace multiple dots with a single dot
    $coord = preg_replace('/\.+/', '.', $coord);
    // Convert to float
    return floatval($coord);
}

// Fetch the data from the database
$stations = getPoliceStationsData();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konkan Range Police Stations Map</title>
    <link rel="stylesheet" href="https://apis.mapmyindia.com/advancedmaps/v1/fa974f01fbe5ffa74497d39011bdb2ac/map_load?v=1.3">
    <style>
        /* Shared styles */
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden; /* Prevents scrollbars on the main body */
            background-color: #f4f4f4;
        }

        #dashboard-container {
            position: relative;
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px;
            box-sizing: border-box;
        }

        #map-container {
            position: relative;
            height: 70vh;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }

        /* Sidebar styles for the navigation drawer effect */
        #sidebar {
            position: absolute;
            top: 20px;
            right: -350px; /* Initially off-screen to the right */
            height: auto;
            max-height: calc(70vh - 40px);
            width: 350px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2); /* Stronger shadow for "pop-out" effect */
            transition: right 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94); /* Smooth cubic-bezier transition */
            z-index: 1000;
            overflow-y: auto;
            border-radius: 10px;
        }
        #sidebar.active {
            right: 20px; /* Slides into view */
        }

        /* Sidebar content and close button styles */
        #close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 28px;
            cursor: pointer;
            color: #fff;
            background: none;
            border: none;
            z-index: 1001;
            transition: color 0.2s;
        }
        #close-btn:hover {
            color: #ccc;
        }

        .sidebar-content h3 {
            background-image: linear-gradient(to right, #4a90e2, #5a67d8);
            color: white;
            padding: 15px 20px;
            margin: -20px -20px 20px -20px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            font-size: 1.75rem; /* Increased font size for prominence */
            font-weight: bold; /* Ensures the text is bold */
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
            text-align: center;
        }

        .detail-item {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .detail-item:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
            display: block;
        }
        .value {
            color: #333;
        }

        .close-button {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
            transition: background-color 0.2s;
        }
        .close-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div id="dashboard-container">
        <h2 style="font-size: 1.5rem; font-weight: 600; color: #1f2937; margin-bottom: 1rem;">Konkan Range Police Stations Map</h2>
        <div id="map-container">
            <div id="sidebar">
                <button id="close-btn">&times;</button>
                <div id="sidebar-content"></div>
            </div>
        </div>
    </div>

    <script src="https://apis.mapmyindia.com/advancedmaps/v1/fa974f01fbe5ffa74497d39011bdb2ac/map_load?v=1.3"></script>
    <script>
        window.onload = function() {
            // Retrieve police stations data passed from the PHP script.
            // json_encode() is used to securely pass the PHP array to JavaScript.
            const stationsData = <?php echo json_encode($stations); ?>;
            
            // Get sidebar and map container elements
            const sidebar = document.getElementById('sidebar');
            const sidebarContent = document.getElementById('sidebar-content');
            const closeBtn = document.getElementById('close-btn');
            const mapContainer = document.getElementById('map-container');

            // Initialize the MapmyIndia map
            const map = new MapmyIndia.Map("map-container", {
                center: [18.5204, 73.8567],
                zoom: 8,
                zoomControl: true,
                hybrid: true
            });
            
            // Disable scroll wheel zoom by default to allow page scrolling
            map.scrollWheelZoom.disable();
            
            // Add a click event listener to the map container to enable zoom
            let isMapActive = false;
            mapContainer.addEventListener('click', () => {
                if (!isMapActive) {
                    map.scrollWheelZoom.enable();
                    isMapActive = true;
                    console.log('Scroll zoom enabled on map.');
                }
            });

            // Add a mouseleave event listener to the map container to disable zoom
            mapContainer.addEventListener('mouseleave', () => {
                if (isMapActive) {
                    map.scrollWheelZoom.disable();
                    isMapActive = false;
                    console.log('Scroll zoom disabled on map.');
                }
            });

            // Stop scroll from bubbling up from the sidebar
            sidebar.addEventListener('wheel', (e) => {
                e.stopPropagation();
            });

            // Custom icon
            const stationIcon = L.icon({
                // Note: In this standalone version, you'll need to provide the correct path to your image file.
                // This assumes 'police-station.png' is in the same directory as this PHP file.
                iconUrl: 'police-station.png',
                iconSize: [40, 40],
                iconAnchor: [20, 40],
                popupAnchor: [0, -40]
            });

            stationsData.forEach(station => {
                const marker = L.marker([station.lat, station.lng], { icon: stationIcon }).addTo(map);
                
                // Add a click event listener to each marker
                marker.on('click', () => {
                    // Populate sidebar with police station info
                    sidebarContent.innerHTML = `
                        <h3 style="font-weight: bold; font-size: 1rem;">Station Details</h3><br/>
                        <div class="detail-item">
                            <span class="label">Police Station:</span>
                            <div class="value">${station.station}</div>
                        </div>
                        <div class="detail-item">
                            <span class="label">District:</span>
                            <div class="value">${station.district}</div>
                        </div>
                        <div class="detail-item">
                            <span class="label">Zone:</span>
                            <div class="value">${station.zone}</div>
                        </div>
                        <div class="detail-item">
                            <span class="label">Division:</span>
                            <div class="value">${station.division}</div>
                        </div>
                        <div class="detail-item">
                            <span class="label">Address:</span>
                            <div class="value">${station.address}</div>
                        </div>
                        <div class="detail-item">
                            <span class="label">Contact Number:</span>
                            <div class="value">${station.contact_number}</div>
                        </div>
                        <div class="detail-item">
                            <span class="label">Email:</span>
                            <div class="value">${station.contact_email}</div>
                        </div>
                        <div class="detail-item">
                            <span class="label">Officer In-Charge:</span>
                            <div class="value">${station.officer}</div>
                        </div>
                        <button id="show-trends-btn" class="close-button">Show Crime Trends</button>
                    `;
                    
                    // Display the sidebar by adding the active class
                    sidebar.classList.add('active');
                    
                    // Attach the station data to the button for later use
                    document.getElementById('show-trends-btn').dataset.stationId = station.code;
                });
            });

            // Handle closing the sidebar
            closeBtn.onclick = function() {
                sidebar.classList.remove('active');
            };
        };
    </script>
</body>
</html>
