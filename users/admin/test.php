<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuzina Nutritional Assessment</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .assessment-container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
        }
        .assessment-container h2 {
            text-align: center;
            color: #4CAF50;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
        }
        .card {
            background: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: transform 0.3s ease;
            flex: 1 1 calc(33.333% - 20px);
            max-width: 150px;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }
        .card i {
            font-size: 36px;
            margin-bottom: 10px;
            color: #4CAF50;
        }
        .card p {
            margin: 0;
            font-weight: bold;
        }
        .form-group input[type="submit"] {
            width: 100%;
            padding: 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 20px;
        }
        .form-group input[type="submit"]:hover {
            background-color: #45a049;
        }
        input[type="hidden"] {
            display: none;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                card.addEventListener('click', function() {
                    const input = document.getElementById(this.dataset.input);
                    input.value = this.dataset.value;
                    cards.forEach(c => c.classList.remove('selected'));
                    this.classList.add('selected');
                });
            });
        });
    </script>
</head>
<body>
    <div class="assessment-container">
        <h2>Nutritional Assessment</h2>
        <form action="/submit-assessment" method="POST">
            <!-- Diet Type Selection -->
            <div class="form-group">
                <label for="diet-type">Select Your Diet Type:</label>
                <div class="card-container">
                    <div class="card" data-input="diet-type" data-value="Vegetarian">
                        <i class="fas fa-leaf"></i>
                        <p>Vegetarian</p>
                    </div>
                    <div class="card" data-input="diet-type" data-value="Vegan">
                        <i class="fas fa-carrot"></i>
                        <p>Vegan</p>
                    </div>
                    <div class="card" data-input="diet-type" data-value="Keto">
                        <i class="fas fa-drumstick-bite"></i>
                        <p>Keto</p>
                    </div>
                    <div class="card" data-input="diet-type" data-value="Balanced Diet">
                        <i class="fas fa-apple-alt"></i>
                        <p>Balanced Diet</p>
                    </div>
                </div>
                <input type="hidden" id="diet-type" name="diet_type">
            </div>
            <!-- Health Goals Selection -->
            <div class="form-group">
                <label for="health-goals">Select Your Health Goals:</label>
                <div class="card-container">
                    <div class="card" data-input="health-goals" data-value="Weight Loss">
                        <i class="fas fa-weight"></i>
                        <p>Weight Loss</p>
                    </div>
                    <div class="card" data-input="health-goals" data-value="Muscle Gain">
                        <i class="fas fa-dumbbell"></i>
                        <p>Muscle Gain</p>
                    </div>
                    <div class="card" data-input="health-goals" data-value="Improve Energy Levels">
                        <i class="fas fa-bolt"></i>
                        <p>Improve Energy</p>
                    </div>
                    <div class="card" data-input="health-goals" data-value="Better Digestion">
                        <i class="fas fa-stomach"></i>
                        <p>Better Digestion</p>
                    </div>
                </div>
                <input type="hidden" id="health-goals" name="health_goals">
            </div>
            <div class="form-group">
                <input type="submit" value="Submit">
            </div>
        </form>
    </div>
</body>
</html>