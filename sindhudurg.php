<?php
// Define dashboard data for Sindhudurg district.
$dashboardData = [
    'totalRecords'     => '589',
    'detectionRate'    => '82%',
    'yoyChange'        => '+2.61%',
    'policeStations'   => '14'
];

// Define database credentials.
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'kokan renge');
define('DB_USER', 'root');
define('DB_PASS', '8TyUN=cV[-Xy.ERB$H}|');

/**
 * Connect to the database using PDO and fetch the data.
 * @return array
 */
function getPoliceStationsData()
{
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // SQL query filtered for Sindhudurg district
        $sql = "SELECT police_station_code, police_station_en, officer_incharge, contact_number, contact_email,
                       latitude, longitude, district_en, zone_en, division_en, address
                FROM konkan_police_station
                WHERE district_en = 'Sindhudurg'";
        
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
    $coord = str_replace(['N','S','E','W'], '', $coord);
    $coord = preg_replace('/\.+/', '.', $coord);
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
    <title>Sindhudurg Police Stations Dashboard & Map</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://apis.mapmyindia.com/advancedmaps/v1/fa974f01fbe5ffa74497d39011bdb2ac/map_load?v=1.3">
    <style>
      .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }
        .card {
            background-color: #fff;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 1rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            transition: transform 0.2s, box-shadow 0.2s;
            text-align: center;
            border-left-width: 5px;
            border-left-style: solid;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .card.blue-border {
            border-color: #3b82f6;
        }
        .card.green-border {
            border-color: #22c55e;
        }
        .card.orange-border {
            border-color: #f97316;
        }
        .card.purple-border {
            border-color: #a855f7;
        }
        .icon {
            font-size: 2rem;
            margin-bottom: 0.25rem;
        }
        .card-title {
            font-size: 0.9rem;
            font-weight: 500;
            color: #6b7280;
        }
        .card-value {
            font-size: 2rem;
            font-weight: 700;
            margin-top: 0.25rem;
        }
        .card-description {
            font-size: 0.75rem;
            color: #9ca3af;
        }
        /* Shared styles */
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: auto;
            background-color: #f4f4f4;
        }
        
        #main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px;
        }

        #map-container {
            position: relative;
            height: 50vh;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            margin-top: 2rem;
        }

        #sidebar {
            position: absolute;
            top: 20px;
            right: -350px;
            height: auto;
            max-height: calc(50vh - 40px);
            width: 350px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            transition: right 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            z-index: 1000;
            overflow-y: auto;
            border-radius: 10px;
        }
        #sidebar.active {
            right: 20px;
        }

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
            font-size: 1.75rem;
            font-weight: bold;
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
<body class="p-4 sm:p-8">
    <div id="main-container">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl sm:text-4xl font-bold mb-6 sm:mb-8 text-slate-800 text-center">Sindhudurg District Crime Records Dashboard</h1>
            <div class="dashboard-grid">
                <div class="card blue-border">
                    <div class="text-blue-500 icon"><i class="fas fa-file-alt"></i></div>
                    <div class="card-title">Total Records</div>
                    <div class="card-value text-blue-700" data-target="<?= $dashboardData['totalRecords'] ?>">0</div>
                    <div class="card-description">All records processed in Sindhudurg</div>
                </div>
                <div class="card green-border">
                    <div class="text-green-500 icon"><i class="fas fa-search"></i></div>
                    <div class="card-title">Detection Rate</div>
                    <div class="card-value text-green-700" data-target="<?= $dashboardData['detectionRate'] ?>">0%</div>
                    <div class="card-description">Overall detection success</div>
                </div>
                <div class="card orange-border">
                    <div class="text-orange-500 icon"><i class="fas fa-chart-line"></i></div>
                    <div class="card-title">YoY Change</div>
                    <div class="card-value text-orange-700" data-target="<?= $dashboardData['yoyChange'] ?>">0</div>
                    <div class="card-description">2024 vs 2025</div>
                </div>
                <div class="card purple-border">
                    <div class="text-purple-500 icon"><i class="fas fa-building"></i></div>
                    <div class="card-title">Police Stations</div>
                    <div class="card-value text-purple-700" data-target="<?= $dashboardData['policeStations'] ?>">0</div>
                    <div class="card-description">Total stations in the district</div>
                </div>
            </div>
        </div>

        <div id="map-container">
            <div id="sidebar">
                <button id="close-btn">&times;</button>
                <div id="sidebar-content"></div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const counters = document.querySelectorAll('.card-value');
            counters.forEach(counter => {
                const targetText = counter.getAttribute('data-target');
                const target = parseFloat(targetText.replace('%', ''));
                const isPercentage = targetText.includes('%');
                const isNegative = target < 0;
                const duration = 1500;
                let startTimestamp = null;
                const formatNumber = (num) => {
                    let formatted = parseFloat(num).toFixed(isNegative || num % 1 !== 0 ? 1 : 0);
                    if (isPercentage) return `${formatted}%`;
                    if (Number.isInteger(parseFloat(num))) return num.toLocaleString();
                    return formatted;
                };
                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = timestamp - startTimestamp;
                    let value = progress / duration * target;
                    if (isNegative) value = Math.max(value, target);
                    else value = Math.min(value, target);
                    counter.innerText = formatNumber(value);
                    if (progress < duration) {
                        window.requestAnimationFrame(step);
                    } else {
                        counter.innerText = formatNumber(target);
                    }
                };
                window.requestAnimationFrame(step);
            });
        });
    </script>
    <script src="https://apis.mapmyindia.com/advancedmaps/v1/fa974f01fbe5ffa74497d39011bdb2ac/map_load?v=1.3"></script>
    <script>
        window.onload = function() {
            const stationsData = <?php echo json_encode($stations); ?>;
            const sidebar = document.getElementById('sidebar');
            const sidebarContent = document.getElementById('sidebar-content');
            const closeBtn = document.getElementById('close-btn');
            const mapContainer = document.getElementById('map-container');

            const map = new MapmyIndia.Map("map-container", {
                center: [16.0354, 73.6558],
                zoom: 9,
                zoomControl: true,
                hybrid: true
            });
            
            map.scrollWheelZoom.disable();
            
            let isMapActive = false;
            mapContainer.addEventListener('click', () => {
                if (!isMapActive) {
                    map.scrollWheelZoom.enable();
                    isMapActive = true;
                }
            });

            mapContainer.addEventListener('mouseleave', () => {
                if (isMapActive) {
                    map.scrollWheelZoom.disable();
                    isMapActive = false;
                }
            });

            sidebar.addEventListener('wheel', (e) => {
                e.stopPropagation();
            });

            const stationIcon = L.icon({
                iconUrl: 'police-station.png',
                iconSize: [40, 40],
                iconAnchor: [20, 40],
                popupAnchor: [0, -40]
            });

            stationsData.forEach(station => {
                const marker = L.marker([station.lat, station.lng], { icon: stationIcon }).addTo(map);
                marker.on('click', () => {
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
                    sidebar.classList.add('active');
                    document.getElementById('show-trends-btn').dataset.stationId = station.code;
                });
            });

            closeBtn.onclick = function() {
                sidebar.classList.remove('active');
            };
        };
    </script>
</body>
</html>