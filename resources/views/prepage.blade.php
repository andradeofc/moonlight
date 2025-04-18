<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Continue to Site</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
        }
        .flex {
            display: flex;
            justify-content: center;
            width: 100%;
        }
        .container {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 400px;
            text-align: center;
        }
        .like {
            width: 60px;
            height: 60px;
            margin-bottom: 20px;
        }
        .copy {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .alt {
            color: #666;
            margin-bottom: 25px;
        }
        label {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            cursor: pointer;
        }
        input[type="checkbox"] {
            margin-right: 10px;
            width: 18px;
            height: 18px;
        }
        .label-text {
            font-size: 16px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="flex">
        <div class="container">
            <svg class="like" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#4CAF50">
                <path d="M12 22C6.48 22 2 17.52 2 12S6.48 2 12 2s10 4.48 10 10-4.48 10-10 10zm-1-9v4h2v-4h3l-4-4-4 4h3z"/>
            </svg>
            <p class="copy">
                All set!
            </p>
            <p class="alt">
                Check <strong>"I'm not a robot"</strong> and click continue
                to be redirected to the page.
            </p>
            <label>
                <input type="checkbox" name="check" id="check"> 
                <span class="label-text">I'm not a robot</span>
            </label>
            <form method="POST" action="{{ url()->current() }}">
    @csrf
    <input type="hidden" name="url" value="{{ $encoded_url }}">
    <button type="submit" id="redirect" style="display: none;">Continue</button>
</form>


        </div>
    </div>

    <script>
        document.getElementById('check').addEventListener('change', function() {
            document.getElementById('redirect').style.display = this.checked ? 'inline-block' : 'none';
        });
    </script>
</body>
</html>