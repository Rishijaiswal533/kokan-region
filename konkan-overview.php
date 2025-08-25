<?php
// Dashboard data reflects the real figures from the provided images.
// Commas are removed for JavaScript number animation.
$dashboardData = [
    'totalRecords'     => '5602',
    'detectionRate'    => '85 %',
    // Calculated from the provided data: (5602 - 5608) / 5608 * 100
    'yoyChange'        => '-0.1',
    // This value is not in the image, so it remains as previously requested.
    'policeStations'   => '90'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crime Records Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        /* Custom styles to match the requested design */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); /* Reduced minmax for smaller grid items */
            gap: 1.5rem;
        }
        .card {
            background-color: #fff;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 1rem; /* Reduced padding for smaller height */
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
            font-size: 2rem; /* Reduced icon size */
            margin-bottom: 0.25rem; /* Reduced margin */
        }
        .card-title {
            font-size: 0.9rem; /* Reduced font size */
            font-weight: 500;
            color: #6b7280;
        }
        .card-value {
            font-size: 2rem; /* Reduced font size */
            font-weight: 700;
            margin-top: 0.25rem;
        }
        .card-description {
            font-size: 0.75rem; /* Reduced font size */
            color: #9ca3af;
        }
    </style>
</head>
<body class="p-4 sm:p-8">

    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl sm:text-4xl font-bold mb-6 sm:mb-8 text-slate-800 text-center">Crime Records Dashboard</h1>

        <div class="dashboard-grid">
            <div class="card blue-border">
                <div class="text-blue-500 icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="card-title">Total Records</div>
                <div class="card-value text-blue-700" data-target="<?= $dashboardData['totalRecords'] ?>">0</div>
                <div class="card-description">All records processed in Konkan Renge</div>
            </div>

            <div class="card green-border">
                <div class="text-green-500 icon">
                    <i class="fas fa-search"></i>
                </div>
                <div class="card-title">Detection Rate</div>
                <div class="card-value text-green-700" data-target="<?= $dashboardData['detectionRate'] ?>">0</div>
                <div class="card-description">Overall detection success</div>
            </div>

            <div class="card orange-border">
                <div class="text-orange-500 icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="card-title">YoY Change</div>
                <div class="card-value text-orange-700" data-target="<?= $dashboardData['yoyChange'] ?>">0</div>
                <div class="card-description">2024 vs 2025</div>
            </div>

            <div class="card purple-border">
                <div class="text-purple-500 icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="card-title">Police Stations</div>
                <div class="card-value text-purple-700" data-target="<?= $dashboardData['policeStations'] ?>">0</div>
                <div class="card-description">Total stations in the renge</div>
            </div>
        </div>
    </div>

    <script>
        // JavaScript for the counting animation effect
        document.addEventListener("DOMContentLoaded", () => {
            const counters = document.querySelectorAll('.card-value');

            counters.forEach(counter => {
                const target = parseFloat(counter.getAttribute('data-target'));
                const isPercentage = counter.getAttribute('data-target').includes('%');
                const isNegative = target < 0;
                
                const duration = 1500; // Animation duration in milliseconds
                let startTimestamp = null;
                
                const formatNumber = (num) => {
                    let formatted = parseFloat(num).toFixed(isNegative ? 1 : 0);
                    if (isPercentage) {
                        return `${formatted}%`;
                    }
                    if (Number.isInteger(parseFloat(num))) {
                        return num.toLocaleString();
                    }
                    return formatted;
                };

                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = timestamp - startTimestamp;
                    let value = progress / duration * target;

                    if (isNegative) {
                        // For negative numbers, animate towards the negative target
                        value = Math.max(value, target);
                    } else {
                        // For positive numbers, animate up
                        value = Math.min(value, target);
                    }
                    
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
</body>
</html>