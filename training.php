<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <title>training</title>
    <style>
        .navbar {
            display: none !important;
        }
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .container_training {
            display: flex;
            flex-direction: row;
            width: 100%;
            height: 100vh;
            padding: 20px;
            box-sizing: border-box;
        }

        .left,
        .right {
            flex: 1;
            display: flex;
            flex-direction: column;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .left {
            margin-right: 10px;
        }

        .right {
            margin-left: 10px;
        }

        .left textarea,
        .right iframe {
            flex: 1;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            font-size: 16px;
        }

        .left label,
        .right label {
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
        }

        .back button {
            background-color: #7BD3EA; /* Updated color */
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .back button:hover {
            background-color: #A1EEBD; /* Updated color */
        }

        /* Dark mode styles */
        body.dark-mode {
            background-color: #1a1a1a;
            color: #f4f4f4;
        }

        body.dark-mode .left,
        body.dark-mode .right {
            background-color: #2a2a2a;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
        }

        body.dark-mode .left textarea,
        body.dark-mode .right iframe {
            background-color: #333;
            color: #f4f4f4;
            border-color: #555;
        }

        body.dark-mode .left label,
        body.dark-mode .right label {
            color: #f4f4f4;
        }

        body.dark-mode .back button {
            background-color: #555;
        }

        body.dark-mode .back button:hover {
            background-color: #777;
        }

        /* Dark mode switch styles */
        .dark-mode-switch {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #2196F3;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }
    </style>
</head>

<body>
    <div>
        <a href="home.php" class="back"><button><i class="fa-sharp fa-solid fa-arrow-left"></i> back to homepage</button></a>
    </div>
    
    <!-- Dark mode switch -->
    <div class="dark-mode-switch">
        <label class="switch">
            <input type="checkbox" id="darkModeToggle">
            <span class="slider"></span>
        </label>
    </div>

    <div class="container_training">
        <div class="left">
            <label><i class="fa-brands fa-html5"></i>HTML</label>
            <textarea id="html-code" onkeyup="run()"></textarea>

            <label><i class="fa-brands fa-css3-alt"></i>CSS</label>
            <textarea id="css-code" onkeyup="run()"></textarea>

            <label><i class=" fa-brands fa-js"></i>JavaScript</label>
            <textarea id="js-code" onkeyup="run()"></textarea>
        </div>
        <div class=" right">
            <label><i class="fa-solid fa-play">Output</i></label>
            <iframe id="output"></iframe>
        </div>
    </div>

    <script>
        function run() {
            let htmlCode = document.getElementById("html-code").value;
            let cssCode = document.getElementById("css-code").value;
            let jsCode = document.getElementById("js-code").value;
            let output = document.getElementById("output");

            output.contentDocument.body.innerHTML = htmlCode + "<style>" + cssCode + "</style>";
            output.contentWindow.eval(jsCode);
        }

        // Toggle dark mode
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            if (document.body.classList.contains('dark-mode')) {
                localStorage.setItem('darkMode', 'enabled');
            } else {
                localStorage.setItem('darkMode', 'disabled');
            }
        }

        // Check for dark mode preference
        function checkDarkMode() {
            if (localStorage.getItem('darkMode') === 'enabled') {
                document.body.classList.add('dark-mode');
                document.getElementById('darkModeToggle').checked = true;
            }
        }

        // Run dark mode check on page load
        checkDarkMode();

        // Add event listener to dark mode toggle
        document.getElementById('darkModeToggle').addEventListener('change', toggleDarkMode);

        // Listen for dark mode changes from other pages
        window.addEventListener('storage', function(e) {
            if (e.key === 'darkMode') {
                if (e.newValue === 'enabled') {
                    document.body.classList.add('dark-mode');
                    document.getElementById('darkModeToggle').checked = true;
                } else {
                    document.body.classList.remove('dark-mode');
                    document.getElementById('darkModeToggle').checked = false;
                }
            }
        });
    </script>

</body>

</html>