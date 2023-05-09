<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>test - live search</title>
    <style>
        body {
            background: #262a2f;
            color: #333;
        }

        .search-box {
            width: 600px;
            background: #fff;
            margin: 200px auto 0;
            border-radius: 5px;
        }

        .row {
            display: flex;
            align-items: center;
            padding: 10px 20px;
        }

        .search-box .row .input {
            flex: 1;
            height: 50px;
            background: transparent;
            border: 0;
            outline: 0;
            font-size: 18px;
            color: #333;
        }

        .search-box button {
            background: transparent;
            border: 0;
            outline: 0;

        }

        .search-box button .fa-solid {
            width: 25px;
            color: #555;
            font-size: 22px;
            cursor: pointer;
        }

        ::placeholder {
            color: #555;
        }

        .result-box ul {
            border-top: 1px solid #999;
            padding: 15px 10px;
        }

        .result-box ul li {
            list-style: none;
            border-radius: 3px;
            padding: 15px 10px;
            cursor: pointer;
        }

        .result-box ul li:hover {
            background: #e9f3ff;
        }

        .result-box {
            max-height: 300px;
            overflow-y: scroll;
        }
    </style>
    <script src="https://kit.fontawesome.com/c4254e24a8.js" crossorigin="anonymous"></script>
</head>

<body>
    <div class="search-box">
        <div class="row">
            <input type="text" id="input-box" placeholder="Search" autocomplete="off">
            <button>
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>
        <div class="result-box">

        </div>
    </div>
    <script>
        let list = ['html', 'css', 'java']
        const resultsBox = document.querySelector(".result-box");
        const inputBox = document.getElementById("input-box");

        inputBox.onkeyup = function() {
            let result = [];
            let input = inputBox.value;
            if (input.length) {
                result = list.filter((keyword) => {
                    return keyword.toLowerCase().includes(input.toLowerCase());
                });
                console.log(result);
            }
            display(result);
            if (!result.length) {
                resultsBox.innerHTML = '';
            }
        }

        function display(result) {
            const content = result.map((list1) => {
                return "<li onClick = selectInput(this)>" + list1 + "</li>";
            });
            resultsBox.innerHTML = "<ul>" + content.join('') + "</ul>";
        }

        function selectInput(list1) {
            inputBox.value = list1.innerHTML;
            resultsBox.innerHTML = '';
        }
    </script>
</body>

</html>